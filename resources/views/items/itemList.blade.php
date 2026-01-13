<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sku-matrix-cell {
            transition: all 0.2s ease;
        }
        .sku-matrix-cell:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .checkbox-all:checked ~ .checkmark {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }
        .modal-open {
            overflow: hidden;
        }
        /* Hide scrollbar in SKU modal */
        #skuModal .modal-content::-webkit-scrollbar {
            display: none;
        }

        #skuModal .modal-content {
            -ms-overflow-style: none;
            scrollbar-width: none;
            overflow-y: auto; /* Keep scrolling functionality */
        }

        
    </style>
</head>
<body class="bg-gray-50">

@include('layout.sidebar')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <!-- Title and Search section in one row -->
    <div class="flex-1 min-w-0 flex flex-col sm:flex-row items-start sm:items-center gap-4">
        <!-- Search section -->
        <div class="w-full sm:w-auto flex-1">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <!-- Live Search Checkbox -->
                <div class="flex items-center space-x-2">
                    <input 
                        type="checkbox" 
                        id="liveSearchCheckbox" 
                        name="live_search"
                        checked
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                    >
                    <label for="liveSearchCheckbox" class="text-sm font-medium text-gray-700">
                        Like Search
                    </label>
                </div>

                <!-- Search textareas with button -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start gap-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 flex-1 min-w-0">
                            <!-- Item Code Search -->
                            <div class="relative min-w-0">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-hashtag text-gray-400 text-sm"></i>
                                </div>
                                <textarea 
                                    id="itemCodeSearch"
                                    name="item_code_search"
                                    placeholder="Item Code..."
                                    rows="1"
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 resize-y text-sm min-h-[42px]"
                                ></textarea>
                            </div>
                            
                            <!-- Item Name Search -->
                            <div class="relative min-w-0">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-tag text-gray-400 text-sm"></i>
                                </div>
                                <textarea 
                                    id="itemNameSearch"
                                    name="item_name_search"
                                    placeholder="Item Name..."
                                    rows="1"
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 resize-y text-sm min-h-[42px]"
                                ></textarea>
                            </div>
                        </div>
                        
                        <!-- Search Button aligned with textareas -->
                        <button 
                            type="button"
                            id="searchButton"
                            class="px-4 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition flex items-center justify-center whitespace-nowrap h-[42px] self-center"
                        >
                            <i class="fas fa-search"></i>
                            <span class="hidden md:inline ml-2">Search</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right side buttons section -->
    <div class="w-full sm:w-auto flex flex-col sm:flex-row gap-4 sm:items-center">
        <!-- View Type Toggle -->
        <div class="flex items-center space-x-2 bg-gray-100 rounded-lg p-1">
            <button 
                type="button"
                id="itemViewBtn"
                class="px-4 py-2 rounded-md text-sm font-medium transition-all bg-white text-gray-800 shadow-sm"
                onclick="setViewType('item')"
            >
                <i class="fas fa-cube mr-1"></i>
                Item
            </button>
            <button 
                type="button"
                id="skuViewBtn"
                class="px-4 py-2 rounded-md text-sm font-medium transition-all text-gray-600 hover:text-gray-800"
                onclick="setViewType('sku')"
            >
                <i class="fas fa-boxes mr-1"></i>
                SKU
            </button>
            <input type="hidden" id="viewTypeInput">
        </div>

        <!-- Export Buttons Group -->
        <div class="flex gap-2">
            <!-- Excel Export Form -->
            <form id="exportForm" method="GET" action="/items/export" class="export-form inline">
                <input type="hidden" name="view_type" id="excelExportViewType" value="item">
                <input type="hidden" name="format" value="excel">
                <button 
                    type="submit"
                    class="px-4 py-2.5 border border-gray-300 bg-white text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition flex items-center justify-center"
                >
                    <i class="fas fa-file-excel mr-2 text-green-600"></i>
                    <span>Excel</span>
                </button>
            </form>
            
            <!-- CSV Export Form -->
            <form id="csvExportForm" method="GET" action="/items/export" class="export-form inline">
                <input type="hidden" name="view_type" id="csvExportViewType" value="item">
                <input type="hidden" name="format" value="csv">
                <button 
                    type="submit"
                    class="px-4 py-2.5 border border-gray-300 bg-white text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition flex items-center justify-center"
                >
                    <i class="fas fa-file-csv mr-2 text-blue-600"></i>
                    <span>CSV</span>
                </button>
            </form>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2 sm:gap-3">
            <!-- Delete Button -->
            <button 
                id="deleteSelectedBtn" 
                class="px-4 py-2.5 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center justify-center"
            
            >
                <i class="fas fa-trash mr-2"></i>
                <span class="hidden sm:inline">Delete Selected</span>
                <span class="sm:hidden">Delete</span>
            </button>
            
            <!-- Create New Button -->
            <a href="/items-create">
                <button 
                    class="px-4 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition flex items-center justify-center"
                >
                    <i class="fas fa-plus mr-2"></i>
                    <span class="hidden sm:inline">Create New</span>
                    <span class="sm:hidden">New</span>
                </button>
            </a>
        </div>
    </div>
