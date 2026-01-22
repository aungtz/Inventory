<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKU Master Import</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* File upload custom styling */
        .file-upload {
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }
        
        .file-upload input[type="file"] {
            position: absolute;
            top: 0;
            right: 0;
            min-width: 100%;
            min-height: 100%;
            font-size: 100px;
            text-align: right;
            filter: alpha(opacity=0);
            opacity: 0;
            outline: none;
            background: white;
            cursor: pointer;
            display: block;
        }
        
        /* Drag and drop area styling */
        .drag-drop-area {
            border: 2px dashed #cbd5e0;
            transition: all 0.3s ease;
        }
        
        .drag-drop-area.dragover {
            border-color: #667eea;
            background-color: rgba(102, 126, 234, 0.05);
        }
        
        /* Progress bar animation */
        @keyframes progress {
            0% { width: 0%; }
            100% { width: 100%; }
        }
        
        .progress-bar {
            animation: progress 2s ease-in-out;
        }
        
        /* File list item hover effect */
        .file-item:hover {
            background-color: #f7fafc;
            transform: translateX(4px);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Main Content Area -->
        <!-- Sidebar will be included here -->
        @include('layout.sidebar')
        
        <!-- Main Content -->
        <main class="flex-1 p-6">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">
                            SKU Master Import
                        </h1>
                        <p class="text-gray-600 mt-2">Import SKU data from Excel or CSV files</p>
                    </div>
                    
                    <!-- Back Button -->
                    <a href="/import-log" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-all duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Import Log
                    </a>
                </div>
            </div>

            <!-- Import Form Card -->
            <div class="max-w-4xl mx-auto">
                <!-- Form Card -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/80 overflow-hidden">
                    <!-- Card Header -->
                    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-indigo-50">
                        <div class="flex items-center">
                            <div class="p-3 rounded-xl bg-purple-100 mr-4">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">Upload SKU Master File</h2>
                                <p class="text-gray-600 text-sm mt-1">Supported formats: Excel (.xlsx, .xls) and CSV (.csv)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <div class="p-6">
                        <!-- File Format Selection -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Select File Format</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Excel Upload Card -->
                                <div class="border border-gray-300 rounded-2xl p-6 hover:border-purple-400 hover:shadow-lg transition-all duration-300 cursor-pointer file-upload-card" data-format="excel">
                                    <div class="flex flex-col items-center text-center">
                                        <div class="p-4 rounded-xl bg-green-100 mb-4">
                                            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <h4 class="text-lg font-bold text-gray-800 mb-2">Excel Import</h4>
                                        <p class="text-gray-600 text-sm mb-4">Upload Excel files (.xlsx, .xls)</p>
                                        <div class="file-upload">
                                            <button type="button" class="px-6 py-3 bg-green-500 text-white rounded-xl font-medium hover:bg-green-600 transition-all duration-300 transform hover:scale-105">
                                                <i class="fas fa-file-excel mr-2"></i>
                                                Choose Excel File
                                            </button>
                                            <input type="file" id="excelFile" accept=".xlsx,.xls" class="hidden">
                                        </div>
                                    </div>
                                </div>

                                <!-- CSV Upload Card -->
                                <div class="border border-gray-300 rounded-2xl p-6 hover:border-blue-400 hover:shadow-lg transition-all duration-300 cursor-pointer file-upload-card" data-format="csv">
                                    <div class="flex flex-col items-center text-center">
                                        <div class="p-4 rounded-xl bg-blue-100 mb-4">
                                            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <h4 class="text-lg font-bold text-gray-800 mb-2">CSV Import</h4>
                                        <p class="text-gray-600 text-sm mb-4">Upload CSV files (.csv)</p>
                                        <div class="file-upload">
                                            <button type="button" class="px-6 py-3 bg-blue-500 text-white rounded-xl font-medium hover:bg-blue-600 transition-all duration-300 transform hover:scale-105">
                                                <i class="fas fa-file-csv mr-2"></i>
                                                Choose CSV File
                                            </button>
                                            <input type="file" id="csvFile" accept=".csv" class="hidden">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Drag & Drop Area -->
                    
                        <!-- Selected File Display -->
                        <div class="mb-8" id="fileDisplay" style="display: none;">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Selected File</h3>
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="p-3 rounded-lg bg-purple-100 mr-4">
                                            <i class="fas fa-file text-purple-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-800" id="fileName">No file selected</h4>
                                            <p class="text-gray-500 text-sm" id="fileSize">-</p>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="button" id="removeFile" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200">
                                            <i class="fas fa-times mr-1"></i>
                                            Remove
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Progress Bar (hidden by default) -->
                                <div class="mt-4" id="uploadProgress" style="display: none;">
                                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                                        <span>Uploading...</span>
                                        <span id="progressPercent">0%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div id="progressBar" class="bg-purple-600 h-2 rounded-full progress-bar" style="width: 0%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Import Options -->
                        

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-4">
                            <button type="button" id="cancelBtn" class="px-8 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-all duration-300">
                                Cancel
                            </button>
                            <button type="button" id="submitBtn" class="px-8 py-3 bg-gradient-to-r from-purple-600 to-purple-800 text-white rounded-xl font-medium hover:from-purple-700 hover:to-purple-900 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center">
                                <i class="fas fa-upload mr-2"></i>
                                Start Import
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Information Panel -->
                <!-- <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-100 p-6">
                    <div class="flex items-start">
                        <div class="p-3 rounded-xl bg-blue-100 mr-4">
                            <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">Import Guidelines</h3>
                            <ul class="text-sm text-gray-600 space-y-2">
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    <span>Excel files should have columns: <strong>Size Name, Color Name, Size Code, Color Code, JAN Code, Qty-flag, Stock Quantity</strong></span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    <span>CSV files should use comma (,) as delimiter and UTF-8 encoding</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    <span>Maximum file size: <strong>50MB</strong></span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    <span>First row should contain column headers</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-download text-blue-500 mr-2 mt-1"></i>
                                    <span>
                                        <a href="#" class="text-blue-600 hover:underline font-medium">Download sample template</a> for reference
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div> -->
            </div>
        </main>
    </div>

    <!-- JavaScript -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

        <script src="{{ asset('js/validation/import-validation.js') }}?v={{ time() }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // File input elements
            const excelFileInput = document.getElementById('excelFile');
            const csvFileInput = document.getElementById('csvFile');
            const dragDropFileInput = document.getElementById('dragDropFile');
            const fileDisplay = document.getElementById('fileDisplay');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');
            const removeFileBtn = document.getElementById('removeFile');
            const uploadProgress = document.getElementById('uploadProgress');
            const progressBar = document.getElementById('progressBar');
            const progressPercent = document.getElementById('progressPercent');
            const submitBtn = document.getElementById('submitBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const dragDropArea = document.getElementById('dragDropArea');
            let selectedFile = null;

            // File upload card click handlers
            
            // File upload button click handlers
            document.querySelectorAll('.file-upload button').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const parent = this.closest('.file-upload');
                    const fileInput = parent.querySelector('input[type="file"]');
                    if (fileInput) {
                        fileInput.click();
                    }
                });
            });

            // Browse button in drag-drop area
            

            // File selection handler
            function handleFileSelect(file) {
                if (file) {
                    selectedFile = file;
                    fileName.textContent = file.name;
                    fileSize.textContent = formatFileSize(file.size);
                    fileDisplay.style.display = 'block';
                    
                    // Scroll to file display
                    fileDisplay.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }

            // File input change handlers
            excelFileInput.addEventListener('change', function(e) {
                if (this.files.length > 0) {
                    handleFileSelect(this.files[0]);
                }
            });

            csvFileInput.addEventListener('change', function(e) {
                if (this.files.length > 0) {
                    handleFileSelect(this.files[0]);
                }
            });

         

            // Remove file button
            removeFileBtn.addEventListener('click', function() {
                selectedFile = null;
                fileDisplay.style.display = 'none';
                excelFileInput.value = '';
                csvFileInput.value = '';
            });

            

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

          

            function highlight() {
                dragDropArea.classList.add('dragover');
            }

            function unhighlight() {
                dragDropArea.classList.remove('dragover');
            }


            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files.length > 0) {
                    handleFileSelect(files[0]);
                }
            }

            // Format file size
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            function simulateSkuUpload() {
                runUpload(selectedFile, SKU_HEADERS, parseAndValidateSKU);
            }



            // Submit button click
            submitBtn.addEventListener('click', function() {
                if (!selectedFile) {
                    alert('Please select a file to import.');
                    return;
                }

                // Check file type
                const fileExt = selectedFile.name.split('.').pop().toLowerCase();
                const validExtensions = ['xlsx', 'xls', 'csv'];
                
                if (!validExtensions.includes(fileExt)) {
                    alert('Please select a valid Excel (.xlsx, .xls) or CSV (.csv) file.');
                    return;
                }

                // Check file size (max 50MB)
                const maxSize = 50 * 1024 * 1024; // 50MB in bytes
                if (selectedFile.size > maxSize) {
                    alert('File size exceeds 50MB limit. Please choose a smaller file.');
                    return;
                }

                // Simulate upload
                simulateSkuUpload();
                // parseAndValidateSKU(selectedFile);

                
            });

            // Cancel button
            cancelBtn.addEventListener('click', function() {
                if (selectedFile && !confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
                    return;
                }
                window.location.href = '/import-log';
            });

            // Back button
            document.querySelector('a[href="/import-log"]').addEventListener('click', function(e) {
                if (selectedFile) {
                    e.preventDefault();
                    if (confirm('You have a file selected. Are you sure you want to leave?')) {
                        window.location.href = '/import-log';
                    }
                }
            });
        });
    </script>
</body>
</html>