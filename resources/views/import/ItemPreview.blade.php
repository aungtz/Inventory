<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Import Preview</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
            max-width: 600px;
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
                            Item Import Preview
                        </h1>
                        <span class="ml-4 px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Preview Mode
                        </span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3">
                    <a href="/item-master/import" class="inline-flex items-center px-5 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-all duration-300">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Import
                    </a>
                    
                
                    
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
                    <!-- <div>
                        <h2 class="text-xl font-bold text-gray-800">Imported Items Preview</h2>
                        <p class="text-gray-600 text-sm mt-1">Showing <span class="font-medium text-indigo-600" id="totalCount">1,250</span> items from import file</p>
                    </div> -->
                    
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <span class="status-dot status-success"></span>
                                <span class="text-sm text-gray-600">Valid</span>
                            </div>
                            
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
                            <th class="p-4 text-left font-semibold">Item_Name</th>
                            <th class="p-4 text-left font-semibold">JanCD</th>
                            <th class="p-4 text-left font-semibold">MakerName</th>
                            <th class="p-4 text-left font-semibold">Memo</th>
                            <th class="p-4 text-right font-semibold">ListPrice</th>
                            <th class="p-4 text-right font-semibold">SalePrice</th>
                            <th class="p-4 text-left font-semibold w-80">Error Message</th>
                        </tr>
                    </thead>
<tbody id="previewTableBody" class="divide-y divide-gray-200"></tbody>
                       
                        
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
            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination" id="paginationNav">
                </nav>
        </div>
    </div>
</div>
         
        </div>


        <input type="hidden" id="importType" value="1">

      
                </div>
                <div class="flex flex-wrap gap-3">
                  
    </main>

    <!-- JavaScript -->
          <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
      
        document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.getElementById("previewTableBody");

    // Retrieve preview data from sessionStorage
    const previewData = JSON.parse(sessionStorage.getItem("previewData") || "[]");
    console.log(previewData)

    if (previewData.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="10" class="p-6 text-center text-gray-500">
                    No preview data available.
                </td>
            </tr>`;
        return;
    }

    previewData.forEach((row, index) => {
        const lineNumber = index + 1;

        // Status style
        let statusClass = "";
        let statusLabel = "";
        let statusIcon = "";

        if (row.errors.length > 0) {
            statusClass = "error-row bg-red-50";
            statusLabel = "Error";
            statusIcon = "fa-times-circle";
        } else if (row.warnings.length > 0) {
            statusClass = "warning-row bg-yellow-50";
            statusLabel = "Warning";
            statusIcon = "fa-exclamation-triangle";
        } else {
            statusClass = "success-row bg-green-50";
            statusLabel = "Valid";
            statusIcon = "fa-check-circle";
        }

        // Build error/warning messages
        let errorHtml = "";
        if (row.errors.length > 0) {
            errorHtml = `
                <div class="text-sm text-red-600">
                    <div class="font-medium mb-1">
                        <i class="fas ${statusIcon} mr-1"></i> ${row.errors.length} Errors
                    </div>
                    <ul class="text-xs text-gray-500 list-disc pl-4 mt-1 space-y-1">
                        ${row.errors.map(err => `<li>${err}</li>`).join("")}
                    </ul>
                </div>
            `;
        } else if (row.warnings.length > 0) {
            errorHtml = `
                <div class="text-sm text-yellow-600">
                    <div class="font-medium mb-1">
                        <i class="fas ${statusIcon} mr-1"></i> ${row.warnings.length} Warning(s)
                    </div>
                    <ul class="text-xs text-gray-500 list-disc pl-4 mt-1 space-y-1">
                        ${row.warnings.map(w => `<li>${w}</li>`).join("")}
                    </ul>
                </div>
            `;
        } else {
            errorHtml = `<span class="text-green-600 text-sm"><i class="fas fa-check-circle mr-1"></i>No issues</span>`;
        }

        // Insert row into table
       tableBody.innerHTML += `
<tr class="${statusClass} hover:bg-gray-50 transition-all duration-150">
    <!-- Line # -->
    <td class="p-4 font-mono"> ${row.lineNo ? Number(String(row.lineNo).replace('#','')) : "_"}</td>

    <!-- Status -->
    <td class="p-4">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
            ${row.errors.length > 0 ? "bg-red-100 text-red-800" :
              row.warnings.length > 0 ? "bg-yellow-100 text-yellow-800" :
              "bg-green-100 text-green-800"}">
            <i class="fas ${statusIcon} mr-1"></i>
            ${statusLabel}
        </span>
    </td>

    <!-- Item_Code -->
<td class="p-4 font-mono">
    <span class="truncate-text whitespace-nowrap overflow-hidden text-ellipsis block"
          title="${row.Item_Code || '-'}">
        ${row.Item_Code || "-"}
    </span>
</td>

<!-- Item_Name -->
<td class="p-4">
    <span class="whitespace-nowrap overflow-hidden text-ellipsis block"
          title="${row.Item_Name || '-'}">
        ${row.Item_Name || "-"}
    </span>
</td>

<!-- JanCD -->
<td class=" font-mono w-36 min-w-[140px]">
    <span class="truncate-text whitespace-nowrap overflow-hidden text-ellipsis block"
          title="${row.JanCD || '-'}">
        ${row.JanCD || "-"}
    </span>
</td>

<!-- MakerName -->
<td class="p-4">
    <span class="truncate-text whitespace-nowrap overflow-hidden text-ellipsis block"
          title="${row.MakerName || '-'}">
        ${row.MakerName || "-"}
    </span>
</td>

<!-- Memo -->
<td class="p-4 max-w-xs">
    <span class="truncate-text text-sm text-gray-600 whitespace-nowrap overflow-hidden text-ellipsis block"
          title="${row.Memo || '-'}">
        ${row.Memo || "-"}
    </span>
</td>

<!-- ListPrice -->
<td class="p-4 text-right font-medium">
    <span class="truncate-text whitespace-nowrap overflow-hidden text-ellipsis block"
          title="${row.ListPrice ? Number(row.ListPrice).toLocaleString('en-US') : '-'}">
        ${row.ListPrice ? Number(row.ListPrice).toLocaleString('en-US') : '-'}
    </span>
</td>

<!-- SalePrice -->
<td class="p-4 text-right font-medium">
    <span class="truncate-text whitespace-nowrap overflow-hidden text-ellipsis block"
          title="${row.SalePrice ? Number(row.SalePrice).toLocaleString('en-US') : '-'}">
        ${row.SalePrice ? Number(row.SalePrice).toLocaleString('en-US') : '-'}
    </span>
</td>

<!-- Error Message -->
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
                    : `<span class="text-green-600 text-sm flex items-center"
                             title="No issues">
                        <i class="fas fa-check-circle mr-2"></i>
                        No issues
                      </span>`
        }
    </div>
