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
            max-width: 300px;
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
            
            <!-- Summary Stats -->
            <!-- <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-blue-100 mr-4">
                            <i class="fas fa-list text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Items</p>
                            <p class="text-2xl font-bold text-gray-800">1,250</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-green-100 mr-4">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Valid Items</p>
                            <p class="text-2xl font-bold text-gray-800">1,120</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-red-100 mr-4">
                            <i class="fas fa-exclamation-circle text-red-600 text-xl error-badge"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Errors</p>
                            <p class="text-2xl font-bold text-gray-800">45</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-yellow-100 mr-4">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Warnings</p>
                            <p class="text-2xl font-bold text-gray-800">85</p>
                        </div>
                    </div>
                </div>
            </div>
             -->
            <!-- Filters and Actions -->
            <!-- <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 mb-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                            <select id="statusFilter" class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                <option value="all">All Items</option>
                                <option value="error">Errors Only</option>
                                <option value="warning">Warnings Only</option>
                                <option value="success">Valid Only</option>
                            </select>
                        </div>
                        
                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Items per page</label>
                            <select id="itemsPerPage" class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                <option value="50">50 items</option>
                                <option value="100" selected>100 items</option>
                                <option value="250">250 items</option>
                                <option value="500">500 items</option>
                            </select>
                        </div> 
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <input type="text" placeholder="Search items..." 
                                   class="border border-gray-300 rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 w-full md:w-64">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                        
                        <button id="toggleAllBtn" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors duration-200">
                            <i class="fas fa-eye mr-2"></i>
                            Show/Hide All Details
                        </button>
                    </div>
                </div>
            </div> -->
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
                            <th class="p-4 text-left font-semibold">ListPrice</th>
                            <th class="p-4 text-left font-semibold">SalePrice</th>
                            <th class="p-4 text-left font-semibold w-80">Error Message</th>
                        </tr>
                    </thead>
