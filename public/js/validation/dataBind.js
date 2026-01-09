        // State
        const state = {
          productImages: [null, null, null, null, null],
          skus: []
        };

        document.addEventListener('DOMContentLoaded', () => {
          for (let i = 0; i < 5; i++) {
            setupImageSlot(i);
          }
          setupSkuModal();
        });




          window.state = window.state || {};
          window.state.productImages = window.state.productImages || [];

          function padSerial(n) {
            return String(n);
          }

          function slugifyForFilename(text) {
            if (!text) return 'image';
            return String(text)
              .trim()
              .replace(/\s+/g, '-')           // spaces -> hyphen
              .replace(/[^a-zA-Z0-9\-_]/g, '')// remove odd chars
              .toLowerCase();
          }

          // --- Replace setupImageSlot with this version ---
          function setupImageSlot(i, options = {}) {
            const input = document.getElementById(`imageInput${i}`);
            const preview = document.getElementById(`imagePreview${i}`);
            const nameInput = document.getElementById(`imageName${i}`);
            const btn = document.getElementById(`imageBtn${i}`);
            const removeBtn = document.getElementById(`imageRemove${i}`);

            const itemCodeSelector = options.itemCodeSelector || '#Item_Code';
            const itemNameSelector = options.itemNameSelector || '#Item_Name';
            const padWidth = options.padWidth || 3;
            const forceAutoName = options.forceAutoName ?? false;

            if (!input || !preview || !nameInput || !btn || !removeBtn) {
              console.warn('setupImageSlot: missing DOM elements for slot', i);
              return;
            }

            if (!window.state.productImages[i]) window.state.productImages[i] = null;

            btn.addEventListener('click', () => input.click());

            removeBtn.addEventListener('click', () => {
              input.value = "";
              window.state.productImages[i] = null;
              preview.innerHTML = '<div class="text-center">No Image</div>';
              nameInput.value = "";
              nameInput.disabled = !!forceAutoName;
              btn.textContent = "Upload";
              console.log('slot', i, 'removed, state now:', window.state.productImages);
            });

            nameInput.addEventListener('input', () => {
              if (!window.state.productImages[i]) window.state.productImages[i] = { file: null, name: '' };
              window.state.productImages[i].name = nameInput.value;
            });

            input.addEventListener('change', (e) => {
              const file = e.target.files[0];
              if (!file) return;

              if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                input.value = '';
                return;
              }

              const reader = new FileReader();
              reader.onload = function(ev) {
                preview.innerHTML = `<img src="${ev.target.result}" class="w-full h-full object-cover rounded-xl" alt="Preview">`;
              };
              reader.readAsDataURL(file);

              // --- Build base from Item_Code (preferred) or Item_Name ---
              let base = '';
              const itemCodeEl = document.querySelector(itemCodeSelector);
              const itemNameEl = document.querySelector(itemNameSelector);
              if (itemCodeEl && itemCodeEl.value) base = itemCodeEl.value;
              else if (itemNameEl && itemNameEl.value) base = itemNameEl.value;
              else base = 'item';

              // Display base (keep casing) but sanitize for final name
              let displayBase = base.trim();
              if (!displayBase) displayBase = 'item';

              // slug for matching (lowercase, safe)
              const slugBase = slugifyForFilename(base);

              // --- Collect existing serial numbers ---
              const existingNumbers = new Set();

              // 1) scan current JS state
              window.state.productImages.forEach((slot, idx) => {
                if (!slot || !slot.name) return;
                const nm = slot.name.toString();
                const m = nm.match(new RegExp(`${slugBase}[-_]?([0-9]{1,})`, 'i'));
                if (m && m[1]) existingNumbers.add(parseInt(m[1], 10));
              });

              // 2) scan all name input DOM fields (helps when state isn't fully populated)
              document.querySelectorAll('input[name^="image_names"]').forEach((el) => {
                const nm = (el.value || '').toString();
                if (!nm) return;
                const m = nm.match(new RegExp(`${slugBase}[-_]?([0-9]{1,})`, 'i'));
                if (m && m[1]) existingNumbers.add(parseInt(m[1], 10));
              });

              // Debug: show what was found
              console.log('slot', i, 'base:', displayBase, 'slug:', slugBase, 'existing:', Array.from(existingNumbers));

              // Find smallest available serial
              let serial = 1;
              while (existingNumbers.has(serial)) serial++;

              // Determine extension from original file
              const extMatch = file.name.match(/(\.[^./\\]+)$/);
              const extension = extMatch ? extMatch[1].toLowerCase() : '';

              // Final name format: DisplayBase-001.ext  (hyphen between)
              // sanitize displayBase for final filename: remove unwanted chars but keep casing
              const displayBaseSanitized = displayBase
                .trim()
                .replace(/\s+/g, '-')            // spaces -> hyphen
                .replace(/[^A-Za-z0-9\-_]/g, ''); // remove other chars

//               const finalFilename = `${displayBaseSanitized}-${padSerial(serial)}${extension}`;
                const finalFilename = `${displayBaseSanitized}-${serial}${extension}`;
              // Apply
              nameInput.value = finalFilename;
              nameInput.disabled = !!forceAutoName;
              btn.textContent = 'Edit';

              window.state.productImages[i] = {
                file: file,
                name: finalFilename,
                url: null
              };

              console.log('slot', i, 'set name ->', finalFilename, 'state now:', window.state.productImages);
            });
          }



          function setupSkuModal() {
              const skuModal = document.getElementById('skuModal');
              const openSku = document.getElementById('openSkuModal');
              const closeSku = document.getElementById('closeSkuModal');
              const closeModalBtn = document.getElementById('closeModalBtn');
              const saveSkusBtn = document.getElementById('saveSkusBtn');
              const addSkuRowBtn = document.getElementById('addSkuRowBtn');
              const skuModalBody = document.getElementById('skuModalBody');

              openSku.addEventListener('click', () => {
                  populateModalWithExistingSkus();
                  skuModal.classList.add('active');
              });

              closeSku.addEventListener('click', () => skuModal.classList.remove('active'));
              closeModalBtn.addEventListener('click', () => skuModal.classList.remove('active'));
              addSkuRowBtn.addEventListener('click', () => addSkuRow());

              // SAVE BUTTON
              saveSkusBtn.addEventListener('click', () => {

              const newSkus = [];
              let janError = false;
              let duplicateFound = false;

              skuModalBody.querySelectorAll('tr').forEach(row => {

                  const sizeName = row.querySelector('.size-name')?.value.trim() || '';
                  const colorName = row.querySelector('.color-name')?.value.trim() || '';
                  const sizeCode = row.querySelector('.size-code')?.value.trim() || '';
                  const colorCode = row.querySelector('.color-code')?.value.trim() || '';
                  const janCodeInput = row.querySelector('.jan-code');
                  const janCode = janCodeInput?.value.trim() || '';
                  const qtyFlag = row.querySelector('.qty-flag')?.value || 'false';
                  const stockQuantity = row.querySelector('.stock-quantity')?.value || '0';

                  // Skip empty entire row
                  if (!sizeName && !colorName && !sizeCode && !colorCode && !janCode) return;

                  // Validate JAN
                  if (!validateSkuJan(janCodeInput)) {
                      janError = true;
                  }

                  const keyName = `${sizeName}__${colorName}`;
                  const keyCode = `${sizeCode}__${colorCode}`;

                  // --- DUPLICATE CHECKS ---
                // ---- DUPLICATE CHECKS ----
                 // --- REVISED DUPLICATE AND CONSISTENCY CHECKS ---

      // 1. Check if the Name/Code Pairs already exist (STILL ESSENTIAL)
      let pairNameDup  = newSkus.some(s => s.keyName === keyName);
      let pairCodeDup  = newSkus.some(s => s.keyCode === keyCode);

      // 2. Check for Name/Code Inconsistency (STILL ESSENTIAL)
      let inconsistentNameCode = newSkus.some(s => 
          (s.sizeName === sizeName && s.sizeCode !== sizeCode) ||
          (s.colorName === colorName && s.colorCode !== colorCode)
      );

      // 3. NEW: Check for individual Code duplicates (This is what you are asking for)
      let sizeCodeAloneDup = newSkus.some(s => s.sizeCode === sizeCode && s.sizeName !== sizeName);
      let colorCodeAloneDup = newSkus.some(s => s.colorCode === colorCode && s.colorName !== colorName);

      // Collect messages for multiple errors
      let errors = [];

      // 🔥 CASE 1: exact pair duplicate (sizeName + colorName)
      if (pairNameDup) {
          errors.push(`Size "${sizeName}" AND Color "${colorName}" combination already exists.`);
      }

      // 🔥 CASE 2: exact pair duplicate (sizeCode + colorCode)
      if (pairCodeDup) {
          errors.push(`SizeCode "${sizeCode}" AND ColorCode "${colorCode}" combination already exists.`);
      }

      // 🔥 CASE 3: Name/Code Inconsistency
      // (Kept separate for clearer error messages)
      if (inconsistentNameCode) {
          const sizeNameInconsistent = newSkus.some(s => s.sizeName === sizeName && s.sizeCode !== sizeCode);
          const colorNameInconsistent = newSkus.some(s => s.colorName === colorName && s.colorCode !== colorCode);

          if (sizeNameInconsistent) {
              errors.push(`Size Name "${sizeName}" is already defined with a different Size Code.`);
          }
          if (colorNameInconsistent) {
              errors.push(`Color Name "${colorName}" is already defined with a different Color Code.`);
          }
      }

      // 🔥 CASE 4: Individual Code Duplicates (New/Stricter Rule)
      if (sizeCodeAloneDup) {
          errors.push(`Size Code "${sizeCode}" is already used for Size "${newSkus.find(s => s.sizeCode === sizeCode).sizeName}". It cannot be reused for a different Size Name.`);
      }

      if (colorCodeAloneDup) {
          errors.push(`Color Code "${colorCode}" is already used for Color "${newSkus.find(s => s.colorCode === colorCode).colorName}". It cannot be reused for a different Color Name.`);
      }


      // ... rest of your code remains the same ...
      // 🔥 If any errors → stop saving
      if (errors.length > 0) {
          duplicateFound = true;
          alert(errors.join("\n")); // show all messages at once
          return;
      }

      // No duplicates — safe to push
      // ...

            // No duplicates — safe to push
            newSkus.push({
                keyName,
                keyCode,
                sizeName,
                colorName,
                sizeCode,
                colorCode,
                janCode,
                qtyFlag,
                stockQuantity: parseInt(stockQuantity) || 0
            });
            });



          // Error Handling
          if (janError) {
              alert("Fix JAN Code before saving.");
              return;
          }

          if (duplicateFound) {
              return;
          }

          // Save SKUs
          state.skus = newSkus.map(({ _key, ...sku }) => sku);
          document.getElementById('skus_json').value = JSON.stringify(state.skus);

          renderSkuTable();
          skuModal.classList.remove('active');
      });
      }

        function populateModalWithExistingSkus() {
          const skuModalBody = document.getElementById('skuModalBody');
          skuModalBody.innerHTML = '';

          state.skus.forEach(sku => {
            addSkuRow(sku);
          });

          if (state.skus.length === 0) {
            addSkuRow();
          }
        }

      function formatPriceInput(input) {
          let value = input.value.replace(/,/g, '');        // remove commas
          value = value.replace(/\D/g, '');                // remove non-digits

          if (value === '') {
              input.value = '';
              return;
          }

          input.value = Number(value).toLocaleString('ja-JP'); // add commas
      }

      // --- Remove commas before submit ---
      function unformatPrice(value) {
          return value.replace(/,/g, '');
      }

      // Apply to all 3 price fields
      document.querySelectorAll('.price-input').forEach(input => {
          input.addEventListener('input', () => formatPriceInput(input));
          input.addEventListener('blur', () => formatPriceInput(input));
      });



        document.getElementById('itemForm').addEventListener('submit', function() {
          document.getElementById('skus_json').value = JSON.stringify(state.skus);

          const priceFields = document.querySelectorAll('.price-input');
          priceFields.forEach(f => {
              f.value = unformatPrice(f.value); 
          });
      });


          function addSkuRow(skuData = {}) {
          const skuModalBody = document.getElementById('skuModalBody');
          const rowId = Date.now() + Math.random();
          
          const row = document.createElement('tr');
        row.className = 'sku-row border-b border-gray-200';
      row.innerHTML = `
        <td class="p-3 border-r">
          <button type="button" class="delete-row-btn text-red-500 p-1 rounded transition-none" data-row-id="${rowId}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
        </td>
        <td class="p-3 border-r">
        <div class="input-wrap">
          <input type="text" class="size-name w-full p-2 border border-gray-300 rounded-lg transition-none" 
                value="${skuData.sizeName || ''}" placeholder="Enter size name">
                <p class="error-text hidden"></p>
                  </div>
        </td>
        <td class="p-3 border-r">
        <div class="input-wrap">
          <input type="text" class="color-name w-full p-2 border border-gray-300 rounded-lg transition-none" 
                value="${skuData.colorName || ''}" placeholder="Enter color name">
                <p class="error-text hidden"></p>
                  </div>
        </td>
        <td class="p-3 border-r">
        <div class="input-wrap">
          <input type="text" class="size-code w-full p-2 border border-gray-300 rounded-lg transition-none" 
                value="${skuData.sizeCode || ''}" placeholder="Size code">
                <p class="error-text hidden"></p>
                  </div>
        </td>
        <td class="p-3 border-r">
        <div class="input-wrap">
          <input type="text" class="color-code w-full p-2 border border-gray-300 rounded-lg transition-none" 
                value="${skuData.colorCode || ''}" placeholder="Color code">
                <p class="error-text hidden"></p>
                  </div>
        </td>
        <td class="p-3 border-r">
        <div class="input-wrap">
          <input type="text" class="jan-code w-full p-2 border border-gray-300 rounded-lg transition-none" 
                value="${skuData.janCode || ''}" placeholder="JAN code">
                  <p class="error-text hidden"></p>
                  </div>
        </td>
        <td class="p-3 border-r w-48">
        <div class="input-wrap">
          <select class="qty-flag !w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
            <option value="true" ${skuData.qtyFlag === 'true' ? 'selected' : ''}>Yes</option>
            <option value="false" ${skuData.qtyFlag === 'false' || !skuData.qtyFlag ? 'selected' : ''}>No</option>
          </select>
          <p class="error-text hidden"></p>
        </div>
      </td>
        <td class="p-3">
        <div class="input-wrap">
          <input type="number" class="stock-quantity text-right w-full p-2 border border-gray-300 rounded-lg transition-none" 
                value="${skuData.stockQuantity || '0'}" placeholder="0" min="0">
                <p class="error-text hidden"></p>
                  </div>
        </td>
      `;

          skuModalBody.appendChild(row);
          row.querySelector('.delete-row-btn').addEventListener('click', (e) => {
            e.preventDefault();
            row.remove();
          });
          attachSkuRowValidation(row);
          
          // checkSkuValidation();

        }


        // display select sku as table
       
        function renderSkuTable() {
          const skuTableBody = document.getElementById('skuTableBody');
          const emptyState = document.getElementById('emptySkuState');

          const existingRows = skuTableBody.querySelectorAll('tr:not(#emptySkuState)');
          existingRows.forEach(row => row.remove());
          
          if (state.skus.length === 0) {
            if (!emptyState) {
              const newEmptyState = document.createElement('tr');
              newEmptyState.id = 'emptySkuState';
              newEmptyState.className = 'pulse-gentle';
              newEmptyState.innerHTML = `
                <td colspan="3" class="p-8 text-center text-gray-500 bg-gray-50/50">
                  <div class="flex flex-col items-center justify-center">
                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <span class="text-lg">No SKUs added yet</span>
                    <p class="text-sm text-gray-400 mt-1">Click "Add SKU" to create variants</p>
                  </div>
                </td>
              `;
              skuTableBody.appendChild(newEmptyState);
            } else {
              emptyState.style.display = '';
            }
          } else {
            if (emptyState) emptyState.style.display = 'none';
            state.skus.forEach((sku, index) => {
              const row = document.createElement('tr');
              row.className = 'border-b border-gray-200 transition-all duration-200';
              row.innerHTML = `
                <td class="p-4 border-r">${escapeHtml(sku.colorName || '-')}</td>
                <td class="p-4 border-r">${escapeHtml(sku.sizeName || '-')}</td>
                <td class="p-4 border-r">${escapeHtml(sku.stockQuantity || '0')}</td>
              `;
              skuTableBody.appendChild(row);
            });
          }
        }

        function escapeHtml(text) {
          if (!text && text !== 0) return '';
          return String(text).replace(/[&<>"'\/]/g, function (s) {
            const entityMap = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '/': '&#x2F;' };
            return entityMap[s];
          });
        }

//       document.getElementById('itemForm').addEventListener('submit', async function(e) {
//           e.preventDefault(); // stop normal submit

//           const itemCode = document.querySelector('input[name="Item_Code"]').value.trim();

//           // Check duplicate item code first
//           const response = await fetch(`/check-item-code?code=${itemCode}`);
//           const data = await response.json();

//           if (data.exists) {
//               alert("❌ Item Code already exists. Please use another one.");
//               return;
//           }

//           // No duplicate → submit form normally
//           this.submit();
//       });