</td>
</tr>
`;

    });

    if (!previewData) return;

   

});
    const previewData = JSON.parse(sessionStorage.getItem("previewData") || "[]");

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

// Run on page load and window resize
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
    const tableBody = document.getElementById("previewTableBody");
    const paginationNav = document.getElementById("paginationNav");
    
    // Retrieve data
    const previewData = JSON.parse(sessionStorage.getItem("previewData") || "[]");
    
    // Pagination State
    let currentPage = 1;
    const rowsPerPage = 10;

    function renderTable(page) {
        tableBody.innerHTML = "";
        
        if (previewData.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="10" class="p-6 text-center text-gray-500">No data available.</td></tr>`;
            return;
        }

        // Calculate start and end
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedItems = previewData.slice(start, end);

        // Update counts in UI
        document.getElementById("startRange").textContent = start + 1;
        document.getElementById("endRange").textContent = Math.min(end, previewData.length);
        document.getElementById("totalResults").textContent = previewData.length;

        paginatedItems.forEach((row, index) => {
            // Determine Status Logic (Same as your original code)
            const isError = row.errors && row.errors.length > 0;
            const isWarning = row.warnings && row.warnings.length > 0;
            
            let statusClass = isError ? "bg-red-50" : isWarning ? "bg-yellow-50" : "bg-green-50";
            let statusLabel = isError ? "Error" : isWarning ? "Warning" : "Valid";
            let statusIcon = isError ? "fa-times-circle" : isWarning ? "fa-exclamation-triangle" : "fa-check-circle";
            let badgeClass = isError ? "bg-red-100 text-red-800" : isWarning ? "bg-yellow-100 text-yellow-800" : "bg-green-100 text-green-800";

            tableBody.innerHTML += `
                <tr class="${statusClass} hover:bg-gray-100 transition-all border-b">
                    <td class="p-4 font-mono">${row.lineNo || (start + index + 1)}</td>
                    <td class="p-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${badgeClass}">
                            <i class="fas ${statusIcon} mr-1"></i> ${statusLabel}
                        </span>
                    </td>
                    <td class="p-4 font-mono truncate max-w-[120px]" title="${row.Item_Code}">${row.Item_Code || "-"}</td>
                    <td class="p-4 truncate max-w-[150px]" title="${row.Item_Name}">${row.Item_Name || "-"}</td>
                    <td class=" font-mono w-40 truncate">${row.JanCD || "-"}</td>
                    <td class="p-4 truncate max-w-[120px]">${row.MakerName || "-"}</td>
                    <td class="p-4 truncate max-w-[100px] text-gray-500">${row.Memo || "-"}</td>
                    <td class="p-4 text-right">¥${row.ListPrice ? Number(row.ListPrice).toLocaleString() : "-"}</td>
                    <td class="p-4 text-right font-bold">¥${row.SalePrice ? Number(row.SalePrice).toLocaleString() : "-"}</td>
                    <td class="p-4">
                        <div class="text-xs ${isError ? 'text-red-600' : 'text-yellow-600'}">
                            ${isError ? row.errors[0] : (isWarning ? row.warnings[0] : '<span class="text-green-600">No issues</span>')}
                        </div>
                    </td>
                </tr>`;
        });

        renderPagination();
    }

    function renderPagination() {
        const totalPages = Math.ceil(previewData.length / rowsPerPage);
        paginationNav.innerHTML = "";

        // Previous Button
        addPaginationButton("<", () => { if (currentPage > 1) { currentPage--; renderTable(currentPage); } });

        // Page Numbers
        for (let i = 1; i <= totalPages; i++) {
            // Simple logic: only show pages near current or first/last
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                addPaginationButton(i, () => { currentPage = i; renderTable(currentPage); }, i === currentPage);
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                const dots = document.createElement("span");
                dots.className = "relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300";
                dots.textContent = "...";
                paginationNav.appendChild(dots);
            }
        }

        // Next Button
        addPaginationButton(">", () => { if (currentPage < totalPages) { currentPage++; renderTable(currentPage); } });
    }

    function addPaginationButton(text, onClick, isActive = false) {
        const btn = document.createElement("button");
        btn.innerHTML = text;
        btn.className = isActive 
            ? "relative z-10 inline-flex items-center bg-indigo-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            : "relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0";
        btn.addEventListener("click", onClick);
        paginationNav.appendChild(btn);
    }

    // Initial load
    renderTable(currentPage);
});
//06-Feb-2026
    </script>
</body>
</html>