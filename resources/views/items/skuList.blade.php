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
<div class="flex flex-wrap justify-between items-start gap-y-4 gap-x-6 mb-6">
    <!-- Search Section - Now can wrap properly -->
    <div class="flex-1 min-w-[300px] lg:min-w-[500px] xl:min-w-[600px]">
        <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
            <!-- Live Search Checkbox -->
            <div class="flex items-center space-x-2 shrink-0">
                <input 
                    type="checkbox" 
                    id="liveSearchCheckbox" 
                    name="live_search"
                    checked
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                >
                <label for="liveSearchCheckbox" class="text-sm font-medium text-gray-700 whitespace-nowrap">
                    Like Search
                </label>
            </div>

            <!-- Search textareas with button -->
            <div class="flex-1 min-w-0 w-full">
                <div class="flex flex-col md:flex-row items-start md:items-center gap-3 w-full">
                    <!-- Textareas container with enforced minimum -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 flex-1 min-w-0 w-full min-w-[300px]">
                        <!-- Item Code Search -->
                        <div class="relative min-w-0">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <!-- <i class="fas fa-hashtag text-gray-400 text-sm"></i> -->
                            </div>
                            <textarea 
                                id="itemCodeSearch"
                                name="item_code_search"
                                placeholder="Item Code Search"
                                rows="1"
                                class="w-full min-w-[140px] pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 resize-y text-sm min-h-[42px]"
                            ></textarea>
                        </div>
                        
                        <!-- Item Name Search -->
                        <div class="relative min-w-0">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <!-- <i class="fas fa-tag text-gray-400 text-sm"></i> -->
                            </div>
                            <textarea 
                                id="itemNameSearch"
                                name="item_name_search"
                                placeholder="Item Name Search"
                                rows="1"
                                class="w-full min-w-[140px] pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 resize-y text-sm min-h-[42px]"
                            ></textarea>
                        </div>
                    </div>
                    
                    <!-- Search Button -->
                    <div class="shrink-0">
                        <button 
                            type="button"
                            id="searchButton"
                            class="px-4 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition flex items-center justify-center whitespace-nowrap h-[42px] w-full md:w-auto"
                        >
                            <i class="fas fa-search"></i>
                            <span class="hidden md:inline ml-2">Search</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons Section - Will wrap when needed -->
    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center shrink-0">
        <!-- View Type Toggle -->
        <!-- <div class="flex items-center space-x-2 bg-gray-100 rounded-lg p-1 shrink-0">
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
        </div> -->

        <!-- Export & Action Buttons Group -->
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Export Buttons Group -->
            <div class="flex gap-2">
                <!-- Excel Export Form -->
                <form id="exportForm" method="GET" action="/items/export" class="export-form">
                    <input type="hidden" name="view_type" id="excelExportViewType" value="sku">
                    <input type="hidden" name="format" value="excel">
                    <button 
                        type="submit"
                        class="px-4 py-2.5 border border-gray-300 bg-white text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition flex items-center justify-center shrink-0"
                    >
                        <i class="fas fa-file-excel mr-2 text-green-600"></i>
                        <span>Excel</span>
                    </button>
                </form>
                
                <!-- CSV Export Form -->
                <form id="csvExportForm" method="GET" action="/items/export" class="export-form">
                    <input type="hidden" name="view_type" id="csvExportViewType" value="sku">
                    <input type="hidden" name="format" value="csv">
                    <button 
                        type="submit"
                        class="px-4 py-2.5 border border-gray-300 bg-white text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition flex items-center justify-center shrink-0"
                    >
                        <i class="fas fa-file-csv mr-2 text-blue-600"></i>
                        <span>CSV</span>
                    </button>
                </form>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <!-- Delete Button -->
                <button 
                    id="deleteSelectedBtn" 
                    class="px-4 py-2.5 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center justify-center shrink-0"
                >
                    <i class="fas fa-trash mr-2"></i>
                    <span class="hidden sm:inline">Delete Selected</span>
                    <span class="sm:hidden">Delete</span>
                </button>
                
                <!-- Create New Button -->
                <a href="/items-create" class="shrink-0">
                    <button 
                        class="px-4 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition flex items-center justify-center shrink-0"
                    >
                        <i class="fas fa-plus mr-2"></i>
                        <span class="hidden sm:inline">Create New</span>
                        <span class="sm:hidden">New</span>
                    </button>
                </a>
               <form action="{{ route('sku.updateStock') }}" method="POST">
             @csrf
             {{-- Your existing Update Button --}}
            <div class="mb-4 flex justify-end">
                <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition flex items-center shadow-md">
                    <i class="fas fa-save mr-2"></i>
                    <span>Update Stock</span>
                </button>
            </div>
   
            </div>
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
            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Item Code</th>
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
               onchange="toggleEdit(this)" value{{ $sku->Item_AdminCode }}>
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
               <td class="px-3 py-4 whitespace-nowrap text-right">
        <input type="number" 
name="quantities[{{ $sku->Item_AdminCode }}]"
               value="{{ $sku->Quantity }}" 
               disabled
               class="qty-input w-24 px-2 py-1 text-right border border-gray-300 rounded bg-gray-50 focus:ring-blue-500 focus:border-blue-500 disabled:opacity-50 disabled:bg-transparent disabled:border-transparent transition-all font-bold {{ $sku->Quantity <= 0 ? 'text-red-500' : 'text-gray-900' }}"
               min="0">
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
</script>
</body>
</html>