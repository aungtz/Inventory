<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
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
       

        #skuModal .modal-content {
            -ms-overflow-style: none;
            scrollbar-width: none;
           
        }
       .custom-margin {
    margin: 10px;
       }


        .custom-padding {
    padding: 10px;
    }
    /* Enhanced hover effects */
[onclick*="sortTable"] {
    transition: background-color 0.2s ease;
    position: relative;
}

[onclick*="sortTable"]:hover {
    background-color: #f3f4f6;
}

[onclick*="sortTable"]:active {
    background-color: #e5e7eb;
    transform: translateY(1px);
}

/* Ripple effect (optional) */
[onclick*="sortTable"]::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 5px;
    height: 5px;
    background: rgba(59, 130, 246, 0.3);
    border-radius: 100%;
    transform: scale(1, 1) translate(-50%);
    transform-origin: 50% 50%;
    opacity: 0;
}

[onclick*="sortTable"]:focus:not(:active)::after {
    animation: ripple 1s ease-out;
}

@keyframes ripple {
    0% {
        transform: scale(0, 0);
        opacity: 0.5;
    }
    100% {
        transform: scale(30, 30);
        opacity: 0;
    }
}
#modalItemName {
    word-break: break-word;
    overflow-wrap: break-word;
    max-width: 100%;
    display: inline-block;
}

/* Accessibility improvements */
[onclick*="sortTable"]:focus {
    outline: 2px solid #3b82f6;
    outline-offset: -2px;
}
        
    </style>
</head>
<body class="bg-gray-50">

@include('layout.sidebar')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
<!-- Header - Main container with flex-wrap -->
<div class="flex flex-wrap justify-between items-start gap-y-4 gap-x-6 mb-6"><!-- Search and Actions Section -->
<div class="flex flex-col xl:flex-row gap-4 xl:gap-6 items-start xl:items-center w-full">
    <!-- Search Section -->
    <div class="flex flex-col xl:flex-row gap-4 xl:gap-6 items-stretch xl:items-center w-full bg-white p-4">
    <div class="flex-[3] min-w-0">
        <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-4">
            <div class="flex items-center space-x-2 shrink-0 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200 h-[42px]">
                <input type="checkbox" id="liveSearchCheckbox" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="liveSearchCheckbox" class="text-sm font-medium text-gray-700 whitespace-nowrap">Live Search</label>
            </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 flex-1">
    <div class="relative">
        <input
            id="itemCodeSearch"
            type="text"
            placeholder="Item Code"
            class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm h-[42px]"
        >
    </div>

    <div class="relative">
        <input
            id="itemNameSearch"
            type="text"
            placeholder="Item Name"
            class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm h-[42px]"
        >
    </div>

    <div class="relative">
     <input
    id="janCodeSearch"
    type="text"
    maxlength="13"
    placeholder="JAN Code"
    class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm h-[42px]"
    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
>
    </div>

    <div class="relative">
        <input
            id="adminCodeSearch"
            type="text"
            placeholder="AdminCode"
            class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm h-[42px]"
        >
    </div>
</div>

            
          <button onclick="executeSkuSearch()" class="searchButton lg:w-32 h-[42px] bg-blue-600 text-white rounded-lg" id="searchButton">
    <i class="fas fa-search mr-2"></i> Search
</button>

        </div>
    </div>

    