</div>


        <!-- Table -->
     <div class="bg-white rounded-xl shadow overflow-hidden border border-gray-200">
    <div class="overflow-x-auto">
        <table class="w-full divide-y divide-gray-200 table-fixed" style="min-width: 1000px;">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">
                            <input type="checkbox" id="check-all" class="h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        </th>      
                    <th class="px-3 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Item Code</th>
                    <th class="px-3 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-64">Item Name</th>
                    <th class="px-3 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-40">Jan CD</th>
                    <th class="px-3 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-40">Maker Name</th>
                    <th class="px-3 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-48">Memo</th>
                    <th class="px-3 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">List Price</th>
                    <th class="px-3 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">Sale Price</th>
                    <th class="px-3 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="itemsTableBody">
                @foreach ($items as $index => $item)    
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <input type="checkbox" class="item-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500" value="{{ $item->Item_Code }}">
                        </td>

                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <div class="truncate" title="{{ $item->Item_Code }}">
                                <a href="{{ route('items.edit', $item->Item_Code) }}" class="text-blue-600 hover:underline">
                                    {{ $item->Item_Code ?? '-' }}
                                </a>
                            </div>
                        </td>

                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">
                            <div class="truncate font-medium" title="{{ $item->Item_Name }}">
                                {{ $item->Item_Name ?? '-' }}
                            </div>
                        </td>

                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="truncate" title="{{ $item->JanCD }}">
                                {{ $item->JanCD ?? '-' }}
                            </div>
                        </td>

                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="truncate" title="{{ $item->MakerName }}">
                                {{ $item->MakerName ?? '-' }}
                            </div>
                        </td>

                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="truncate italic" title="{{ $item->Memo }}">
                                {{ $item->Memo ?? '-' }}
                            </div>
                        </td>

                        <td class="px-3 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">
                            ¥{{ number_format($item->ListPrice ?? 0) }}
                        </td>

                        <td class="px-3 py-4 whitespace-nowrap text-sm font-semibold text-green-600 text-right">
                            ¥{{ number_format($item->SalePrice ?? 0) }}
                        </td>

                        <td class="px-3 py-4 whitespace-nowrap text-center">
                            <button class="view-sku-btn inline-flex items-center px-2.5 py-1.5 bg-white border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 hover:bg-gray-50 focus:outline-none"
                                data-code="{{ trim($item->Item_Code) }}"
                                data-name="{{ $item->Item_Name }}"
                                data-id="{{ $item->id ?? '' }}">
                                <i class="fas fa-eye mr-1 text-blue-500"></i> SKU
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>



            <!-- No items message -->
            <!-- <div id="noItemsMessage" class="py-12 text-center">
                <i class="fas fa-box-open text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg">No items found. Click "Create New Item" to add your first item.</p>
            </div>
        </div> -->
        
        <!-- Pagination (example) -->
        <div class="flex items-center justify-between mt-6" id="pagination-wrapper">

    <div id="pagination-info" class="text-sm text-gray-700"></div>
    <div id="pagination-controls" class="flex space-x-2"></div>
</div>

<div class="flex items-center justify-between mt-6 hidden" id="search-pagination-wrapper">
    <div id="search-pagination-info" class="text-sm text-gray-700"></div>
    <div id="search-pagination-controls" class="flex space-x-2"></div>
</div>
    </div>

