<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKU Details Log</title>
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
        
        /* Size and color indicators */
        .size-badge {
            display: inline-block;
            padding: 2px 8px;
            background: #e5e7eb;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .color-badge {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 6px;
            vertical-align: middle;
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

        /* Tooltip css */
        .truncate-cell {
    position: relative;
    max-width: 150px; /* Adjust based on your table layout */
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.truncate-content {
    display: inline-block;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    vertical-align: middle;
}

/* Tooltip styles */
.truncate-cell::before {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: #1f2937;
    color: white;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 12px;
    white-space: normal;
    max-width: 300px;
    word-wrap: break-word;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.2s, visibility 0.2s;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    pointer-events: none;
}

.truncate-cell:hover::before {
    opacity: 1;
    visibility: visible;
}

/* Custom badges styling */
.size-badge {
    display: inline-block;
    padding: 2px 8px;
    background-color: #e5e7eb;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.color-badge {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    margin-right: 8px;
    display: inline-block;
    flex-shrink: 0;
    border: 1px solid #d1d5db;
}

/* Adjust for cells with flex content */
.truncate-cell .flex {
    min-width: 0; /* Allow flex container to shrink */
}

.truncate-cell .flex .truncate-content {
    flex: 1;
    min-width: 0; /* Allow text to truncate properly in flex */
}

/* Alternative: Tooltip that appears on the side */
.truncate-cell.alternate-tooltip::before {
    left: auto;
    right: 100%;
    top: 50%;
    transform: translateY(-50%);
    bottom: auto;
    margin-right: 8px;
}

/* Ensure table cells have consistent height */
td {
    vertical-align: middle;
}

/* Make sure the tooltip doesn't overflow table container */
.table-container {
    position: relative;
}

/* For very long content */
.truncate-cell.long-content {
    max-width: 200px;
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
                    <h1 class="text-2xl font-bold text-gray-800">SKU Details Log</h1>
                    <p class="text-gray-600">Import ID: IMP-2024-002-SKU | Date: 2024-01-14 03:45 PM</p>
                </div>
                <a href="/import-log" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Import Log
                </a>
            </div>
            
            <!-- Summary -->
            <div class="bg-white rounded-lg p-4 shadow mb-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Total SKUs</p>
                        <p class="text-lg font-semibold">{{ $items->count() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Import Status</p>
                        <p class="text-lg font-semibold text-green-600">SKU</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Imported By</p>
                        <p class="text-lg font-semibold">System</p>
                    </div>
                  
                </div>
            </div>
        </div>

        <!-- SKU Details Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Imported SKUs</h2>
            </div>
            
           <div class="table-container">
    <table class="w-full">
        <thead class="bg-gray-100 sticky-header">
            <tr>
                <th class="p-3 text-left">Item Code</th>
                <th class="p-3 text-left">Size</th>
                <th class="p-3 text-left">Color</th>
                <th class="p-3 text-left">Size Code</th>
                <th class="p-3 text-left">Color Code</th>
                <th class="p-3 text-left">JAN Code</th>
                <th class="p-3 text-left">Quantity</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($items as $item)
            <tr class="hover:bg-gray-50">
                <td class="p-3 font-mono truncate-cell" title="{{ $item->Item_Code }}">
                    <span class="truncate-content">{{ $item->Item_Code }}</span>
                </td>
                <td class="p-3 truncate-cell" title="{{ $item->SizeName }}">
                    <span class="size-badge truncate-content">{{ $item->SizeName }}</span>
                </td>
                <td class="p-3 truncate-cell" title="{{ $item->ColorName }}">
                    <div class="flex items-center">
                        <span class="color-badge" style="background-color: #{{ $item->ColorCode ?? 'ccc' }}"></span>
                        <span class="truncate-content">{{ $item->ColorName }}</span>
                    </div>
                </td>
                <td class="p-3 font-mono truncate-cell" title="{{ $item->SizeCode }}">
                    <span class="truncate-content">{{ $item->SizeCode }}</span>
                </td>
                <td class="p-3 font-mono truncate-cell" title="{{ $item->ColorCode }}">
                    <span class="truncate-content">{{ $item->ColorCode }}</span>
                </td>
                <td class="p-3 font-mono truncate-cell" title="{{ $item->JanCD }}">
                    <span class="truncate-content">{{ $item->JanCD }}</span>
                </td>
                <td class="p-3 font-medium truncate-cell" title="{{ $item->Quantity }}">
                    <span class="truncate-content">{{ $item->Quantity }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<style>
/* Base truncation styles */

</style>

<script>
// Optional JavaScript to automatically add titles to cells with truncated content
document.addEventListener('DOMContentLoaded', function() {
    const cells = document.querySelectorAll('.truncate-cell');
    
    cells.forEach(cell => {
        const content = cell.querySelector('.truncate-content') || cell;
        const isTruncated = content.scrollWidth > cell.clientWidth;
        
        // Only show tooltip if content is actually truncated
        if (!isTruncated) {
            cell.removeAttribute('title');
            cell.classList.remove('truncate-cell');
        }
    });
});
</script>
            
            <!-- Pagination -->
            <!-- <div class="p-4 border-t">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        Showing 1 to 10 of 2,845 SKUs
                    </div>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 border rounded text-sm">Previous</button>
                        <button class="px-3 py-1 bg-blue-600 text-white rounded text-sm">1</button>
                        <button class="px-3 py-1 border rounded text-sm">2</button>
                        <button class="px-3 py-1 border rounded text-sm">3</button>
                        <button class="px-3 py-1 border rounded text-sm">Next</button>
                    </div>
                </div>
            </div> -->
        </div>
    </main>
</body>
</html>