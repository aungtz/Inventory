<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Details Log</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Simple table styling */
        .table-container {
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .sticky-header th {
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        /* Scrollbar */
        .table-container::-webkit-scrollbar {
            width: 6px;
        }
        
        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .table-container::-webkit-scrollbar-thumb {
            background: #888;
        }

                /* 1. The Container - MUST allow overflow for tooltips to be seen */
        .table-container {
            overflow: visible !important; 
            padding-bottom: 60px; /* Space for tooltips on the bottom row */
        }

        /* 2. The Cell - Anchor for the tooltip */
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

        }

        /* 5. Triggering visibility */
        .tooltip-cell.truncated:hover::after {
            display: block;
        }

        /* 6. Change cursor only for truncated cells */
        .tooltip-cell.truncated {
            cursor: pointer;
        }

        tr:hover {
            position: relative;
            z-index: 100; /* Makes the hovered row float above others */
        }
        /* Fix tooltip overflow for last column */
.tooltip-cell.truncated:last-child::after {
    left: auto;
    right: 0;
}

.tooltip-cell.truncated::after {
    top: 100%;
    left: 0;
    transform: translateY(-6px);
}

    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    @include('layout.sidebar')
    <main class="container mx-auto px-4 py-6">
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Item Error Log</h1>
                    <!-- <p class="text-gray-600">Import ID: IMP-2024-001-ITEM | Date: 2024-01-15 10:30 AM</p> -->
                </div>
                <a href="/import-log" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Import Log
                </a>
            </div>
            
            <!-- Summary -->
            <div class="bg-white rounded-lg p-4 shadow mb-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Total Items</p>
                    <p class="text-lg font-semibold">{{ $items->total() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Import Status</p>
                        <p class="text-lg font-semibold text-green-600">Items</p>

                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Imported By</p>
                        <p class="text-lg font-semibold">{{ $log->Imported_By }}</p>

                    </div>
                      
                          <div>
                        <p class="text-sm text-gray-600">Created At</p>
                <p class="text-lg font-semibold">
                    {{ $log->Imported_Date?->format('Y-m-d H:i') ?? '-' }}
                </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Item Details Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Imported Items</h2>
            </div>
            
            <div class="overflow-x-auto">
    <table class="w-full min-w[800p] border-separate border-spacing-0">
                    <thead class="bg-gray-100 ">
                        <tr>
                            <th class="p-3 text-left font-medium text-gray-700">Item_Code</th>
                            <th class="p-3 text-left font-medium text-gray-700">Item_Name</th>
                            <th class="p-3 text-left font-medium text-gray-700">JanCD</th>
                            <th class="p-3 text-left font-medium text-gray-700">MakerName</th>
                            <th class="p-3 text-left font-medium text-gray-700">Memo</th>
                            <th class="p-3 text-left font-medium text-gray-700">ListPrice</th>
                            <th class="p-3 text-left font-medium text-gray-700">SalePrice</th>
                                                         <th class="p-3 text-left font-medium text-gray-700">Error Message</th>

                        </tr>
                    </thead>
              @foreach($items as $item)
<tr class="border-b hover:bg-gray-50">
    <!-- Item_Code -->
    <td class="p-3 whitespace-nowrap overflow-hidden text-ellipsis max-w-[8rem]" 
        title="{{ $item->Item_Code }}">
        <span class="font-mono">{{ $item->Item_Code }}</span>
    </td>
    
    <!-- Item_Name -->
    <td class="p-3 whitespace-nowrap overflow-hidden text-ellipsis max-w-[8rem]" 
        title="{{ $item->Item_Name }}">
        <span>{{ $item->Item_Name }}</span>
    </td>

    <!-- JanCD -->
    <td class="p-3 whitespace-nowrap overflow-hidden text-ellipsis max-w-[8rem]" 
        title="{{ $item->JanCD }}">
        <span class="font-mono">{{ $item->JanCD }}</span>
    </td>

    <!-- MakerName -->
    <td class="p-3 whitespace-nowrap overflow-hidden text-ellipsis max-w-[8rem]" 
        title="{{ $item->MakerName }}">
        <span>{{ $item->MakerName }}</span>
    </td>

    <!-- Memo -->
    <td class="p-3 whitespace-nowrap overflow-hidden text-ellipsis max-w-[8rem]" 
        title="{{ $item->Memo }}">
        <span>{{ $item->Memo }}</span>
    </td>

    <!-- ListPrice -->
    <td class="p-3 whitespace-nowrap overflow-hidden text-ellipsis max-w-[8rem] text-right" 
        title="        {{ number_format($item->ListPrice, 0) }}
">
        <span>        {{ number_format($item->ListPrice, 0) }}
</span>
    </td>

    <!-- SalePrice -->
    <td class="p-3 whitespace-nowrap overflow-hidden text-ellipsis max-w-[8rem] text-right" 
        title="        {{ number_format($item->SalePrice, 0) }}
">
        <span>        {{ number_format($item->SalePrice, 0) }}
</span>
    </td>

    <!-- Error Message -->
    <td class="p-3 whitespace-nowrap overflow-hidden text-ellipsis max-w-[8rem]" 
        title="{{ $item->Error_Msg }}">
        <span class="font-semibold {{ $item->Status == 'Valid' ? 'text-green-600' : 'text-red-600' }}">
            {{ $item->Error_Msg }}
        </span>
    </td>
</tr>
@endforeach
</tbody>
                </table>
            </div>
            
            <!-- Pagination -->
                @if($items->hasPages() || $items->total() > 0)
        <x-pagination :paginator="$items" label="items" />
                 @endif
        </div>
    </main>
</body>
<script>
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
//04-feb-2026 Fixed Update
</script>
</html>