<!-- sku modal html -->
    
    <div id="skuModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden transition-opacity">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-6xl shadow-lg rounded-xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <div>
                <h3 class="text-2xl font-bold text-gray-800">
                    SKU Matrix: <span id="modalItemName" class="text-blue-600"></span>
                </h3>
                <p class="text-sm text-gray-500">Item Code: <span id="modalItemCode" class="font-mono"></span></p>
            </div>
            <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <div class="py-6">
            <div class="overflow-x-auto" id="matrixContainer">
                <div class="text-center py-10 text-gray-500">
                    <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                    <p>Loading SKU data...</p>
                </div>
            </div>

            <div id="skuSummary" class="mt-6 p-4 bg-blue-50 rounded-lg hidden">
                <div class="flex justify-between items-center">
                    <div>
                        <h5 class="font-medium text-gray-800">SKU Summary</h5>
                        <p class="text-sm text-gray-600">Total variants: <span id="sumVariants" class="font-bold text-blue-700">0</span></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Total quantity: <span id="sumQty" class="font-bold text-blue-700">0</span></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end pt-4 border-t">
            <button id="closeModalBtn" class="px-5 py-2 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300">Close</button>
        </div>
    </div>
</div>

    <!-- JavaScript for functionality -->
    <script>
        // Sample data for the table
      
        // DOM elements
        const itemsTableBody = document.getElementById('itemsTableBody');
        const noItemsMessage = document.getElementById('noItemsMessage');
        const selectAllCheckbox = document.getElementById('selectAll');
        const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
        const skuModal = document.getElementById('skuModal');
        const closeModal = document.getElementById('closeModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const modalItemName = document.getElementById('modalItemName');

        // Track selected items
        let selectedItems = new Set();

        // Initialize table with sample data
        
 document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const itemId = this.dataset.id;
                    
                    if (this.checked) {
                        selectedItems.add(itemId);
                    } else {
                        selectedItems.delete(itemId);
                        selectAllCheckbox.checked = false;
                    }
                    
                    // updateDeleteButtonState();
                });
            });
            
    


