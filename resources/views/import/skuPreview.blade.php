<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SKU Import Preview</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
       <style>
        /* Table row highlighting */
        .error-row {
            background-color: #fef2f2 !important;
            border-left: 4px solid #ef4444;
        }
        
        .error-row:hover {
            background-color: #fee2e2 !important;
        }
        
        .warning-row {
            background-color: #fffbeb !important;
            border-left: 4px solid #f59e0b;
        }
        
        .warning-row:hover {
            background-color: #fef3c7 !important;
        }
        
        .success-row {
            background-color: #f0fdf4 !important;
            border-left: 4px solid #10b981;
        }
        
        .success-row:hover {
            background-color: #dcfce7 !important;
        }
        
        /* Fixed header table */
        .table-container {
            min-height: 40vh;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .sticky-header th {
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        /* Scrollbar styling */
        .table-container::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .table-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        .table-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Error badge */
        .error-badge {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }
        
        /* Status indicators */
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }
        
        .status-error { background-color: #ef4444; }
        .status-warning { background-color: #f59e0b; }
        .status-success { background-color: #10b981; }
        
        /* Line number styling */
        .line-number {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #6b7280;
        }

          /* Add these styles to your CSS */
    .table-container {
        position: relative;
        width: 100%;
    }
    
      .tooltip-cell {
            position: relative;
            cursor: default;
            /* Do NOT use overflow: hidden here */
        }

        /* 3. The Text Wrapper - Handles the ellipsis (...) */
        .truncate-text {
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }

        /* 4. The Tooltip - ONLY for truncated cells */
        .tooltip-cell.truncated::after {
            content: attr(data-tooltip);
            position: absolute;
            /* Position exactly below the cell */
            top: 100%;
            left: 0;
            
            /* Ensure it is on top of EVERYTHING */
            z-index: 9999;
            
            /* Styling */
            background-color: #1f2937;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: normal;
            
            /* Wrapping Logic */
            width: max-content;
            max-width: 400px;
            white-space: normal;
            word-wrap: break-word;
            
            /* Animation/Visibility */
            display: none;
            pointer-events: none;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
                transform: translateY(-50px); /* 👈 move tooltip UP */

        }

        /* 5. Triggering visibility */
        .tooltip-cell.truncated:hover::after {
            display: block;
        }

        /* 6. Change cursor only for truncated cells */
        .tooltip-cell.truncated {
            cursor: pointer;
        }

    
    /* Ensure table cells don't wrap */
    table {
        table-layout: auto;
        min-width: 1200px; /* Minimum width before scrolling */
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table-container {
            border-radius: 0;
        }
        
        .table-container > div {
            margin-left: -1rem;
            margin-right: -1rem;
            width: calc(100% + 2rem);
        }
    }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    @include('layout.sidebar')
    <main class="container mx-auto px-4 py-6">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <div class="flex items-center mb-2">
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            SKU Import Preview
                        </h1>
                        <span class="ml-4 px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Preview Mode
                        </span>
                    </div>
                    <!-- <p class="text-gray-600">Review imported items before finalizing. <span class="font-medium text-red-600">Errors must be fixed before proceeding.</span></p> -->
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3">
                    <a href="/sku-master/import" class="inline-flex items-center px-5 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-all duration-300">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Import
                    </a>
                    
                    <!-- <button id="downloadErrorsBtn" class="inline-flex items-center px-5 py-3 bg-red-100 text-red-700 rounded-xl font-medium hover:bg-red-200 transition-all duration-300">
                        <i class="fas fa-download mr-2"></i>
                        Download Errors
                    </button> -->
                    
                    <button id="proceedBtn" class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-medium hover:from-green-600 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-check-circle mr-2"></i>
                        Proceed with Import
                    </button>
                </div>
            </div>
            
         
            
        </div>

        <!-- Preview Table Container -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200/80 overflow-hidden">
            <!-- Table Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">SKU Imported Preview</h2>
                        <!-- <p class="text-gray-600 text-sm mt-1">Showing <span class="font-medium text-indigo-600">1,250</span> items from import file</p> -->
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <span class="status-dot status-success"></span>
                                <span class="text-sm text-gray-600">Valid</span>
                            </div>
                            <!-- <div class="flex items-center">
                                <span class="status-dot status-warning"></span>
                                <span class="text-sm text-gray-600">Warning</span>
                            </div> -->
                            <div class="flex items-center">
                                <span class="status-dot status-error"></span>
                                <span class="text-sm text-gray-600">Error</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Table -->
            <div class="table-container">
    <table class="w-full table-fixed border-separate border-spacing-0">
                    <thead class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white ">
                        <tr>
                            <th class="p-4 text-left font-semibold w-20">Line #</th>
                            <th class="p-4 text-left font-semibold">Status</th>
                            <th class="p-4 text-left font-semibold">Item_Code</th>
                            <th class="p-4 text-left font-semibold">Size_Name</th>
                            <th class="p-4 text-left font-semibold">Color_Name</th>
                            <th class="p-4 text-left font-semibold">Size_Code</th>
                            <th class="p-4 text-left font-semibold">Color_Code</th>
                            <th class="p-4 text-left font-semibold">JanCode</th>
                            <th class="p-4 text-right font-semibold">Quantity</th>
                            <th class="p-4 text-left font-semibold w-80">Error Message</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="skuPreviewBody">
                        <!-- Row 1 - Error Example -->
                        
                    </tbody>
                </table>
            </div>
            
               <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 mt-4">
    <div class="flex flex-1 justify-between sm:hidden">
        <button id="prevBtnMobile" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</button>
        <button id="nextBtnMobile" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</button>
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-700">
                Showing <span id="startRange" class="font-medium">0</span> to <span id="endRange" class="font-medium">0</span> of <span id="totalResults" class="font-medium">0</span> results
            </p>
        </div>
        <div>
            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination" id="skuPaginationNav">
                </nav>
        </div>
    </div>
</div>
         
        </div>
            <input type="hidden" id="importType" value="2">

        </div>

        
       
        </div>
    </main>

    <!-- JavaScript -->
             <script src="{{ asset('js/validation/import-validation.js') }}?v={{ time() }}"></script>

    <script>
   
            
            // Search functionality
           
            
            // Row click to show details
            
            
            // Pagination buttons
           
       
    const previewData = JSON.parse(sessionStorage.getItem("skuPreviewData") || "[]");
    function renderSKUTable(previewData) {
    const tbody = document.getElementById("skuPreviewBody");

    if (!previewData.length) {
        tbody.innerHTML = `
            <tr><td colspan="10" class="text-center py-6 text-gray-500">No SKU preview data found.</td></tr>
        `;
        return;
    }

    tbody.innerHTML = previewData.map(row => {
        let statusBadge = "";
        let rowClass = "";

        if (row.status === "Error") {
            statusBadge = `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    <i class="fas fa-times-circle mr-1"></i> Error
                </span>`;
            rowClass = "error-row hover:bg-red-50";
        } else if (row.status === "Warning") {
            statusBadge = `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Warning
                </span>`;
            rowClass = "warning-row hover:bg-yellow-50";
        } else {
            statusBadge = `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <i class="fas fa-check-circle mr-1"></i> Valid
                </span>`;
            rowClass = "hover:bg-green-50";
        }

        return `
        <tr class="${rowClass} transition-all duration-200">
    <!-- Line # -->
    <td class="p-4 font-mono">
        <span class="line-number">${row.lineNo || "_"}</span>
    </td>

    <!-- Status -->
    <td class="p-4">
        ${statusBadge}
    </td>

   <!-- Item_Code -->
<td class="p-4 font-mono font-medium text-purple-600 w-40"
    title="${row.Item_Code || '-'}">
    <span class="truncate-text block whitespace-nowrap overflow-hidden text-ellipsis">
        ${row.Item_Code || "-"}
    </span>
</td>

<!-- Size Name -->
<td class="p-4 w-40">
    <span class="truncate-text size-indicator bg-blue-100 text-blue-800 px-2 py-1 rounded inline-block max-w-full whitespace-nowrap overflow-hidden text-ellipsis"
          title="${row.SizeName || '-'}">
        ${row.SizeName || "-"}
    </span>
</td>

<!-- Color Name -->
<td class="p-4 w-48">
    <div class="flex items-center">
        <span class="color-indicator"
              style="background-color: #${row.ColorCode || 'ccc'}"></span>
        <span class="ml-2 truncate-text whitespace-nowrap overflow-hidden text-ellipsis"
              title="${row.ColorName || '-'}">
            ${row.ColorName || "-"}
        </span>
    </div>
</td>

<!-- Size Code -->
<td class="p-4 w-32">
    <span class="truncate-text code-highlight whitespace-nowrap overflow-hidden text-ellipsis block"
          title="${row.SizeCode || '-'}">
        ${row.SizeCode || "-"}
    </span>
</td>

<!-- Color Code -->
<td class="p-4 w-32">
    <span class="truncate-text code-highlight whitespace-nowrap overflow-hidden text-ellipsis block"
          title="${row.ColorCode || '-'}">
        ${row.ColorCode || "-"}
    </span>
</td>

<!-- JanCD -->
<td class=" font-mono w-36 min-w-[140px]">
    <span class="truncate-text whitespace-nowrap overflow-hidden text-ellipsis block"
          title="${row.JanCD || '-'}">
        ${row.JanCD || "-"}
    </span>
</td>

<!-- Quantity -->
<td class="p-4 w-28">
    <span class="truncate-text quantity-normal whitespace-nowrap overflow-hidden text-ellipsis block text-right"
          title="${row.Quantity || '-'}">
        ${row.Quantity || "-"}
    </span>
</td>

<!-- Errors / Warnings -->
<td class="p-4">
    <div class="space-y-1 max-h-20 overflow-y-auto"
         title="${row.errors.concat(row.warnings).join('\n') || 'No issues'}">
        ${
            row.errors.length > 0
                ? row.errors.map(err =>
                    `<div class="text-sm text-red-600 flex items-start">
                        <i class="fas fa-times-circle mr-2 mt-0.5 flex-shrink-0"></i>
                        <span class="truncate-text whitespace-nowrap overflow-hidden text-ellipsis"
                              title="${err}">
                            ${err}
                        </span>
                    </div>`
                  ).join("")
                : row.warnings.length > 0
                    ? row.warnings.map(warn =>
                        `<div class="text-sm text-yellow-600 flex items-start">
                            <i class="fas fa-exclamation-triangle mr-2 mt-0.5 flex-shrink-0"></i>
                            <span class="truncate-text whitespace-nowrap overflow-hidden text-ellipsis"
                                  title="${warn}">
                                ${warn}
                            </span>
                        </div>`
                      ).join("")
                    : `<span class="text-green-600 text-sm flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        No issues
                      </span>`
        }
    </div>
</td>
</tr>`;
    }).join("");
     requestAnimationFrame(() => {
        initTooltips();
    });
}
document.addEventListener("DOMContentLoaded", function () {
    // Initial render from sessionStorage (JS validation only)
    const previewData = JSON.parse(sessionStorage.getItem("skuPreviewData") || "[]");
    renderSKUTable(previewData);

    // If you want to immediately trigger backend validation:
    sendPreviewToBackend().then(() => {
        // Re-render table after SP validation
        const updatedData = JSON.parse(sessionStorage.getItem("skuPreviewData") || "[]");
        renderSKUTable(updatedData);
    });
});


    const errorRows = previewData.filter(r => r.errors && r.errors.length > 0);
    const validRows = previewData.filter(r => !r.errors || r.errors.length === 0);
        document.getElementById("proceedBtn").addEventListener("click", function () {

            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch("/import/process", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({
                    valid: validRows,
                    errors: errorRows,
                    import_type: document.getElementById("importType").value

                
                })
            })
           .then(res => res.json())
            .then(data => {
                if (!data.success) throw new Error("Import failed");

                window.location.href = "/import-log";
            })

            .catch(err => {
                console.error("Import Error:", err);
                alert("Something went wrong during import.");
            });

        });


 function checkOverflow(element) {
    const span = element.querySelector('.truncate-text');
    if (!span) return;
    
    // Check if text is truncated (scrollWidth > clientWidth)
    const isTruncated = span.scrollWidth > span.clientWidth;
    
    // Only show tooltip if text is actually truncated
    if (isTruncated) {
        element.classList.add('truncated');
    } else {
        element.classList.remove('truncated');
        element.removeAttribute('data-tooltip');
    }
}


document.addEventListener('DOMContentLoaded', function() {
    initTooltips();
});

window.addEventListener('resize', function() {
    initTooltips();
});

function initTooltips() {
    const tooltipCells = document.querySelectorAll('.tooltip-cell');
    tooltipCells.forEach(cell => {
        checkOverflow(cell);
    });
}
document.addEventListener("DOMContentLoaded", function () {
    const skuPreviewBody = document.getElementById("skuPreviewBody");
    const paginationNav = document.getElementById("skuPaginationNav"); // Ensure this ID matches your HTML

    // Retrieve data
    const previewData = JSON.parse(sessionStorage.getItem("skuPreviewData") || "[]");

    // Pagination Settings
    let currentPage = 1;
    const rowsPerPage = 10;

    function renderSKUTable(page) {
        if (!skuPreviewBody) return;
        skuPreviewBody.innerHTML = "";

        if (previewData.length === 0) {
            skuPreviewBody.innerHTML = `<tr><td colspan="10" class="text-center py-6 text-gray-500">No SKU preview data found.</td></tr>`;
            return;
        }

        // Calculate slice
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedItems = previewData.slice(start, end);

        // Map through paginated data
        skuPreviewBody.innerHTML = paginatedItems.map(row => {
            let statusBadge = "";
            let rowClass = "";

            if (row.status === "Error" || (row.errors && row.errors.length > 0)) {
                statusBadge = `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i> Error</span>`;
                rowClass = "error-row hover:bg-red-50";
            } else if (row.status === "Warning" || (row.warnings && row.warnings.length > 0)) {
                statusBadge = `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800"><i class="fas fa-exclamation-triangle mr-1"></i> Warning</span>`;
                rowClass = "warning-row hover:bg-yellow-50";
            } else {
                statusBadge = `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i> Valid</span>`;
                rowClass = "hover:bg-green-50";
            }

            return `
            <tr class="${rowClass} transition-all duration-200 border-b border-gray-100">
                <td class="p-4 font-mono">${row.lineNo || "_"}</td>
                <td class="p-4">${statusBadge}</td>
                <td class="p-4 font-mono font-medium text-purple-600 w-40 truncate" title="${row.Item_Code || '-'}">${row.Item_Code || "-"}</td>
                <td class="p-4 w-40">
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded inline-block max-w-full truncate" title="${row.SizeName || '-'}">
                        ${row.SizeName || "-"}
                    </span>
                </td>
                <td class="p-4 w-48">
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full mr-2" style="background-color: #${row.ColorCode || 'ccc'}"></span>
                        <span class="truncate" title="${row.ColorName || '-'}">${row.ColorName || "-"}</span>
                    </div>
                </td>
                <td class="p-4 w-32 truncate">${row.SizeCode || "-"}</td>
                <td class="p-4 w-32 truncate">${row.ColorCode || "-"}</td>
                <td class="p-4 font-mono w-40 truncate">${row.JanCD || "-"}</td>
                <td class="p-4 w-28 text-right font-bold">${row.Quantity || "0"}</td>
                <td class="p-4">
                    <div class="text-xs space-y-1">
                        ${renderIssueMessages(row)}
                    </div>
                </td>
            </tr>`;
        }).join("");

        updatePaginationUI();
    }

    function renderIssueMessages(row) {
        if (row.errors?.length > 0) {
            return row.errors.map(err => `<div class="text-red-600 truncate" title="${err}"><i class="fas fa-times-circle mr-1"></i>${err}</div>`).join("");
        } else if (row.warnings?.length > 0) {
            return row.warnings.map(warn => `<div class="text-yellow-600 truncate" title="${warn}"><i class="fas fa-exclamation-triangle mr-1"></i>${warn}</div>`).join("");
        }
        return `<span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>No issues</span>`;
    }

    function updatePaginationUI() {
        if (!paginationNav) return;
        const totalPages = Math.ceil(previewData.length / rowsPerPage);
        paginationNav.innerHTML = "";

        // Previous Button
        addPageButton("<", () => { if (currentPage > 1) { currentPage--; renderSKUTable(currentPage); } });

        // Numeric Buttons
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                addPageButton(i, () => { currentPage = i; renderSKUTable(currentPage); }, i === currentPage);
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                const dots = document.createElement("span");
                dots.className = "px-3 py-2 text-gray-400";
                dots.textContent = "...";
                paginationNav.appendChild(dots);
            }
        }

        // Next Button
        addPageButton(">", () => { if (currentPage < totalPages) { currentPage++; renderSKUTable(currentPage); } });
    }

    function addPageButton(text, onClick, isActive = false) {
        const btn = document.createElement("button");
        btn.textContent = text;
        btn.className = isActive 
            ? "relative z-10 inline-flex items-center bg-purple-600 px-4 py-2 text-sm font-semibold text-white focus:z-20"
            : "relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20";
        btn.addEventListener("click", onClick);
        paginationNav.appendChild(btn);
    }

    // Initial Execution
    renderSKUTable(currentPage);
});



//09 -Feb-2026

</script>
</body>
</html>