</div>

    <!-- Actions Section -->
    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center shrink-0 w-full sm:w-auto min-w-0">
        <!-- Export Buttons -->
        <div class="flex gap-2 bg-gray-50 p-1.5 rounded-lg border border-gray-200 w-full sm:w-auto justify-center sm:justify-start min-w-0">
            <!-- Excel Export -->
            <form id="exportForm" method="GET" action="/items/export" class="export-form shrink-0">
                <input type="hidden" name="view_type" id="excelExportViewType" value="sku">
                <input type="hidden" name="format" value="excel">
                <button 
                    type="submit"
                    class="px-4 py-2 bg-white text-gray-700 font-medium rounded-md hover:bg-gray-50 transition-all duration-200 flex items-center justify-center shrink-0 border border-gray-300 hover:border-gray-400 min-w-[40px] lg:min-w-[80px]"
                    title="Export to Excel"
                >
                    <i class="fas fa-file-excel text-green-600 text-sm"></i>
                    <span class="ml-2 hidden lg:inline text-sm">Excel</span>
                </button>
            </form>
            
            <!-- CSV Export -->
            <form id="csvExportForm" method="GET" action="/items/export" class="export-form shrink-0">
                <input type="hidden" name="view_type" id="csvExportViewType" value="sku">
                <input type="hidden" name="format" value="csv">
                <button 
                    type="submit"
                    class="px-4 py-2 bg-white text-gray-700 font-medium rounded-md hover:bg-gray-50 transition-all duration-200 flex items-center justify-center shrink-0 border border-gray-300 hover:border-gray-400 min-w-[40px] lg:min-w-[80px]"
                    title="Export to CSV"
                >
                    <i class="fas fa-file-csv text-blue-600 text-sm"></i>
                    <span class="ml-2 hidden lg:inline text-sm">CSV</span>
                </button>
            </form>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2 w-full sm:w-auto justify-center sm:justify-start min-w-0 flex-wrap">
            <!-- Delete Button -->
            <button 
                id="deleteSelectedBtn" 
                class="px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white font-medium rounded-lg hover:from-red-600 hover:to-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center justify-center shrink-0 shadow-sm hover:shadow min-w-[100px] sm:min-w-[120px]"
            >
                <i class="fas fa-trash text-sm"></i>
                <span class="ml-2 text-sm">Delete</span>
            </button>
            
            <!-- Create New Button -->
            <a href="/items-create" class="shrink-0">
                <button 
                    class="px-4 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white font-medium rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 flex items-center justify-center shrink-0 shadow-sm hover:shadow min-w-[100px] sm:min-w-[120px]"
                >
                    <i class="fas fa-plus text-sm"></i>
                    <span class="ml-2 text-sm hidden sm:inline">New Item</span>
                    <span class="ml-2 text-sm sm:hidden">New</span>
                </button>
            </a>

            <!-- Update Stock Button -->
            <form action="{{ route('sku.updateStock') }}" method="POST" class="shrink-0" id="updateStockForm">
                @csrf
                <button 
                    type="submit" 
                    class="px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white font-medium rounded-lg hover:from-indigo-600 hover:to-indigo-700 transition-all duration-200 flex items-center justify-center shrink-0 shadow-sm hover:shadow min-w-[100px] sm:min-w-[130px]"
                >
                    <i class="fas fa-save text-sm"></i>
                    <span class="ml-2 text-sm hidden sm:inline">Update Stock</span>
                    <span class="ml-2 text-sm sm:hidden">Stock</span>
                </button>
            
        </div>
    </div>
