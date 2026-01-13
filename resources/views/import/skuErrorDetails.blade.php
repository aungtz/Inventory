<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKU Error Log</title>
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
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
        @include('layout.sidebar')

    <main class="container mx-auto px-4 py-6">
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">SKU Error Log</h1>
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
                    <!-- <div>
                        <p class="text-sm text-gray-600">Import Status</p>
                        <p class="text-lg font-semibold text-green-600">Completed</p>
                    </div> -->
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
                                <th class="p-3 text-left">Error Message</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <!-- Row 1 -->
                        @foreach ($items as $item)
          <tr class="hover:bg-gray-50">
                <td class="p-3 font-mono">{{ $item->Item_Code }}</td>
                <td class="p-3"><span class="size-badge">{{ $item->SizeName }}</span></td>
                <td class="p-3">
                    <div class="flex items-center" >
                        <span class="color-badge" style="background-color: #{{ $item->ColorCode ?? 'ccc' }}"></span>
                        {{ $item->ColorName }}
                    </div>
                </td>
                <td class="p-3 font-mono">{{ $item->SizeCode }}</td>
                <td class="p-3 font-mono">{{ $item->ColorCode }}</td>
                <td class="p-3 font-mono">{{ $item->JanCD }}</td>
                <td class="p-3 font-medium">{{ $item->Quantity }}</td>
<td class="p-3 font-semibold 
        {{ $item->Status == 'Valid' ? 'text-green-600' : 'text-red-600' }}">
        {{ $item->Error_Msg }}
    </td>
            </tr>
        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
                  <!-- Pagination -->
                @if($items->hasPages() || $items->total() > 0)
        <x-pagination :paginator="$items" label="items" />
                 @endif
        </div>
    </main>
</body>
</html>