function renderMatrixTable(data, container) {
    const sizeKeys = Object.keys(data.sizes);
    const sizeCount = sizeKeys.length;
    const colorEntries = Object.entries(data.colors);
    
    let html = `<div class="flex border-2 border-gray-300 rounded-lg overflow-hidden">
        <!-- LEFT SIDEBAR: VERTICAL COLOR LABEL -->
        <div class="flex items-center justify-center bg-blue-600" style="width: 60px; min-width: 60px;">
            <div class="font-bold text-white text-xl tracking-wider text-center">
                <div class="py-2 border-b border-blue-700">C</div>
                <div class="py-2 border-b border-blue-700">O</div>
                <div class="py-2 border-b border-blue-700">L</div>
                <div class="py-2 border-b border-blue-700">O</div>
                <div class="py-2">R</div>
            </div>
        </div>
        
        <!-- MAIN TABLE AREA -->
        <div class="flex-1 overflow-x-auto">
            <table class="min-w-full border-collapse">
                <!-- HEADER ROWS -->
                <thead>
                    <!-- Row 1: SIZE BANNER (spans all columns including empty first cell) -->
                    <tr>
                        <th colspan="${sizeCount + 1}" class="p-4 border-b-2 border-gray-300 bg-blue-600">
                            <div class="flex items-center justify-center">
                                <span class="text-xl font-bold text-white tracking-wide">SIZE</span>
                            </div>
                        </th>
                    </tr>
                    
                    <!-- Row 2: Size Labels with empty first cell -->
                    <tr>
                        <!-- First cell: Empty or with # symbol -->
                        <th class="p-3 border-b-2 border-gray-300 bg-gray-50 text-center">
                            <div class="font-bold text-base text-gray-800">#</div>
                        </th>
                        
                        <!-- Size data cells (starting from column 2) -->
                        ${sizeKeys.map(sCode => {
                            const sizeName = data.sizes[sCode];
                            return `<th class="p-3 border-b-2 border-gray-300 bg-gray-50 text-center">
                                <div class="font-bold text-base text-gray-800">${sizeName}</div>
                               
                            </th>`;
                        }).join('')}
                    </tr>
                </thead>
                
                <!-- DATA ROWS -->
                <tbody>`;

    // Color data rows
    colorEntries.forEach(([cCode, cName], index) => {
        const rowBg = index % 2 === 0 ? 'bg-gray-50' : 'bg-white';
        
        html += `<tr class="${rowBg}">`;
        
        // First cell: Color name (this aligns under the "#" column)
        html += `<td class="p-3 border-b border-gray-300 text-center font-medium bg-gray-100">
                    <div class="font-semibold text-sm text-gray-800">${cName}</div>
                </td>`;
        
        // SKU data cells (starting from 2nd column, aligning under size labels)
        sizeKeys.forEach(sCode => {
            const sku = data.matrix[sCode] ? data.matrix[sCode][cCode] : null;
            html += `<td class="p-3 border-b border-gray-300 text-center">
                        <div class="min-h-[70px] flex flex-col items-center justify-center">`;
            if (sku) {
                html += `<div class="font-bold text-indigo-700 text-lg mb-1">${sku.Quantity}</div>
                         <div class="text-xs text-gray-500 font-mono bg-gray-50 px-2 py-1 rounded">${sku.JanCode}</div>`;
            } else {
                html += `<span class="text-gray-400 text-2xl">—</span>`;
            }
            html += `</div></td>`;
        });
        
        html += `</tr>`;
    });
    
    html += `</tbody></table></div></div>`;
    
    // Calculate summary
    let totalVariants = 0;
    let totalQuantity = 0;
    
    for (const [sCode, sizeData] of Object.entries(data.matrix)) {
        for (const [cCode, sku] of Object.entries(sizeData)) {
            if (sku) {
                totalVariants++;
                totalQuantity += parseInt(sku.Quantity) || 0;
            }
        }
    }
    
    // Update summary if element exists
    const summaryEl = document.getElementById('skuSummary');
    if (summaryEl) {
        document.getElementById('sumVariants').textContent = totalVariants;
        document.getElementById('sumQty').textContent = totalQuantity;
        summaryEl.classList.remove('hidden');
    }
    
    container.innerHTML = html;
}

        // Open SKU modal
        function openSkuModal(itemId, itemName) {
            modalItemName.textContent = itemName;
            skuModal.classList.remove('hidden');
            document.body.classList.add('modal-open');
        }

        // Close SKU modal
        function closeSkuModal() {
            skuModal.classList.add('hidden');
            document.body.classList.remove('modal-open');
        }

       

        deleteSelectedBtn.addEventListener('click', function() {
            if (selectedItems.size === 0) return;
            
            if (confirmDelete) {
                // In a real app, you would make an API call to delete items
                selectedItems.clear();
                selectAllCheckbox.checked = false;
                
                // Uncheck all checkboxes
                document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
           
           
           
           
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
    const deleteBtn = document.getElementById("deleteSelectedBtn");
    const checkboxes = document.querySelectorAll(".item-checkbox");
    
    // Function to update Delete button state
    function updateDeleteButtonState() {
        const checkedCount = document.querySelectorAll(".item-checkbox:checked").length;
        deleteBtn.disabled = checkedCount === 0;
    }

    // Listen for checkbox changes
    checkboxes.forEach(cb => {
        cb.addEventListener("change", updateDeleteButtonState);
    });

    // Handle Delete Click
    deleteBtn.addEventListener("click", function() {
        const selectedIds = Array.from(document.querySelectorAll(".item-checkbox:checked"))
                                 .map(cb => cb.value);

        if (confirm(`Are you sure you want to delete ${selectedIds.length} items? This cannot be undone.`)) {
            
            // Send request to Laravel
            fetch("{{ route('items.selectDelete') }}", {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ ids: selectedIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload(); // Refresh the page to show updated list
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred while deleting.");
            });
        }
    });
});

     

        // Modal close events
        closeModal.addEventListener('click', closeSkuModal);
        closeModalBtn.addEventListener('click', closeSkuModal);

        // Close modal when clicking outside
        skuModal.addEventListener('click', function(e) {
            if (e.target === skuModal) {
                closeSkuModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !skuModal.classList.contains('hidden')) {
                closeSkuModal();
            }
        });





    const rows = document.querySelectorAll("table tbody tr");
    let currentPage = 1;
    const itemsPerPage = 10; // Change this to show more/less rows per page
    let searchRows = []; 
   
    const infoContainer = document.getElementById("pagination-info");
    const controlsContainer = document.getElementById("pagination-controls");
    const originalRows = Array.from(document.querySelectorAll("table tbody tr"));

     window.renderPagination = function() {
        const totalItems = rows.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);

        // 1. Hide all rows, then show only the ones for the current page
        rows.forEach((row, index) => {
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
row.style.display = (index >= start && index < end) ? "table-row" : "none";
        });

        // 2. Update the "Showing X to Y of Z" text
        const startItem = totalItems === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1;
        const endItem = Math.min(currentPage * itemsPerPage, totalItems);
        infoContainer.innerHTML = `Showing <span class="font-medium">${startItem}</span> to <span class="font-medium">${endItem}</span> of <span class="font-medium">${totalItems}</span> items`;

        // 3. Render Buttons
        controlsContainer.innerHTML = "";

        // Previous Button
        const prevBtn = createButton("Previous", currentPage === 1, () => {
            currentPage--;
            renderPagination();
        });
        controlsContainer.appendChild(prevBtn);

        // Page Numbers
        for (let i = 1; i <= totalPages; i++) {
            const isCurrent = i === currentPage;
            const pageBtn = createButton(i, false, () => {
                currentPage = i;
                renderPagination();
            }, isCurrent);
            controlsContainer.appendChild(pageBtn);
        }

        // Next Button
        const nextBtn = createButton("Next", currentPage === totalPages, () => {
            currentPage++;
            renderPagination();
        });
        controlsContainer.appendChild(nextBtn);
        console.log("🔥 renderPagination running");
console.log("currentPage:", currentPage);
console.log("rows length inside renderPagination:", rows.length);

    }