</div>

        <!-- Table -->
     <div class="bg-white rounded-xl shadow overflow-hidden border border-gray-200">
    <div class="overflow-x-auto">
     <table id="skuTable" class="w-full divide-y divide-gray-200 table-fixed" style="min-width: 1000px;">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">
                <input type="checkbox" id="check-all-sku" class="h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
            </th>
          <th class="px-3 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-40">Item Admin Codes</th>
 <th class="px-3 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">
                            @php
                                // Get current sort parameters
                                $currentSort = request('sort');
                                $currentDirection = request('direction', 'asc');
                                
                                // Build URL for Item Code sorting
                                if ($currentSort === 'item_code') {
                                    // Same column clicked - toggle direction
                                    $newDirection = $currentDirection === 'asc' ? 'desc' : 'asc';
                                    $sortUrl = url()->current() . '?' . http_build_query([
                                        'sort' => 'item_code',
                                        'direction' => $newDirection
                                    ]);
                                } else {
                                    // Different column - start with ascending
                                    $sortUrl = url()->current() . '?' . http_build_query([
                                        'sort' => 'item_code',
                                        'direction' => 'asc'
                                    ]);
                                }
                                
                                // Determine icon color
                                $iconClass = $currentSort === 'item_code' ? 'text-blue-600' : 'text-gray-400';
                                
                                // Determine icon path
                                if ($currentSort === 'item_code') {
                                    $iconPath = $currentDirection === 'asc' 
                                        ? 'M7 16V4m0 0L3 8m4-4l4 4m10 4V20m0 0l4-4m-4 4l-4-4'  // Ascending
                                        : 'M7 4V16m0 0L3 12m4 4l4-4m10 12V8m0 0l-4 4m4-4l4 4'; // Descending
                                } else {
                                    $iconPath = 'M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4'; // Neutral
                                }
                            @endphp
                            
                            <a href="{{ $sortUrl }}" class="flex items-center justify-between hover:text-blue-600 transition-colors duration-200">
                                <span>Item Code</span>
                                <svg class="h-4 w-4 {{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"></path>
                                </svg>
                            </a>
                        </th>        
                        
                        <th class="px-3 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-40">Jan Code</th>
            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Size</th>
            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Color</th>
            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Size CD</th>
            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Color CD</th>
                       <th class="px-3 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Status</th>

            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">Quantity</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200" id="skuTableBody">
        @foreach ($skus as $sku)
            <tr class="hover:bg-gray-50 transition-colors">
             <td class="px-4 py-4 whitespace-nowrap">
        <input type="checkbox" 
               class="sku-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
               onchange="toggleEdit(this)" value="{{ $sku->Item_AdminCode }}">
    </td>
    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                    {{ $sku->Item_AdminCode ?? '-' }}
                </td>
                
                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    <div class="truncate" title="{{ $sku->Item_Code }}">
                                <a href="{{ route('items.edit', $sku->Item_Code) }}" class="text-blue-600 hover:underline">
                                   {{ $sku->Item_Code }}
                                </a>
                            </div>
                    
                </td>


                   
                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                    {{ $sku->JanCode ?? '-' }}
                </td>

                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">
 <div class="truncate" title="{{ $sku->Size_Name }}">
                                   {{ $sku->Size_Name }}
                                
                            </div>                </td>

                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">
                    <div class="flex items-center">
                        {{-- Optional: Add a small color circle if Color_Code is a Hex --}}
                      <div class="truncate" title="{{ $sku->Color_Name }}">
                                   {{ $sku->Color_Name }}
                                
                            </div> 
                    </div>
                </td>

                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-400">
                    {{ $sku->Size_Code }}
                </td>

                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-400">
                    {{ $sku->Color_Code }}
                </td>

               
                <td class="px-3 py-4 whitespace-nowrap text-center">
                    @if($sku->Quantity > 0)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">In Stock</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Out</span>
                    @endif
                </td>
      <td class="px-3 py-4 whitespace-nowrap text-right ">
    <input type="number" 
           name="quantities[{{ $sku->Item_AdminCode }}]"
           value="{{ $sku->Quantity }}" 
           disabled
           class="qty-input w-32 px-2 py-1 text-right border border-gray-300 rounded bg-gray-50 focus:ring-blue-500 focus:border-blue-500 disabled:opacity-50 disabled:bg-transparent disabled:border-transparent transition-all font-bold {{ $sku->Quantity <= 0 ? 'text-red-500' : 'text-gray-900' }}"
           min="0"
           max="999999999"
           pattern="\d{0,9}"
           oninput="this.value = this.value.toString().slice(0, 9); if(this.value > 999999999) this.value = 999999999;">
</td>

            </tr>
        @endforeach
    </tbody>
</table>

    </div>
</div>
 </form>


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
    
<div id="skuModal"
     class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-start justify-center pt-10">

<div class="bg-white w-full max-w-6xl rounded-xl shadow-lg flex flex-col max-h-[90vh]">
<div class="flex justify-between items-center p-5 border-b">
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
        
<div class="py-6 flex-1 overflow-y-auto custom-padding">
            <div class="overflow-auto" id="matrixContainer">
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
            <button id="closeModalBtn" class="px-5 py-2 custom-margin  bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300">Close</button>
        </div>
    </div>
</div>
  @if($skus->hasPages() || $skus->total() > 0)
                <x-pagination :paginator="$skus" label="sku" />
            @endif
    <!-- JavaScript for functionality -->
<script>
function toggleEdit(checkbox) {
    const row = checkbox.closest('tr');
    const qtyInput = row.querySelector('.qty-input');
    
    if (checkbox.checked) {
        qtyInput.disabled = false;
        qtyInput.classList.remove('bg-transparent', 'border-transparent');
        qtyInput.classList.add('bg-white', 'border-gray-300');
    } else {
        qtyInput.disabled = true;
        qtyInput.classList.add('bg-transparent', 'border-transparent');
        qtyInput.classList.remove('bg-white', 'border-gray-300');
    }
}

// Check-all functionality
document.getElementById('check-all-sku')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.sku-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = this.checked;
        toggleEdit(cb); // Trigger the input enable/disable logic
    });
});




