{{-- resources/views/items/create.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Item Registration</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <style>
    @keyframes slideInDown {
      from {
        opacity: 0;
        transform: translateY(-30px) scale(0.95);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes slideInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .modal-overlay { 
      display: none; 
      animation: fadeIn 0.3s ease-out;
    }
    
    .modal-overlay.active { 
      display: flex; 
    }

    .modal-content {
      max-width: 1100px;
      width: 95%;
      animation: slideInDown 0.4s cubic-bezier(0.16, 1, 0.3, 1);
      transform-origin: top center;
    }

    .card-hover {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card-hover:hover {
      transform: translateY(-2px);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .input-focus {
      transition: all 0.2s ease-in-out;
    }

    .input-focus:focus {
      transform: translateY(-1px);
      box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.1), 0 4px 6px -2px rgba(99, 102, 241, 0.05);
    }

    .image-preview {
      transition: all 0.3s ease-in-out;
    }

    .image-preview:hover {
      transform: scale(1.02);
    }

    .sku-row {
      transition: all 0.2s ease-in-out;
    }

    /* .sku-row:hover {
      background-color: rgba(249, 250, 251, 0.8);
      transform: translateX(4px);
    } */

    .btn-primary {
      background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
      background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
    }

    .btn-success {
      background: linear-gradient(135deg, #059669 0%, #10b981 100%);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.4);
      background: linear-gradient(135deg, #047857 0%, #0d9468 100%);
    }

    .pricing-card {
      background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
      border: 1px solid rgba(226, 232, 240, 0.8);
      transition: all 0.3s ease-in-out;
    }

    .pricing-card:hover {
      background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
      border-color: rgba(99, 102, 241, 0.3);
    }

    .image-upload-box {
      border: 2px dashed #d1d5db;
      transition: all 0.3s ease-in-out;
    }

    .image-upload-box:hover {
      border-color: #4f46e5;
      background-color: rgba(79, 70, 229, 0.05);
    }

    .image-upload-box.dragover {
      border-color: #4f46e5;
      background-color: rgba(79, 70, 229, 0.1);
      transform: scale(1.05);
    }

    .fade-in {
      animation: fadeIn 0.5s ease-in-out;
    }

    .slide-in-up {
      animation: slideInUp 0.4s ease-out;
    }

    .pulse-gentle {
      animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    .backdrop-blur {
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
    }

    /* Hide scrollbar for all elements */
    .no-scrollbar::-webkit-scrollbar {
      display: none;
    }

    .no-scrollbar {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
    .input-error-tooltip {
    position: absolute;
    background: #dc2626;
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    top: -28px;            /* SHOW ABOVE INPUT */
    left: 0;
    z-index: 50;
    animation: fadeIn 0.2s ease-in-out;
    white-space: nowrap;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-3px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Error text below input */
.error-text {
    color: #dc2626; /* red-600 */
    font-size: 0.875rem; /* 14px */
    margin-top: 0.25rem; /* 4px spacing below input */
    font-weight: 500;
    line-height: 1.2;
    display: block;
    opacity: 0;
    transition: opacity 0.2s ease-in-out;
}

/* When showing error */
.error-text:not(.hidden) {
    opacity: 1;
}

/* Warning icon inside input */
.error-icon {
    position: absolute;
    right: 0.75rem; /* 12px from right */
    top: 50%;
    transform: translateY(-50%);
    font-size: 1rem; /* 16px */
    color: #dc2626; /* red-600 */
    pointer-events: none; /* icon is not clickable */
}

/* Input border colors */
input.border-red-500 {
    border-color: #dc2626 !important;
}

input.border-green-500 {
    border-color: #16a34a !important; /* green-600 */
}




  </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-cyan-50 text-gray-800">

  @include('layout.sidebar')

<!-- main contents -->
<div class="max-w-6xl mx-auto p-6 animate__animated animate__fadeIn">
  
    <!-- Header with smooth entrance -->
    <div class="flex justify-between items-center mb-8 slide-in-up">
      <div>
        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
          Item Registration
        </h1>
        <p class="text-gray-600 mt-2">Create new product with detailed specifications</p>
      </div>
      <a href="{{route('itemList')}}" 
         class="btn-primary px-6 py-3 rounded-xl text-white font-medium flex items-center gap-2 shadow-lg hover:shadow-xl transition-all duration-300">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        View Item List
      </a>
    </div>

    <!-- Main Form Container -->
    <div class="bg-white/80 backdrop-blur rounded-2xl shadow-xl p-8 transition-all duration-500 border border-gray-200/80 card-hover">
      <form id="itemForm" action="{{ route('items.store') }}"  method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- Basic Information Section -->
        <div class="fade-in">
          <div class="flex gap-6">
            <!-- Left Column -->
            <div class="w-[600px]">
              <!-- Top Row Grid -->
              <div class="grid grid-cols-3 gap-4 mb-4">
                <!-- Item Code -->
               <div class="transform transition-all duration-300 hover:scale-[1.02]">
            <label class="block font-semibold mb-2 text-gray-700 text-sm">Item Code <span class="text-red-500">*</span></label>
           <div class="input-wrap">
            <input type="text" name="Item_Code" id="Item_Code" required
                  class="input-focus w-full p-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 text-sm">
             <p class="error-text hidden"></p>
</div>
              </div>

                <!-- JAN Code -->
               <div class="transform transition-all duration-300 hover:scale-[1.02]">
  <label class="block font-semibold mb-2 text-gray-700 text-sm">JAN Code <span class="text-red-500">*</span></label>
  <div class="input-wrap">
  <input type="text" name="JanCD" maxlength="13" required
      id="janInput"   class="input-focus w-full p-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 text-sm">
             <p class="error-text hidden"></p>
</div>
        </div>

      <!-- Maker Name -->
      <div class="transform transition-all duration-300 hover:scale-[1.02]">
        <label class="block font-semibold mb-2 text-gray-700 text-sm">Maker Name <span class="text-red-500">*</span></label>
         <div class="input-wrap">

        <input type="text" name="MakerName" required onblur="validateMakerNameLength(this)"
              class="input-focus w-full p-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 text-sm">
                <p class="error-text hidden"></p>
            </div>
            </div>
      </div>

      <!-- Item Name -->
      <div class="transform transition-all duration-300 hover:scale-[1.02]">
        <label class="block font-semibold mb-2 text-gray-700 text-sm">Item Name <span class="text-red-500">*</span></label>
                 <div class="input-wrap">

        <textarea name="Item_Name" rows="2" required onblur="validateItemNameLength(this)"
            class="input-focus w-full p-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 resize-none text-sm"></textarea>
                <p class="error-text hidden"></p>
            </div>
          </div>
      </div>


            <!-- Memo Right Column -->
            <div class="flex-1 transform transition-all duration-300 hover:scale-[1.01]">
              <label class="block font-semibold mb-2 text-gray-700">Memo</label>
                <div class="input-wrap">
              <textarea name="Memo" rows="10" onblur="validateMemoLength(this)"
                      class="input-focus w-full p-4 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 resize-y"></textarea>
                        <p class="error-text hidden"></p>
            </div>
                    </div>
          </div>
        </div>

        <!-- Pricing Information -->
       <div class="fade-in">
  <div class="pricing-card p-4 md:p-6 rounded-2xl border border-gray-200/80 bg-white shadow-sm overflow-x-hidden">
  <h3 class="text-lg md:text-xl font-bold mb-4 md:mb-6 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
    Pricing Information
  </h3>

  <div class="grid grid-cols-2 gap-3 md:gap-6"> <!-- Always 2 columns, smaller gap on mobile -->
    
    <!-- Basic Price -->
    <div class="space-y-1 md:space-y-2 min-w-0">
      <label class="block font-semibold text-gray-700 text-xs md:text-sm">
        Sale Price <span class="text-red-500">*</span>
      </label>
      <div class="flex items-center">
        <div class="relative flex-1 min-w-0">
          <input 
            type="text" 
            name="SalePrice" 
            required 
            placeholder="0"
            class="price-input input-focus w-full p-2 md:p-4 text-xs md:text-sm rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 text-right placeholder:text-right"
          />
          <p class="error-text hidden text-xs text-red-500 mt-1 text-right"></p>
        </div>
        <span class="ml-1 md:ml-3 text-gray-600 font-medium whitespace-nowrap text-sm md:text-base flex-shrink-0">
          円
        </span>
      </div>
    </div>

    <!-- List Price -->
    <div class="space-y-1 md:space-y-2 min-w-0">
      <label class="block font-semibold text-gray-700 text-xs md:text-sm">
        List Price <span class="text-red-500">*</span>
      </label>
      <div class="flex items-center">
        <div class="relative flex-1 min-w-0">
          <input 
            type="text" 
            name="ListPrice" 
            required 
            placeholder="0"
            class="price-input input-focus w-full p-2 md:p-4 text-xs md:text-sm rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 text-right placeholder:text-right"
          />
          <p class="error-text hidden text-xs text-red-500 mt-1 text-right"></p>
        </div>
        <span class="ml-1 md:ml-3 text-gray-600 font-medium whitespace-nowrap text-sm md:text-base flex-shrink-0">
          円
        </span>
      </div>
    </div>
    
  </div>
</div>
</div>

        <!-- SKU Section -->
        <div class="fade-in">
          <div class="border-t border-gray-200 pt-8">
            <div class="flex justify-between items-center mb-6">
              <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                  SKU List
                </h2>
                <p class="text-gray-600 text-sm mt-1">Manage product variants and inventory</p>
              </div>

              <button type="button" id="openSkuModal" 
                      class="btn-primary px-6 py-3 rounded-xl text-white font-medium flex items-center gap-2 transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add SKU
              </button>
            </div>

            <div class="overflow-x-auto no-scrollbar rounded-2xl border border-gray-200 shadow-sm">
              <table class="min-w-full border-collapse">
                <thead class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white">
                  <tr>
                    <th class="p-4 border-b font-semibold text-left">Color</th>
                    <th class="p-4 border-b font-semibold text-left">Size</th>
                    <th class="p-4 border-b font-semibold text-left">Qty</th>
                  </tr>
                </thead>

                <tbody id="skuTableBody">
                  <tr id="emptySkuState" class="pulse-gentle">
                    <td colspan="3" class="p-8 text-center text-gray-500 bg-gray-50/50">
                      <div class="flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <span class="text-lg">No SKUs added yet</span>
                        <p class="text-sm text-gray-400 mt-1">Click "Add SKU" to create variants</p>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Product Images -->
        <div class="fade-in">
          <div class="border-t border-gray-200 pt-8">
            <h2 class="text-2xl font-bold mb-6 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
              Product Images
            </h2>

           <div class="bg-white p-4 sm:p-6 rounded-2xl border border-gray-200/80 shadow-sm overflow-x-hidden">
  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 md:gap-6">
    @for($i = 0; $i < 5; $i++)
      <div class="group space-y-2 sm:space-y-3 p-2 sm:p-4 bg-gray-50/50 rounded-xl border-2 border-dashed border-gray-300 image-upload-box transition-all duration-300 min-w-0">
        <!-- Preview -->
        <div id="imagePreview{{ $i }}" 
             class="image-preview w-full aspect-square bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg sm:rounded-xl flex items-center justify-center overflow-hidden shadow-inner">
          <div class="text-center p-1 sm:p-0">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 md:w-8 md:h-8 text-gray-400 mx-auto mb-1 sm:mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span class="text-gray-400 text-xs block">No Image</span>
          </div>
        </div>

        <!-- Name Input -->
        <input id="imageName{{ $i }}" name="image_names[]" type="text" placeholder="Image name" 
               class="w-full p-1.5 sm:p-2 text-xs sm:text-sm rounded-md sm:rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300" disabled>

        <!-- Buttons -->
        <div class="flex gap-1.5 sm:gap-2 min-w-0">
          <label class="flex-1 min-w-0">
            <button id="imageBtn{{ $i }}" type="button" 
                    class="btn-primary w-full px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-white rounded-md sm:rounded-lg transition-all duration-300 truncate">
              Upload
            </button>
            
            <input id="imageInput{{ $i }}" name="images[]" type="file" accept="image/*" class="hidden" onchange="validateFile(this)">
          </label>

          <button id="imageRemove{{ $i }}" type="button" 
                  class="px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm bg-white border border-gray-300 rounded-md sm:rounded-lg hover:bg-gray-50 transition-all duration-300 transform hover:scale-105 flex-shrink-0 min-w-[2rem] sm:min-w-[2.5rem]" 
                  title="Remove">
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
        </div>
      </div>
    @endfor
  </div>
</div>

              <input type="hidden" name="images_meta" id="imagesMeta" value="">
            </div>
          </div>
        </div>

        <!-- Hidden SKU Data -->
        <input type="hidden" name="skus_json" id="skus_json" >

        <!-- Submit Buttons -->
        <div class="flex justify-end mt-8 space-x-4 pt-6 border-t border-gray-200 slide-in-up">
          <a href="" 
             class="px-8 py-3 rounded-xl font-medium transition-all duration-300 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400 transform hover:scale-105">
            Cancel
          </a>
          <button type="submit"   id="submitButton"
                  class="btn-success px-8 py-3 rounded-xl text-white font-medium flex items-center gap-2 transition-all duration-300 submitBtn">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
             <span>Insert Items</span>
          
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Enhanced SKU Modal -->
 <div id="skuModal" class="modal-overlay fixed inset-0 z-50 items-start justify-center pt-20 bg-black/40 backdrop-blur">
    <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 border border-gray-200/80 mx-4 max-h-[85vh]  flex flex-col max-w-7xl">
      <!-- Modal Header -->
      <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <div>
          <h2 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
            SKU Management
          </h2>
          <p class="text-gray-600 mt-2">Add and manage product variants</p>
        </div>
        <button id="closeSkuModal" class="p-2 rounded-lg hover:bg-gray-100 transition-all duration-300 transform hover:scale-110">
          <svg class="w-6 h-6 text-gray-500 hover:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Modal Content - Now with scrollbar -->
<div class="flex-1 overflow-y-auto">
        <div class="h-full overflow-y-auto pr-2 custom-scrollbar">
          <table class="w-full border text-sm rounded-xl overflow-hidden">
            <thead class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white sticky top-0 z-10">
              <tr>
                <th class="p-4 border-r border-white/20 font-semibold">Delete</th>
                <th class="p-4 border-r border-white/20 font-semibold">
                  Size Name<br><small class="font-normal opacity-90">(Horizontal axis)</small>
                </th>
                <th class="p-4 border-r border-white/20 font-semibold">
                  Color Name<br><small class="font-normal opacity-90">(Vertical axis)</small>
                </th>
                <th class="p-4 border-r border-white/20 font-semibold">Size Code</th>
                <th class="p-4 border-r border-white/20 font-semibold">Color Code</th>
                <th class="p-4 border-r border-white/20 font-semibold">JAN Code</th>
                <th class="p-4 border-r border-white/20 font-semibold w-40">Qty-flag</th>
                <th class="p-4 font-semibold">Number in Stock</th>
              </tr>
            </thead>
            <tbody id="skuModalBody" class="bg-white">
              <!-- JS will inject rows here -->
              <!-- Sample rows to show scrollbar -->
              <tr class="border-b hover:bg-gray-50">
                <td class="p-4 text-center"><button class="text-red-500 hover:text-red-700">×</button></td>
                <td class="p-4">Small</td>
                <td class="p-4">Red</td>
                <td class="p-4">S</td>
                <td class="p-4">RED</td>
                <td class="p-4">4901234567890</td>
                <td class="p-4">
                  <select class="w-full p-2 border rounded-lg">
                    <option>Available</option>
                    <option>Out of Stock</option>
                  </select>
                </td>
                <td class="p-4"><input type="number" class="w-full p-2 border rounded-lg" value="10"></td>
              </tr>
              <!-- Add more rows as needed -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
        <button id="addSkuRowBtn" type="button" 
                class="bg-indigo-100 text-indigo-800 px-6 py-3 rounded-xl font-medium hover:bg-indigo-200 transition-all duration-300 transform hover:scale-105 flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Add Row
        </button>

        <div class="flex items-center gap-3">
          <button type="button" id="closeModalBtn" 
                  class="px-8 py-3 rounded-xl font-medium transition-all duration-300 bg-gray-500 text-white hover:bg-gray-600 transform hover:scale-105">
            Close
          </button>
          <button type="button" id="saveSkusBtn" 
                  class="btn-success px-8 py-3 rounded-xl text-white font-medium flex items-center gap-2 transition-all duration-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Save SKUs
          </button>
        </div>
      </div>
    </div>
  </div>

  <style>
    /* Custom scrollbar styling */
    .custom-scrollbar {
      scrollbar-width: thin;
      scrollbar-color: #c7d2fe #f5f5f5;
    }
    
    .custom-scrollbar::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
      background: #f5f5f5;
      border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #c7d2fe;
      border-radius: 10px;
      border: 2px solid #f5f5f5;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
      background: #a5b4fc;
    }
    
    /* Ensure table header stays fixed during scroll */
    table thead {
      position: sticky;
      top: 0;
    }
    
    /* Modal content scroll fix */
    .modal-content {
      display: flex;
      flex-direction: column;
    }
    
    /* Make tbody scrollable */
    table {
      min-width: 100%;
    }
  </style>

  {{-- JavaScript: image handling + SKU modal + form submit --}}
   <script src="{{ asset('js/validation/item-validation.js') }}?v={{ time() }}"></script>
   <script src="{{ asset('js/validation/dataBind.js') }}?v={{ time() }}"></script>
<script>
  document.getElementById('Item_Code').addEventListener('blur', function() {
    const input = this;
    let errorText = this.nextElementSibling;
    
    // 1. First, run your existing format validation
    // This handles spaces, special chars, and leading zeros.
    if (!validateItemCode(input)) {
        return; // Stop here if format is already wrong
    }

    const code = input.value;

    // 2. If format is valid, check the database
    if (code.length > 0) {
        fetch("{{ route('check.item.code') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ Item_Code: code })
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                // Duplicate found: Clear and Alert
                input.value = ""; // Clear the input as requested
errorText.innerText = "⚠️ This Item Code is already taken.";
                errorText.classList.remove('hidden');
                errorText.classList.add('text-red-500', 'mt-1');
                this.classList.add('border-red-500');                
                // Optional: Force focus back so they can't leave until fixed
                setTimeout(() => input.focus(), 10); 
            } else {
                // Truly valid and unique
                setValid(input);
            }
        })
        .catch(error => console.error('Error checking item code:', error));
    }
});
</script> 
<!-- Fixed Latest Create Form -->
</body>
</html>