document.addEventListener("DOMContentLoaded", function() {
    const tableBody = document.getElementById("itemsTableBody");
    const rows = document.querySelectorAll("table tbody tr");
    const infoContainer = document.getElementById("pagination-info");
    const controlsContainer = document.getElementById("pagination-controls");
    const originalRows = Array.from(document.querySelectorAll("table tbody tr"));
    
   

    renderPagination();

    function paginateRows(rowsArray) {
    const totalItems = rowsArray.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    // Hide all rows first
    rowsArray.forEach(r => r.style.display = "none");

    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;

    rowsArray.slice(start, end).forEach(r => {
        r.style.display = "";
    });

    // Info text
    const startItem = totalItems === 0 ? 0 : start + 1;
    const endItem = Math.min(end, totalItems);

    infoContainer.innerHTML = `
        Showing <span class="font-medium">${startItem}</span>
        to <span class="font-medium">${endItem}</span>
        of <span class="font-medium">${totalItems}</span> items
    `;

    // Buttons
    controlsContainer.innerHTML = "";

    if (totalPages <= 1) return;

    controlsContainer.appendChild(
        createButton("Previous", currentPage === 1, () => {
            currentPage--;
            paginateRows(rowsArray);
        })
    );

    for (let i = 1; i <= totalPages; i++) {
        controlsContainer.appendChild(
            createButton(i, false, () => {
                currentPage = i;
                paginateRows(rowsArray);
            }, i === currentPage)
        );
    }

    controlsContainer.appendChild(
        createButton("Next", currentPage === totalPages, () => {
            currentPage++;
            paginateRows(rowsArray);
        })
    );
}


   
   

   
});
 function createButton(text, isDisabled, onClick, isCurrent = false) {
        const btn = document.createElement("button");
        btn.textContent = text;
        btn.disabled = isDisabled;
        
        // Base Tailwind Classes
        let classes = "px-3 py-2 rounded-lg border border-gray-300 transition ";
        
        if (isCurrent) {
            classes += "bg-blue-600 text-white"; // Active style
        } else if (isDisabled) {
            classes += "bg-gray-100 text-gray-400 cursor-not-allowed"; // Disabled style
        } else {
            classes += "bg-white text-gray-700 hover:bg-gray-50"; // Normal style
        }

        btn.className = classes;
        btn.onclick = onClick;
        return btn;
    }


function renderTable() {
    Array.from(rows).forEach(r => (r.style.display = "none"));

    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;

    // filteredRows.slice(start, end).forEach(r => (r.style.display = ""));

}


// --- Search with comma support ---
const tableBody = document.getElementById("itemsTableBody");
const paginationWrapper = document.getElementById("pagination-wrapper");
const originalTableHTML = tableBody.innerHTML;



