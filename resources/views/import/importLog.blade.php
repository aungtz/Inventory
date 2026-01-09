<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Log | Product Management System</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons (optional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Smooth transitions */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        
        /* Focus styles */
        :focus {
            outline: none;
        }
        
        .focus\:ring-2:focus {
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation Header -->

   @include('layout.sidebar')

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Import Log
                    </h1>
                    <p class="text-gray-600 mt-2">Track and manage your import history</p>
                </div>
                @if(request()->has('success'))
                <div class="p-4 mb-4 text-green-800 bg-green-100 border border-green-300 rounded-lg" id="success">
                    ✅ Import completed successfully!
                </div>
                @endif

                
                <!-- Import Type Buttons -->
                <div class="flex flex-wrap gap-3">
                    <a href="/item-master/import" 
                       class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl font-medium hover:from-indigo-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Item Master Import
                    </a>
                    
                    <a href="/sku-master/import" 
                       class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl font-medium hover:from-purple-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        SKU Master Import
                    </a>
                </div>
            </div>
            
            <!-- Stats Cards -->
          
        </div>

        <!-- Import Log Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200/80 overflow-hidden">
            <!-- Table Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Import History</h2>
                        <p class="text-gray-600 text-sm mt-1">Recent import activities and their status</p>
                    </div>
                    
                    <!-- Filters -->
                    <!-- <div class="flex flex-wrap gap-3">
                        <select class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400">
                            <option>All Data Types</option>
                            <option>Item Master</option>
                            <option>SKU Master</option>
                        </select>
                        
                        <select class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400">
                            <option>All Status</option>
                            <option>Success</option>
                            <option>With Errors</option>
                            <option>Failed</option>
                        </select>
                        
                        <input type="text" placeholder="Search imports..." 
                               class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 w-full md:w-48 hover:border-gray-400">
                    </div> -->
                </div>
            </div>
            
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white">
                        <tr>
                            <th class="p-4 text-left font-semibold">Detailed</th>
                            <th class="p-4 text-left font-semibold">Create Date</th>
                            <th class="p-4 text-left font-semibold">Manager</th>
                            <th class="p-4 text-left font-semibold">Datatype</th>
                            <th class="p-4 text-left font-semibold">Number of Valid</th>
                            <th class="p-4 text-left font-semibold">Number of Errors</th>
                            <th class="p-4 text-left font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <!-- Row 1 -->
           
                        
                       @foreach($logs as $log)
                                   @php
    // Determine which routes to use based on Import_Type
    $detailsRoute = $log->Import_Type == 1 
        ? route('item-details', $log->ImportLog_ID) 
        : route('sku-details', $log->ImportLog_ID);

    $errorsRoute = $log->Import_Type == 1
        ? route('item-errors', $log->ImportLog_ID)
        : route('sku-errors', $log->ImportLog_ID);
@endphp
<tr class="hover:bg-gray-50/50 transition-all duration-200">
    <!-- Detail button -->
    <td class="p-4">
        <a href="{{ $detailsRoute }}"
     class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-indigo-50 hover:text-indigo-600">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
        </svg>
        Detail
    </a>

    </td>

    <!-- Date & time -->
    <td class="p-4 text-gray-700">
        <div class="font-medium">{{ $log->Imported_Date->format('Y-m-d') }}</div>
        <!-- <div class="text-sm text-gray-500">{{ $log->Imported_Date->format('h:i A') }}</div> -->
    </td>

    <!-- Imported by -->
    <td class="p-4">
        <span class="font-medium text-indigo-600">{{ $log->Imported_By }}</span>
    </td>

    <!-- Import type -->
    <td class="p-4">
        <span class="inline-flex items-center px-3 py-1 
            @if($log->Import_Type == 1)
                bg-indigo-100 text-indigo-800
            @else
                bg-purple-100 text-purple-800
            @endif
            rounded-full text-sm font-medium">
            {{ $log->Import_Type == 1 ? 'Item Master' : 'SKU Master' }}
        </span>
    </td>

    <!-- Record Count -->
    <td class="p-4">
        <a href="{{ $detailsRoute }}">  <span class="font-medium text-gray-800">{{ number_format($log->Record_Count) }} items</span></a>
    </td>

    <!-- Error Count -->
    <td class="p-4">
       <a href="{{  $errorsRoute }}"> <span class="font-medium text-red-600">{{ $log->Error_Count }} errors</span></a>
    </td>

    <!-- Status -->
    <td class="p-4">
        @if($log->Error_Count == 0)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                Success
            </span>
        @else
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-yellow-800">
                <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span>
                With Errors
            </span>
        @endif
    </td>
</tr>
@endforeach

                       
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <!-- <div class="p-6 border-t border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="text-sm text-gray-600">
                    Showing <span class="font-medium">1</span> to <span class="font-medium">5</span> of <span class="font-medium">125</span> imports
                </div>
                <div class="flex items-center space-x-2">
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors duration-200 hover:border-gray-400">
                        Previous
                    </button>
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors duration-200">
                        1
                    </button>
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors duration-200 hover:border-gray-400">
                        2
                    </button>
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors duration-200 hover:border-gray-400">
                        3
                    </button>
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors duration-200 hover:border-gray-400">
                        Next
                    </button>
                </div>
            </div> -->
        </div>
        
        <!-- Quick Tips -->
        <!-- <div class="mt-8 p-6 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl border border-indigo-100 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-indigo-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="font-semibold text-gray-800 mb-2">Import Log Tips</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Click on the <strong>Detail</strong> button to view complete import details</li>
                        <li>• Click on <strong>Manager names</strong> to view their profile</li>
                        <li>• Click on <strong>Data Type</strong> to go to the import page for that type</li>
                        <li>• Click on <strong>Number of Data Items</strong> to see all imported items</li>
                        <li>• Click on <strong>Number of Errors</strong> to view error details</li>
                        <li>• Use filters to find specific imports by date, type, or status</li>
                    </ul>
                </div>
            </div>
        </div> -->
    </main>

    <!-- Footer -->
    <!-- JavaScript for interactivity -->
    <script>
        // Simple JavaScript for demo interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add click handlers to all links for demo purposes
            const allLinks = document.querySelectorAll('a');
            allLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.getAttribute('href') === '#') {
                        e.preventDefault();
                        alert('This is a demo link. In a real application, this would navigate to the appropriate page.');
                    }
                });
            });
            
            // Add hover effects to table rows
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
            
            // Filter functionality (basic)
            const filterSelects = document.querySelectorAll('select');
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    console.log('Filter changed:', this.value);
                    // In a real app, you would filter the table here
                });
            });
            
            // Search functionality
            const searchInput = document.querySelector('input[type="text"]');
            if (searchInput) {
                searchInput.addEventListener('keyup', function(e) {
                    if (e.key === 'Enter') {
                        console.log('Searching for:', this.value);
                        // In a real app, you would search the table here
                    }
                });
            }
            
            // Pagination button handlers
            const paginationButtons = document.querySelectorAll('.pagination button');
            paginationButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const buttonText = this.textContent.trim();
                    alert(`Pagination: ${buttonText} button clicked`);
                    // In a real app, you would handle pagination here
                });
            });
        });
         setTimeout(() => {
        const alertBox = document.getElementById('success');
        if (alertBox) {
            alertBox.style.transition = "opacity 0.5s ease";
            alertBox.style.opacity = "0";

            // Remove it completely after fade-out
            setTimeout(() => alertBox.remove(), 500);
        }
    }, 5000); 
    </script>
</body>
</html>