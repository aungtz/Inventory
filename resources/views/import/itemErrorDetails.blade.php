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
                    <p class="text-lg font-semibold">{{ $items->count() }}</p>
                    </div>
                    <!-- <div>
                        <p class="text-sm text-gray-600">Import Status</p>
                        <p class="text-lg font-semibold text-green-600">{{ $log->Import_Status }}</p>

                    </div> -->
                    <div>
                        <p class="text-sm text-gray-600">Imported By</p>
                        <p class="text-lg font-semibold">{{ $log->Imported_By }}</p>

                    </div>
                        <!-- <div>
                            <p class="text-sm text-gray-600">File Name</p>
                            <p class="text-lg font-semibold">{{ $log->File_Name }}</p>

                        </div> -->
                </div>
            </div>
        </div>

        <!-- Item Details Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Imported Items</h2>
            </div>
            
            <div class="table-container">
                <table class="w-full">
                    <thead class="bg-gray-100 sticky-header">
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
<tr class="hover:bg-gray-50">
    
  
    <td class="p-3">{{ $item->Item_Code }}</td>
    <td class="p-3">{{ $item->Item_Name }}</td>
    <td class="p-3">{{ $item->JanCD }}</td>
    <td class="p-3">{{ $item->MakerName }}</td>
    <td class="p-3">{{ $item->Memo }}</td>
    <td class="p-3">{{ $item->ListPrice }}</td>
    <td class="p-3">{{ $item->SalePrice }}</td>
      {{-- Status column --}}
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
                @if($items->hasPages() || $items->total() > 0)
        <x-pagination :paginator="$items" label="items" />
                 @endif
        </div>
    </main>
</body>
</html>