function renderSearchRows(items) {
    tableBody.innerHTML = "";

    if (items.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center py-6 text-gray-500">
                    No results found
                </td>
            </tr>
        `;
        return;
    }

    items.forEach(item => {
        const row = document.createElement("tr");
        row.className = "hover:bg-gray-50";

        row.innerHTML = `
            <!-- Checkbox -->
            <td class="w-12 px-4 py-4 whitespace-nowrap">
                <input type="checkbox"
                    class="item-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                    value="${item.Item_Code}">
            </td>

            <!-- Item Code -->
            <td class="w-32 px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 cursor-pointer hover:bg-blue-50">
                <a href="/items/${item.Item_Code}/edit" class="block w-full h-full">
                    <div class="truncate hover:text-blue-600" title="${item.Item_Code ?? ''}">
                        ${item.Item_Code ?? '-'}
                    </div>
                </a>
            </td>

            <!-- Item Name -->
            <td class="w-64 px-4 py-4 whitespace-nowrap text-sm text-gray-700 cursor-pointer hover:bg-blue-50">
                <a href="/items/${item.Item_Code}/edit" class="block w-full h-full">
                    <div class="truncate hover:text-blue-600" title="${item.Item_Name ?? ''}">
                        ${item.Item_Name ?? '-'}
                    </div>
                </a>
            </td>

            <!-- JAN -->
            <td class="w-40 px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                <div class="truncate" title="${item.JanCD ?? ''}">
                    ${item.JanCD ?? '-'}
                </div>
            </td>

            <!-- Maker -->
            <td class="w-56 px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                <div class="truncate" title="${item.MakerName ?? ''}">
                    ${item.MakerName ?? '-'}
                </div>
            </td>

            <!-- Memo -->
            <td class="w-48 px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                <div class="truncate" title="${item.Memo ?? ''}">
                    ${item.Memo ?? '-'}
                </div>
            </td>

            <!-- List Price -->
            <td class="w-32 px-4 py-4 whitespace-nowrap text-sm font-medium text-green-600 text-right">
                ${Number(item.ListPrice ?? 0).toLocaleString()}
            </td>

            <!-- Sale Price -->
            <td class="w-32 px-4 py-4 whitespace-nowrap text-sm font-medium text-green-600 text-right">
                ${Number(item.SalePrice ?? 0).toLocaleString()}
            </td>

            <!-- Action -->
            <td class="w-40 px-4 py-4 whitespace-nowrap text-sm font-medium">
                 <button class="view-sku-btn inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200"
                            data-code="${item.Item_Code}"
    data-name="${item.Item_Name}"
    data-id="${item.id ?? ''}">
                            <i class="fas fa-cube mr-1.5"></i> View SKU
                        </button>
            </td>
        `;

        tableBody.appendChild(row);
    });
    searchRows = Array.from(tableBody.querySelectorAll("tr"));
    searchPage = 1;

    paginationWrapper.style.display = "none";
    document.getElementById("search-pagination-wrapper").classList.remove("hidden");

    paginateSearchRows();
}


  document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById("itemsTableBody");
    const modal = document.getElementById('skuModal');
    const container = document.getElementById('matrixContainer');

    tableBody.addEventListener('click', function(e) {
        const button = e.target.closest('.view-sku-btn');
        if (!button) return;

        const itemCode = button.dataset.code;
        const itemName = button.dataset.name;

        // Update modal labels
        document.getElementById('modalItemCode').innerText = itemCode;
        document.getElementById('modalItemName').innerText = itemName;

        // Show loading spinner
        container.innerHTML = `
            <div class="p-4 text-center text-gray-500">
                <i class="fas fa-spinner fa-spin mr-2"></i> Loading SKU Matrix...
            </div>
        `;
        modal.classList.remove('hidden');

        // Fetch SKU data
        fetch("{{ route('get.sku.matrix') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ item_code: itemCode })
        })

        .then(res => res.json())
        .then(data => {
                                        const summaryEl = document.getElementById('skuSummary');

            if (!data.colors || Object.keys(data.colors).length === 0) {
                container.innerHTML = `
                    <div class="p-8 text-center bg-gray-50 rounded-lg border-2 border-dashed">
                        <p class="text-gray-500">No SKU data found for code: <strong>${itemCode}</strong></p>
                    </div>
                `;
                    if(summaryEl){
                       summaryEl.classList.add('hidden');
                    }
                return;
            }

            // Render table
            renderMatrixTable(data, container);
        })
        .catch(err => {
            console.error(err);
            container.innerHTML = `<div class="text-red-500 p-4">Error loading SKU data. Please try again.</div>`;
        });
    });

    // Close modal
    document.getElementById('closeModalBtn').addEventListener('click', () => {
        modal.classList.add('hidden');
    });
});




document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('skuModal');
    // const modalContent = document.getElementById('modalContent');

    // Handle button clicks
    // Attach to the parent (tableBody) so it catches clicks from dynamic buttons
tableBody.addEventListener('click', function(e) {
    // Check if the clicked element (or its parent) is the button
    const button = e.target.closest('.view-sku-btn');
    
    if (button) {
        const itemCode = button.getAttribute('data-code');
        const itemName = button.getAttribute('data-name');
        
        // Get the matching template
        const template = document.getElementById(`tpl-${itemCode}`);
        
        if (template) {
            modalContent.innerHTML = template.innerHTML;
            modal.classList.remove('hidden');
        } else {
            // Handle case where template doesn't exist
            modal.classList.remove('hidden');
            console.warn(`Template tpl-${itemCode} not found.`);
        }
    }
});
});

// Close function
function closeSkuModal() {
    document.getElementById('skuModal').classList.add('hidden');
}


let currentViewType = 'item';

function setViewType(type) {
    currentViewType = type;
    
    // Update hidden input value
     document.getElementById('excelExportViewType').value = type;
    document.getElementById('csvExportViewType').value = type;
    
    // Update export form hidden input
    
    // Update UI
    if (type === 'item') {
        document.getElementById('itemViewBtn').classList.add('bg-white', 'text-gray-800', 'shadow-sm');
        document.getElementById('itemViewBtn').classList.remove('text-gray-600', 'hover:text-gray-800');
        document.getElementById('skuViewBtn').classList.remove('bg-white', 'text-gray-800', 'shadow-sm');
        document.getElementById('skuViewBtn').classList.add('text-gray-600', 'hover:text-gray-800');
    } else {
        document.getElementById('skuViewBtn').classList.add('bg-white', 'text-gray-800', 'shadow-sm');
        document.getElementById('skuViewBtn').classList.remove('text-gray-600', 'hover:text-gray-800');
        document.getElementById('itemViewBtn').classList.remove('bg-white', 'text-gray-800', 'shadow-sm');
        document.getElementById('itemViewBtn').classList.add('text-gray-600', 'hover:text-gray-800');
    }
    
    // Optional: Reload data based on view type
    loadDataForView(type);
}

function loadDataForView(viewType) {
    // This function can fetch data via AJAX if needed
    console.log('Loading data for:', viewType);
    // Or simply reload the page with the view parameter
    // window.location.href = `?view_type=${viewType}`;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check if view_type is in URL
    const urlParams = new URLSearchParams(window.location.search);
    const urlViewType = urlParams.get('view_type');
    
    if (urlViewType && (urlViewType === 'item' || urlViewType === 'sku')) {
        setViewType(urlViewType);
    }
});


document.querySelectorAll(".export-form").forEach(form => {
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const viewType = currentViewType;

        const itemCode = document.getElementById("itemCodeSearch")?.value.trim() || "";
        const itemName = document.getElementById("itemNameSearch")?.value.trim() || "";
        const live     = document.getElementById("liveSearchCheckbox")?.checked ? 1 : 0;

        upsertHidden(this, "item_code", itemCode);
        upsertHidden(this, "item_name", itemName);
        upsertHidden(this, "live", live);

        const rows = document.querySelectorAll("#itemsTableBody tr");
        const visibleRows = Array.from(rows).filter(row =>
            row.offsetWidth > 0 && row.offsetHeight > 0
        );

        // ❌ no rows at all
        if (visibleRows.length === 0) {
            alert("No data found to export.");
            return;
        }

        // ✅ ONLY validate SKU existence in ITEM view
        if (viewType === "item") {
            const validItemRows = visibleRows.filter(row => {
                const btn = row.querySelector(".view-sku-btn");
                return btn && btn.dataset.code?.trim();
            });

            if (validItemRows.length === 0) {
                alert("Items found but no SKU data.");
                return;
            }
        }

        // ✅ PASS
        this.submit();
    });
});



function upsertHidden(form, name, value) {
    let input = form.querySelector(`input[name="${name}"]`);
    if (!input) {
        input = document.createElement("input");
        input.type = "hidden";
        input.name = name;
        form.appendChild(input);
    }
    input.value = value;
}




function getSearchMode() {
    return document.getElementById("liveSearchCheckbox").checked ? 1 : 0;
}


function getSearchParams() {
    return {
        item_code: document.getElementById("itemCodeSearch").value.trim(),
        item_name: document.getElementById("itemNameSearch").value.trim(),
        live: getSearchMode()
    };
}


const itemCodeInput = document.getElementById("itemCodeSearch");
const itemNameInput = document.getElementById("itemNameSearch");
const searchBtn = document.getElementById("searchButton");

let debounceTimer = null;

// Live typing search
[itemCodeInput, itemNameInput].forEach(input => {
   input.addEventListener("keyup", () => {
    const itemCode = itemCodeInput.value.trim();
    const itemName = itemNameInput.value.trim();

    // 🔥 ALWAYS restore when cleared
    if (!itemCode && !itemName) {
            window.location.reload();

        restoreOriginalTable();
        return;
    }

    // Exact search → do nothing on typing
    if (!getSearchMode()) return;

    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        executeSearch();
    }, 300);
});

});

// Button search (always works)
searchBtn.addEventListener("click", executeSearch);

function executeSearch() {
    const itemCode = document.getElementById("itemCodeSearch").value.trim();
    const itemName = document.getElementById("itemNameSearch").value.trim();
    const isLive   = document.getElementById("liveSearchCheckbox").checked ? 1 : 0;

    // Nothing to search
    if (!itemCode && !itemName) {
        window.location.reload();
        restoreOriginalTable();
        return;
    }

    // Hide pagination when searching
    paginationWrapper.style.display = "none";

    const params = new URLSearchParams({
        item_code: itemCode,
        item_name: itemName,
        live: isLive
    });

    fetch(`/items/search?${params.toString()}`)
        .then(res => res.json())
        .then(data => {
            renderSearchRows(data);
        })
        .catch(err => {
            console.error("Search error:", err);
        });
}
function restoreOriginalTable() {
    console.log("✅ restoreOriginalTable called");

    tableBody.innerHTML = originalTableHTML;

    document.getElementById("search-pagination-wrapper").classList.add("hidden");
    paginationWrapper.style.display = "flex";

    searchRows = [];
    searchPage = 1;

    // 🔥 RE-CAPTURE ROWS AFTER innerHTML
    window.rows = document.querySelectorAll("table tbody tr");
    console.log("HTML restored, length:", originalTableHTML.length);
console.log("tbody innerHTML length:", tableBody.innerHTML.length);


    currentPage = 1;
    window.renderPagination();
}

let searchPage = 1;
const searchItemsPerPage = 10;

function paginateSearchRows() {
    const wrapper = document.getElementById("search-pagination-wrapper");
    const info = document.getElementById("search-pagination-info");
    const controls = document.getElementById("search-pagination-controls");

    const totalItems = searchRows.length;
    const totalPages = Math.ceil(totalItems / searchItemsPerPage);

    console.log("searchRows:", searchRows.length);

    wrapper.classList.remove("hidden");

    searchRows.forEach(r => r.style.display = "none");

    const start = (searchPage - 1) * searchItemsPerPage;
    const end = start + searchItemsPerPage;

    searchRows.slice(start, end).forEach(r => r.style.display = "");

    info.innerHTML = `
        Showing <b>${start + 1}</b>
        to <b>${Math.min(end, totalItems)}</b>
        of <b>${totalItems}</b> search results
    `;

    controls.innerHTML = "";


if (totalPages <= 1) return;


    controls.appendChild(
        createButton("Previous", searchPage === 1, () => {
            searchPage--;
            paginateSearchRows();
        })
    );

    for (let i = 1; i <= totalPages; i++) {
        controls.appendChild(
            createButton(i, false, () => {
                searchPage = i;
                paginateSearchRows();
            }, i === searchPage)
        );
    }

    controls.appendChild(
        createButton("Next", searchPage === totalPages, () => {
            searchPage++;
            paginateSearchRows();
        })
    );
}
document.addEventListener('DOMContentLoaded', function () {
    const checkAllCheckbox = document.getElementById('check-all');

    function getVisibleCheckboxes() {
        return Array.from(document.querySelectorAll('.item-checkbox'))
            .filter(cb => cb.closest('tr').offsetParent !== null); // visible only
    }

    // Check/Uncheck all (CURRENT PAGE ONLY)
    if (checkAllCheckbox) {
        checkAllCheckbox.addEventListener('change', function () {
            const visibleCheckboxes = getVisibleCheckboxes();
            visibleCheckboxes.forEach(cb => {
                cb.checked = this.checked;
            });
        });
    }

    // Update Check-All state
    document.addEventListener('change', function (e) {
        if (!e.target.classList.contains('item-checkbox')) return;

        const visibleCheckboxes = getVisibleCheckboxes();
        const allChecked = visibleCheckboxes.every(cb => cb.checked);
        const someChecked = visibleCheckboxes.some(cb => cb.checked);

        checkAllCheckbox.checked = allChecked;
        checkAllCheckbox.indeterminate = someChecked && !allChecked;
    });

    // Get checked items (CURRENT PAGE ONLY)
    window.getCheckedItems = function () {
        return getVisibleCheckboxes()
            .filter(cb => cb.checked)
            .map(cb => cb.value);
    };
}); //before change nothing 
    </script>
</body>
</html>