document.getElementById('deleteSelectedBtn').addEventListener('click', async function() {
    // 1. Collect all checked ItemAdminCodes
    const selectedCheckboxes = document.querySelectorAll('.sku-checkbox:checked');
    const adminCodes = Array.from(selectedCheckboxes).map(cb => cb.value);

    // 2. Validation: Don't do anything if nothing is selected
    if (adminCodes.length === 0) {
        alert('Please select at least one item to delete.');
        return;
    }

    // 3. Confirmation
    if (!confirm(`Are you sure you want to delete ${adminCodes.length} items?`)) {
        return;
    }

    try {
        // 4. The Backend Call
const response = await fetch("{{ route('skus.deleteByAdminCode') }}", {            method: 'POST', // Or 'DELETE' if your route is defined as Route::delete
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                'itemAdmin-codes': adminCodes // This matches your PHP $request->input('itemAdmin-codes')
            })
        });

        const result = await response.json();

        if (result.success) {
            alert(result.message);
            // Optional: Refresh the page or remove the rows from the DOM
            location.reload(); 
        } else {
            alert('Error: ' + result.message);
        }

    } catch (error) {
        console.error('Error during deletion:', error);
        alert('An unexpected error occurred.');
    }
});


function getSkuSearchParams() {
    return {
        item_code: document.getElementById("itemCodeSearch").value.trim(),
        item_name: document.getElementById("itemNameSearch").value.trim(),
        jan_code: document.getElementById("janCodeSearch").value.trim(),
        item_admin_code: document.getElementById("adminCodeSearch").value.trim(),
        live: document.getElementById("liveSearchCheckbox").checked ? 1 : 0
    };
}