<tbody id="previewTableBody" class="divide-y divide-gray-200"></tbody>
                       
                        
                </table>
            </div>
            
         
        </div>
        <input type="hidden" id="importType" value="1">

      
                </div>
                <div class="flex flex-wrap gap-3">
                  
    </main>

    <!-- JavaScript -->
          <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter functionality
            // const statusFilter = document.getElementById('statusFilter');
            // const tableRows = document.querySelectorAll('tbody tr');
            
            // statusFilter.addEventListener('change', function() {
            //     const filterValue = this.value;
                
            //     tableRows.forEach(row => {
            //         const status = row.classList.contains('error-row') ? 'error' :
            //                       row.classList.contains('warning-row') ? 'warning' :
            //                       row.classList.contains('success-row') ? 'success' : '';
                    
            //         if (filterValue === 'all' || filterValue === status) {
            //             row.style.display = '';
            //         } else {
            //             row.style.display = 'none';
            //         }
            //     });
            // });
            
            // Toggle all details
            // const toggleAllBtn = document.getElementById('toggleAllBtn');
            // let detailsVisible = false;
            
            // toggleAllBtn.addEventListener('click', function() {
            //     detailsVisible = !detailsVisible;
            //     const errorMessages = document.querySelectorAll('td:nth-child(10)');
                
            //     errorMessages.forEach(cell => {
            //         if (detailsVisible) {
            //             cell.style.maxHeight = 'none';
            //             cell.style.overflow = 'visible';
            //             cell.style.whiteSpace = 'normal';
            //         } else {
            //             cell.style.maxHeight = '60px';
            //             cell.style.overflow = 'hidden';
            //         }
            //     });
                
            //     this.innerHTML = detailsVisible ? 
            //         '<i class="fas fa-eye-slash mr-2"></i> Hide All Details' :
            //         '<i class="fas fa-eye mr-2"></i> Show All Details';
            // });
            
            // Proceed button
            // const proceedBtn = document.getElementById('proceedBtn');
            // proceedBtn.addEventListener('click', function() {
            //     const errorCount = 45; // This would come from actual data
                
            //     if (errorCount > 0) {
            //         if (!confirm(`You still have ${errorCount} errors. Are you sure you want to proceed with the import? Errors will be skipped.`)) {
            //             return;
            //         }
            //     }
                
            //     // Show loading state
            //     this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
            //     this.disabled = true;
                
            //     // Simulate processing
            //     setTimeout(() => {
            //         alert('Import completed successfully! 1,120 items imported, 45 errors skipped.');
            //         this.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Proceed with Import';
            //         this.disabled = false;
            //     }, 2000);
            // });
            
            // Download errors button
            // const downloadErrorsBtn = document.getElementById('downloadErrorsBtn');
            // downloadErrorsBtn.addEventListener('click', function() {
            //     // Show loading state
            //     const originalHTML = this.innerHTML;
            //     this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Generating report...';
            //     this.disabled = true;
                
            //     // Simulate report generation
            //     setTimeout(() => {
            //         alert('Error report downloaded successfully! (errors_report.csv)');
            //         this.innerHTML = originalHTML;
            //         this.disabled = false;
            //     }, 1500);
            // });
            
            // Fix errors button
            // const fixErrorsBtn = document.getElementById('fixErrorsBtn');
            // fixErrorsBtn.addEventListener('click', function() {
            //     alert('Download the error report, fix issues in your source Excel/CSV file, and re-upload the corrected file.');
            // });
            
            // Re-import button
            const reimportBtn = document.getElementById('reimportBtn');
            reimportBtn.addEventListener('click', function() {
                window.location.href = '/item-master/import';
            });
            
            // Search functionality
            // const searchInput = document.querySelector('input[type="text"]');
            // searchInput.addEventListener('keyup', function(e) {
            //     const searchTerm = this.value.toLowerCase();
                
            //     tableRows.forEach(row => {
            //         const rowText = row.textContent.toLowerCase();
            //         if (rowText.includes(searchTerm)) {
            //             row.style.display = '';
            //         } else {
            //             row.style.display = 'none';
            //         }
            //     });
            // });
            
            // // Row click to show details
            // tableRows.forEach(row => {
            //     row.addEventListener('click', function(e) {
            //         if (e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target.tagName === 'INPUT') {
            //             return;
            //         }
                    
            //         const errorCell = this.querySelector('td:nth-child(10)');
            //         if (errorCell) {
            //             const isHidden = errorCell.style.maxHeight === '60px' || !errorCell.style.maxHeight;
                        
            //             if (isHidden) {
            //                 errorCell.style.maxHeight = 'none';
            //                 errorCell.style.overflow = 'visible';
            //                 errorCell.style.whiteSpace = 'normal';
            //             } else {
            //                 errorCell.style.maxHeight = '60px';
            //                 errorCell.style.overflow = 'hidden';
            //             }
            //         }
            //     });
            // });
            
            // Pagination buttons
            document.querySelectorAll('.pagination button').forEach(button => {
                button.addEventListener('click', function() {
                    const buttonText = this.textContent.trim();
                    alert(`Page navigation: ${buttonText} (simulated)`);
                });
            });
        });
        document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.getElementById("previewTableBody");

    // Retrieve preview data from sessionStorage
    const previewData = JSON.parse(sessionStorage.getItem("previewData") || "[]");

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
    <td class="p-4 tooltip-cell font-mono" data-tooltip="${row.Item_Code || '-'}">
        <span class="truncate-text">${row.Item_Code || "-"}</span>
    </td>

    <!-- Item_Name -->
    <td class="p-4 tooltip-cell" data-tooltip="${row.Item_Name || '-'}">
        <span class="truncate-text">${row.Item_Name || "-"}</span>
    </td>

    <!-- JanCD -->
    <td class="p-4 tooltip-cell font-mono" data-tooltip="${row.JanCD || '-'}">
        <span class="truncate-text">${row.JanCD || "-"}</span>
    </td>

    <!-- MakerName -->
    <td class="p-4 tooltip-cell" data-tooltip="${row.MakerName || '-'}">
        <span class="truncate-text">${row.MakerName || "-"}</span>
    </td>

    <!-- Memo -->
    <td class="p-4 tooltip-cell max-w-xs" data-tooltip="${row.Memo || '-'}">
        <span class="truncate-text text-sm text-gray-600">
            ${row.Memo || "-"}
        </span>
    </td>

    <!-- ListPrice -->
    <td class="p-4 tooltip-cell text-right font-medium" data-tooltip="${row.ListPrice ?? '-'}">
        <span class="truncate-text">
               ${row.ListPrice ? Number(row.ListPrice).toLocaleString('en-US') : '-'}

        </span>
    </td>

    <!-- SalePrice -->
    <td class="p-4 tooltip-cell text-right font-medium" data-tooltip="${row.SalePrice ?? '-'}">
        <span class="truncate-text">
                ${row.SalePrice ? Number(row.SalePrice).toLocaleString('en-US') : '-'}

        </span>
    </td>

    <!-- Error Message -->
     <td class="p-4"
        data-tooltip="${row.errors.concat(row.warnings).join(' | ') || 'No issues'}">
        <div class="space-y-1 max-h-20 overflow-y-auto">
            ${
                row.errors.length > 0
                    ? row.errors.map(err =>
                        `<div class="text-sm text-red-600 flex items-start">
                            <i class="fas fa-times-circle mr-2 mt-0.5 flex-shrink-0"></i>
                            <span class="truncate-text">${err}</span>
                        </div>`
                      ).join("")
                    : row.warnings.length > 0
                        ? row.warnings.map(warn =>
                            `<div class="text-sm text-yellow-600 flex items-start">
                                <i class="fas fa-exclamation-triangle mr-2 mt-0.5 flex-shrink-0"></i>
                                <span class="truncate-text">${warn}</span>
                            </div>`
                          ).join("")
                        : `<span class="text-green-600 text-sm flex items-center">
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
//fixed latest
    </script>
</body>
</html>