function executeSkuSearch() {
    const paramsObj = getSkuSearchParams();

    // If all empty → do nothing
    if (
        !paramsObj.item_code &&
        !paramsObj.item_name &&
        !paramsObj.jan_code &&
        !paramsObj.item_admin_code
    ) {
        return;
    }

    // paginationWrapper.style.display = "none";

    const params = new URLSearchParams(paramsObj);

    fetch(`/sku/search?${params.toString()}`)
        .then(res => res.json())
        .then(data => {
            renderSkuRows(data);
        })
        .catch(err => console.error("SKU search error:", err));
}
const tableBody = document.querySelector('#skuTableBody')
function renderSkuRows(items) {
    tableBody.innerHTML = "";

    if (!items || items.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-6 text-gray-500">
                    No SKU found
                </td>
            </tr>
        `;
        return;
    }

    items.forEach(item => {
        const row = document.createElement("tr");
        row.className = "hover:bg-gray-50";

    row.innerHTML = `
<form action="/sku/updateStock" method="POST" class="shrink-0">
    <td class="px-4 py-4 whitespace-nowrap">
        <input type="checkbox" 
               class="sku-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
               onchange="toggleEdit(this)" value="${item.Item_AdminCode}">
    </td>
     <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                    ${item.Item_AdminCode}
                </td>

    <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
        <div class="truncate" title="${item.Item_Code}">
            <a href="/items/${item.Item_Code}/edit" class="text-blue-600 hover:underline">
                ${item.Item_Code}
            </a>
        </div>
    </td>

    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
        ${item.JanCode ?? '-'}
    </td>

    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">
        <div class="truncate" title="${item.Size_Name}">
            ${item.Size_Name}
        </div>
    </td>

    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">
        <div class="flex items-center">
            <div class="truncate" title="${item.Color_Name}">
                ${item.Color_Name}
            </div>
        </div>
    </td>

    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-400">
        ${item.Size_Code}
    </td>

    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-400">
        ${item.Color_Code}
    </td>

    <td class="px-3 py-4 whitespace-nowrap text-center">
        ${parseInt(item.Quantity) > 0 
            ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">In Stock</span>'
            : '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Out</span>'}
    </td>

      <td class="px-3 py-4 whitespace-nowrap text-right ">
    <input type="number" 
           name="quantities[{{ $sku->Item_AdminCode }}]"
           value="{{ $sku->Quantity }}" 
           disabled
           class="qty-input w-32 px-2 py-1 text-right border border-gray-300 rounded bg-gray-50 focus:ring-blue-500 focus:border-blue-500 disabled:opacity-50 disabled:bg-transparent disabled:border-transparent transition-all font-bold {{ $sku->Quantity <= 0 ? 'text-red-500' : 'text-gray-900' }}"
           min="0"
           max="999999999"
           pattern="\d{0,9}"
           oninput="this.value = this.value.toString().slice(0, 9); if(this.value > 999999999) this.value = 999999999;">
</td>

</form>
`;



        tableBody.appendChild(row);
    });
}



const itemCodeInput = document.getElementById("itemCodeSearch");
const itemNameInput = document.getElementById("itemNameSearch");
const JanCodeInput =document.getElementById("janCodeSearch")
const adminCodeInput = document.getElementById("adminCodeSearch");
const searchBtn = document.getElementById("searchButton");

let hasTyped = false;
let hasSearched = false;

searchBtn.addEventListener("click", () => {
    const itemCode = itemCodeInput.value.trim();
    const itemName = itemNameInput.value.trim();
    const jancode = JanCodeInput.value.trim();
    const adminCode = adminCodeInput.value.trim();



    if (!itemCode && !itemName && !jancode && !adminCode) {
        window.location.reload();
        return;
    }

   hasTyped = true;
    hasSearched = true;
        executeSkuSearch(); // 🔥 REQUIRED

});

[
    document.getElementById("itemCodeSearch"),
    document.getElementById("itemNameSearch"),
    document.getElementById("janCodeSearch"),
    document.getElementById("adminCodeSearch")
].forEach(input => {
    input.addEventListener("input", () => {
         if (!hasSearched) return;
        // if(!isExporting)return;

        const itemCode = itemCodeInput.value.trim();
        const itemName = itemNameInput.value.trim();
         const jancode = JanCodeInput.value.trim();
    const adminCode = adminCodeInput.value.trim();

    if (!itemCode && !itemName && !jancode && !adminCode) {
            window.location.reload();
        }
        hasTyped = !!(itemCode || itemName);

        // If user changes input after searching → require search again
        if (hasTyped) {
            hasSearched = false;
        }
    });

});
document.querySelectorAll(".export-form").forEach(form => {
    form.addEventListener("submit", function (e) {
        // typed but not searched
        if (hasTyped && !hasSearched) {
            e.preventDefault();
            alert("Please click Search before exporting.");
            return;
        }

        const rows = document.querySelectorAll("#skuTableBody tr");
        if (rows.length === 0) {
            e.preventDefault();
            alert("No data found to export.");
            return;
        }
    });
});


document.querySelectorAll(".export-form").forEach(form => {
    form.addEventListener("submit", function (e) {
        e.preventDefault();


        const itemCode = document.getElementById("itemCodeSearch")?.value.trim() || "";
        const itemName = document.getElementById("itemNameSearch")?.value.trim() || "";
         const janCode   = document.getElementById("janCodeSearch")?.value.trim() || "";
         const adminCode = document.getElementById("adminCodeSearch")?.value.trim() || "";
        const live     = document.getElementById("liveSearchCheckbox")?.checked ? 1 : 0;

        upsertHidden(this, "item_code", itemCode);
        upsertHidden(this, "item_name", itemName);
        upsertHidden(this,"jan_code",janCode);
        upsertHidden(this,"admin_code",adminCode)
        upsertHidden(this, "live", live);

        const rows = document.querySelectorAll("#skuTableBody tr");
        const visibleRows = Array.from(rows).filter(row =>
            row.offsetWidth > 0 && row.offsetHeight > 0
        );

        /* ✅ ITEM export validation */
      
    // ITEM export needs table rows
    if (visibleRows.length === 0) {
        alert("No data found to export.");
        return;
    }

   const validSkuRows = visibleRows.filter(row => {
    const checkbox = row.querySelector(".sku-checkbox");
    return checkbox && checkbox.value?.trim();
});


    if (validSkuRows.length === 0) {
        alert("No data found. Please try again.");
        return;
    }

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


document.getElementById("searchButton")?.addEventListener("click", () => {
    const itemCode  = document.getElementById("itemCodeSearch")?.value.trim();
    const itemName  = document.getElementById("itemNameSearch")?.value.trim();
    const janCode   = document.getElementById("janCodeSearch")?.value.trim();
    const adminCode = document.getElementById("adminCodeSearch")?.value.trim();

    if (!itemCode && !itemName && !janCode && !adminCode) {
        return;
    }

    skuHasTyped = true;
    skuHasSearched = true;

    executeSkuSearch();
});
const updateStockForm = document.getElementById('updateStockForm');

updateStockForm.addEventListener('submit', function(e) {
    e.preventDefault(); // prevent default submit

    // Remove previous hidden inputs added dynamically
    updateStockForm.querySelectorAll('input[name^="quantities"]').forEach(input => input.remove());

    // Find all quantity inputs from table
    const qtyInputs = document.querySelectorAll('.qty-input');

    qtyInputs.forEach(input => {
        if (!input.disabled) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = input.name; // quantities[Item_AdminCode]
            hidden.value = input.value;
            updateStockForm.appendChild(hidden);
        }
    });

    // Submit the form
    this.submit();
});
   //09 -Feb-2026



</script>
</body>
</html>