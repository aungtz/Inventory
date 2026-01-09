// --------------------
// Common Validation UI
// --------------------
function showInputError(input, message, { autoHide = true, duration = 5000 } = {}) {
    const wrapper = input.closest(".input-wrap");
    if (!wrapper) return;

    const errorText = wrapper.querySelector(".error-text");
    if (!errorText) return;

    // --- Cleanup function (defined here for access to local variables) ---
    const cleanupOnFocus = () => {
        // We use the same cleanup logic as setValid below, but add the listener removal.
        errorText.classList.add("hidden");
        wrapper.querySelectorAll(".error-icon").forEach(i => i.remove());
        input.classList.remove("border-red-500");
        input.classList.add("border-gray-300");
        
        // Remove the focus listener
        input.removeEventListener('focus', cleanupOnFocus);
        
        // Clear the stored reference
        delete input.cleanupListener; 
    };
    // ---------------------------------------------
    
    // Store the listener reference on the input so setValid can remove it later
    input.cleanupListener = cleanupOnFocus; 

    // Remove any old icon before setting a new one
    wrapper.querySelectorAll(".error-icon").forEach(i => i.remove());

    // Red border
    input.classList.remove("border-green-500");
    input.classList.remove("border-gray-300"); 
    input.classList.add("border-red-500");

    // Show error message
    errorText.textContent = message;
    errorText.classList.remove("hidden");
    
    // Add the focus listener
    input.addEventListener('focus', cleanupOnFocus);


    // Auto-hide after duration
    if (autoHide) {
        setTimeout(() => {
            // Only clean up if the error is still active
            if (input.classList.contains("border-red-500")) {
                cleanupOnFocus();
            }
        }, duration);
    }
}

function setValid(input) {
    const wrapper = input.closest(".input-wrap");
    if (!wrapper) return;
    
    const errorText = wrapper.querySelector(".error-text");

    // 1. Remove the error message and icons IMMEDIATELY
    if (errorText) {
        errorText.classList.add("hidden");
    }
    wrapper.querySelectorAll(".error-icon").forEach(i => i.remove());

    // 2. Remove the focus listener if it exists
    if (input.cleanupListener) {
        // Remove the listener using the stored reference
        input.removeEventListener('focus', input.cleanupListener);
        // Clean up the stored reference
        delete input.cleanupListener; 
    }
    
    // 3. Set the valid border color
    input.classList.remove('border-red-500');
    input.classList.remove('border-gray-300'); // Ensure default border is removed
    input.classList.add('border-green-500');
}

/**
 * Global function to remove the error state (message, icons, border, and listeners) 
 * from a given input field.
 */
function clearErrorState(input) {
    const wrapper = input.closest(".input-wrap");
    if (!wrapper) return;

    const errorText = wrapper.querySelector(".error-text");
    if (errorText) {
        errorText.classList.add("hidden");
    }

    wrapper.querySelectorAll(".error-icon").forEach(i => i.remove());
    
    input.classList.remove("border-red-500");
    input.classList.add("border-gray-300"); // Add back a default border if needed

    
}

function setInvalid(input) {
    input.classList.remove('border-green-500');
    input.classList.add('border-red-500');
}
function validateRequiredText(input, maxLength = 100) {
    const val = input.value.trim();
    input.value = val;

    if (!val) {
        setInvalid(input);
        showInputError(input,"This field is required");
        return false;
    }

    // if (val.length > maxLength) {
    //     input.value = val.substring(0, maxLength);
    //     setInvalid(input);
    //     showInputError(input,`Max ${maxLength} characters allowed`);
    //     return false;
    // }
    


    setValid(input);
    return true;
}
function validateItemCode(input) {
    // Get original value before any cleaning
    const originalValue = input.value;
    
    // 1. Define the Regular Expression for forbidden characters
    const forbiddenCharsRegex = /[\s&*^$#@%()\u3000-\u30FF\u4E00-\u9FFF\uFF00-\uFFEF]/g;
    
    // 2. Create the 'cleaned' value
    let cleaned = originalValue.replace(forbiddenCharsRegex, '');
    
    // --- NEW RULE: Cannot start with 0 ---
    if (cleaned.length > 0 && cleaned.charAt(0) === '0') {
        // Show alert
        showInputError(input,"Item Code cannot start with 0");
        
        // Remove the leading 0
        cleaned = cleaned.substring(1);
        input.value = cleaned;
        
        setInvalid(input);
        showInputError(input, "Item Code cannot start with 0");
        return false;
    }
    
    // 3. Check for change and update input
    if (cleaned !== originalValue) {
        input.value = cleaned; 
        
        // --- START OF ERROR MESSAGE FIX ---
        
        // A. If the input is empty AFTER cleaning (all input was forbidden chars):
        if (cleaned.length === 0) {
            setInvalid(input);
            showInputError(input, "Item Code cannot contain special characters, spaces, or Japanese characters");
            return false;
        }
        
        // B. If the input is NOT empty AFTER cleaning:
        setInvalid(input);
        showInputError(input, "Invalid characters (spaces, Japanese, special characters) were removed");
        return false;
    }

    // 4. Final Empty check
    if (cleaned.length === 0) {
        setInvalid(input);
        showInputError(input, "Item Code is required");
        return false;
    }

    // 5. Check length
    if (!validateItemCodeLength(input)) {
        return false;
    }
    
    // ✔ Valid
    setValid(input);
    return true;
}

// NEW: Event listener to prevent typing starting 0
function preventLeadingZero(event, input) {
    // Get current value without forbidden characters
    const currentValue = input.value.replace(/[\s&*^$#@%\u3000-\u30FF\u4E00-\u9FFF\uFF00-\uFFEF]/g, '');
    const newChar = event.key;
    
    // If field is empty and user tries to type '0', prevent it
    if (currentValue.length === 0 && newChar === '0') {
        event.preventDefault();
        setInvalid(input);
        showInputError(input, "Item Code cannot start with 0");
        return false;
    }
    
    // Prevent forbidden characters while typing
    const forbiddenCharsRegex = /[\s&*^$#@%\u3000-\u30FF\u4E00-\u9FFF\uFF00-\uFFEF]/;
    if (forbiddenCharsRegex.test(newChar)) {
        event.preventDefault();
        setInvalid(input);
        showInputError(input, "Cannot use special characters, spaces, or Japanese characters");
        return false;
    }
    
    return true;
}

// Update your setup function to include the preventLeadingZero handler
function setupItemCodeValidation() {
    const itemCodeInputs = document.querySelectorAll('.item-code-input'); // Use appropriate selector
    
    itemCodeInputs.forEach(input => {
        // Prevent invalid key input
        input.addEventListener('keydown', function(event) {
            return preventLeadingZero(event, this);
        });
        
        // Validate on input
        input.addEventListener('input', function() {
            validateItemCode(this);
        });
        
        // Validate on blur
        input.addEventListener('blur', function() {
            validateItemCode(this);
        });
        
        // Validate on focus (for empty field check)
        input.addEventListener('focus', function() {
            if (this.value.trim().length === 0) {
                setInvalid(this);
                showInputError(this, "Item Code is required");
            }
        });
    });
}

function validateMemo(input) {
    let val = input.value.replace(/^\s+/g, '');
    input.value = val;

  

    // if (val.length > 200) {
    //     input.value = val.substring(0, 200);
    //     setInvalid(input);
    //     showInputError(input,"Memo cannot exceed 200 characters");
    //     return false;
    // }
    if (!validateMemoLength(input)) {
        return false;
    }

    setValid(input);
    return true;
}



//old code for janCD
// function validateJanGeneric(input, { enforceExact13 = false } = {}) {
//   if (!input) return { ok: false, reason: 'missing input' };

//   // sanitize digits only
//   let raw = input.value.replace(/\D/g, '');

//   // If first char is '0' => show message and refuse to accept that zero.
//   if (raw.startsWith('0')) {
//     // remove the leading zero(s). Show message and stop validation here.
//     raw = raw.replace(/^0+/, ''); // remove all leading zeros safely
//     input.value = raw;
//     setInvalid(input);
    
//     showInputError(input, 'JAN cannot start with 0');
//     return { ok: false, reason: 'starts-with-0' };
//   }

//   // Trim to maximum 13 digits (prevents typing beyond)
//   if (raw.length > 13) {
//     raw = raw.slice(0, 13);
//     input.value = raw;
//     setInvalid(input);
//     showInputError(input, 'JAN cannot exceed 13 digits');
//     return { ok: false, reason: 'too-long' };
//   }

//   input.value = raw; // keep input synced

//    if (raw.length === 0) {
//         setInvalid(input);
//         showInputError(input,"SKU JanCd is  cannot be empty. ")
//         return false;
//     }
//   // Empty check
//   if (raw.length === 0) {
//     setInvalid(input);
//     if (enforceExact13) {
//       showInputError(input, 'JAN cannot be empty');
//     }


   
//     return { ok: false, reason: 'empty' };
//   }

//   // If we require exact 13 (for save) but not yet 13, show message
//   if (enforceExact13 && raw.length !== 13) {
//     setInvalid(input);
//     showInputError(input, 'JAN must be exactly 13 digits');
//     return { ok: false, reason: 'not-13' };
//   }

//   // Not enforcing exact13 (live typing): if <13 then accept as "incomplete" but mark invalid
//   if (!enforceExact13) {
//     if (raw.length < 13) {
//       setInvalid(input);
//       // show a light temporary tooltip (don't spam). optional:
//       showInputError(input, `JAN incomplete (${raw.length}/13)`, { autoHide: 900 });
//       return { ok: false, reason: 'incomplete' };
//     }
//   }

//   // Exactly 13 digits => valid
//   if (raw.length === 13) {
//     setValid(input);
//     return { ok: true, exact13: true };
//   }

//   // Fallback (shouldn't reach)
 
//   setInvalid(input);
//     return false;
// }




         const janInput = document.getElementById('janInput');
        const janError = document.getElementById('janError');
        const submitButton = document.getElementById('submitButton');

function validateJanGeneric(input, { enforceExact13 = false } = {}) {
    if (!input) return { ok: false, reason: 'missing input', raw: '' };

    // 1. Sanitize: Keep digits 1-9 only (Removes non-digits AND the digit 0)
    let raw = input.value.replace(/[^1-9]/g, '');

    // Update input value immediately
    input.value = raw;
    
    // 2. Max length check
    if (raw.length > 13) {
        raw = raw.slice(0, 13);
        input.value = raw;
        setInvalid(input);
        showInputError(input, 'JAN cannot exceed 13 digits');
        return { ok: false, reason: 'too-long', raw };
    }

    // 3. Empty check
    if (raw.length === 0) {
        setInvalid(input);
        showInputError(input, 'JAN cannot be empty');
        return { ok: false, reason: 'empty', raw };
    }

    // NOTE: The "Leading zero check" (Step 4) is removed because 
    // the sanitization above makes it impossible for a 0 to exist.

    // --- Check for Validity based on length ---
    if (raw.length === 13) {
        setValid(input);
        return { ok: true, reason: 'valid', raw };
    }

    if (enforceExact13 && raw.length !== 13) {
        setInvalid(input);
        showInputError(input, `JAN must be 13 digits (currently ${raw.length})`);
        return { ok: false, reason: 'not-13', raw };
    }

    if (!enforceExact13 && raw.length < 13) {
        setInvalid(input);
        showInputError(input, `JAN incomplete (${raw.length}/13)`, { autoHide: 900 });
        return { ok: false, reason: 'incomplete', raw };
    }
    
    setInvalid(input);
    showInputError(input, 'Unknown validation error');
    return { ok: false, reason: 'unknown', raw };
}



        

// wrappers for existing names (keeps your code compatible)
function validateJanCode(input) {
  return validateJanGeneric(input, { enforceExact13: false }); // form-level live validation
}
function validateSkuJan(input) {
  return validateJanGeneric(input, { enforceExact13: false }); // live validation in modal
}

function validatePrice(input,maxDigits) {
    let raw = input.value.replace(/,/g, '').replace(/\D/g, '');

    if (!raw) {
        setInvalid(input);
        showInputError(input,"Price must be a number and canot be empty");
        return false;
    }
    if(raw.length > maxDigits){
        raw =raw.slice(0,maxDigits);
        showInputError(input,`Price cannot exceed ${maxDigits} digits`);
    }

    
    input.value = Number(raw).toLocaleString('ja-JP');
    input.style.textAlign = 'right';

    setValid(input);
    return true;
}
function validateColorDigits(input) {
    let originalVal = input.value;
    let val = originalVal.replace(/,/g, '').replace(/\D/g, '');
    
    // Check if input contains non-digit characters
    const containsNonDigits = originalVal !== val;
    
    if (containsNonDigits) {
        // Remove non-digit characters
        input.value = val;
        
        setInvalid(input);
        showInputError(input, "Color must be a number");
        return false;
    }
    
    // Empty
    if (val.length === 0) {
        setInvalid(input);
        showInputError(input, "Color cannot be empty");
        return false;
    }

    // Valid
    setValid(input);
    return true;
}
function validateSizeDigits(input) {
    let originalVal = input.value;
    let val = originalVal.replace(/,/g, '').replace(/\D/g, '');
    
    // Check if input contains non-digit characters
    const containsNonDigits = originalVal !== val;
    
    if (containsNonDigits) {
        // Remove non-digit characters
        input.value = val;
        
        setInvalid(input);
        showInputError(input, "Size must be a number");
        return false;
    }
    
    // Empty
    if (val.length === 0) {
        setInvalid(input);
        showInputError(input, "Size cannot be empty");
        return false;
    }

    // Valid
    setValid(input);
    return true;
}

function validateSkuDigits(input, fieldName = "Field") {
    let originalVal = input.value;
    let val = originalVal.replace(/,/g, '').replace(/\D/g, '');
    
    // Check if input contains non-digit characters
    const containsNonDigits = originalVal !== val;
    
    if (containsNonDigits) {
        // Remove non-digit characters
        input.value = val;
        
        setInvalid(input);
        showInputError(input, `${fieldName} must be a number`);
        return false;
    }
    
    // Empty
    if (val.length === 0) {
        setInvalid(input);
        showInputError(input, `${fieldName} cannot be empty`);
        return false;
    }

    // Valid
    setValid(input);
    return true;
}

function validateSkuJan(input) {
    let digits = input.value.replace(/\D/g, '');

    // ❌ Don't allow first digit = 0
    if (digits.startsWith("0")) {
        showInputError(input,"JAN cannot start with 0");
        digits = digits.substring(1); // remove the leading zero
    }

    // ❌ Limit to 13 digits max
    if (digits.length > 13) {
        digits = digits.substring(0, 13); // cut extra digits
        showInputError(input,"JAN cannot be more than 13 digits");
    }
if (digits.length < 13) {
        showInputError(input,"JAN cannot be Less than 13 digits");
    }
    input.value = digits;

    // ❌ If empty → invalid
    if (digits.length === 0) {
        setInvalid(input);
        showInputError(input,"SKU JanCd is  cannot be empty. ")
        return false;
    }

    // ❗ Exactly 13 digits = valid
    if (digits.length === 13) {
        setValid(input);
        return true;
    }

    // Otherwise incomplete → invalid
    setInvalid(input);
    return false;
}



function validateSkuRow(row) {
    const sizeName = row.querySelector('.size-name');
    const colorName = row.querySelector('.color-name');
    const sizeCode = row.querySelector('.size-code');
    const colorCode = row.querySelector('.color-code');
    const janCode = row.querySelector('.jan-code');
    const stock = row.querySelector('.stock-quantity');

    // Size Name
    if (!sizeName.value.trim()) {
        setInvalid(sizeName);
        showInputError(input,'Size Name is required.');
        validateItemNameLength(input)
    } else setValid(sizeName);

    // Color Name
    if (!colorName.value.trim()) {
        setInvalid(colorName);
        showInputError(input,'Color Name is required.');
    } else setValid(colorName);

    // Size Code
    if (!sizeCode.value.trim()) {
        setInvalid(sizeCode);
        showInputError(input,'Size Code is required.');
    } else setValid(sizeCode);

    // Color Code
    if (!colorCode.value.trim()) {
        setInvalid(colorCode);
        showInputError(input,'Color Code is required.');
    } else setValid(colorCode);

    // JAN Code
    validateSkuJan(janCode);

    // Stock
    if (stock.value === '' || isNaN(stock.value) || Number(stock.value) < 0) {
        setInvalid(stock);
        showInputError(input,'Stock must be a number and cannot be negative.');
    } else {
        setValid(stock);
    }
}
// --------------------
// DOM Loaded
// --------------------
document.addEventListener('DOMContentLoaded', () => {
    const itemCode = document.querySelector('input[name="Item_Code"]');
    const janCode = document.querySelector('input[name="JanCD"]');
    
    // 1. Separate variables for MakerName and Item_Name
    const makerNameInput = document.querySelector('input[name="MakerName"]');
    const itemNameInput = document.querySelector('textarea[name="Item_Name"]');
    
    const priceInputs = document.querySelectorAll('input[name="SalePrice"], input[name="ListPrice"], input[name="CostPrice"]');
    const memoInput = document.querySelector('textarea[name="Memo"]');

    // =========================================================================
    // ITEM CODE LISTENERS (No change needed here)
    // =========================================================================
    // Prevent typing spaces entirely
    itemCode.addEventListener('keydown', (e) => {
        if (e.key === ' ') e.preventDefault();
    });

    // Remove spaces on input (for paste/copy)
    itemCode.addEventListener('input', () => {
        const cursorPos = itemCode.selectionStart;
        itemCode.value = itemCode.value.replace(/\s+/g, '');
        itemCode.setSelectionRange(cursorPos, cursorPos);
        validateItemCode(itemCode);
    });

    itemCode.addEventListener('blur', () => {
        itemCode.value = itemCode.value.replace(/\s+/g, '');
        validateItemCode(itemCode);
    });

    // =========================================================================
    // JAN CODE LISTENERS (No change needed here)
    // =========================================================================
    // Note: Assuming validateJanCode now calls validateJanCDLength
    janCode.addEventListener('input', () => validateJanCode(janCode)); 
    janCode.addEventListener('blur', () => validateJanCode(janCode));

    // =========================================================================
    // REQUIRED TEXT LISTENERS (Replaced the generic loop)
    // =========================================================================
    
    // 2. Maker Name Listeners (Calls its specific length/required function)
    makerNameInput.addEventListener('input', () => validateMakerNameLength(makerNameInput));
    makerNameInput.addEventListener('blur', () => validateMakerNameLength(makerNameInput));
    
    // 3. Item Name Listeners (Calls its specific length/required function)
    itemNameInput.addEventListener('input', () => validateItemNameLength(itemNameInput));
    itemNameInput.addEventListener('blur', () => validateItemNameLength(itemNameInput));
    
    // =========================================================================
    // MEMO LISTENERS (No change needed here)
    // =========================================================================
    // Note: Assuming validateMemo now calls validateMemoLength
    memoInput.addEventListener('input',()=> validateMemo(memoInput)); 
    memoInput.addEventListener('blur', () => validateMemo(memoInput));


    // =========================================================================
    // PRICE LISTENERS (No change needed here)
    // =========================================================================
    priceInputs.forEach(input => {
        input.addEventListener('input', () => validatePrice(input, 9));
        input.addEventListener('blur', () => validatePrice(input, 9));
    });

    // =========================================================================
    // SKU LISTENERS (No change needed here)
    // =========================================================================
    document.querySelectorAll('.sku-row').forEach(row => {
        row.querySelectorAll('input').forEach(input => {
            // NOTE: validateSkuRow should internally call the specific length/digit validators
            input.addEventListener('input', () => validateSkuRow(row)); 
            input.addEventListener('blur', () => validateSkuRow(row));
        });
    });


});

// --------------------
// SKU Row Attach Validation
// --------------------
function attachSkuRowValidation(row) {
// --- 1. Size Name Validation (NVARCHAR(100) / 200 bytes) ---
    row.querySelectorAll('.size-name').forEach(input => {
        // Calls the correct function: validateSizeNameLength
        input.addEventListener('input', () => validateSizeNameLength(input));
        input.addEventListener('blur', () => validateSizeNameLength(input));
    });

    // --- 2. Color Name Validation (NVARCHAR(100) / 200 bytes) ---
    row.querySelectorAll('.color-name').forEach(input => {
        // Calls the correct function: validateColorNameLength
        input.addEventListener('input', () => validateColorNameLength(input));
        input.addEventListener('blur', () => validateColorNameLength(input));
    });
row.querySelectorAll('.size-code').forEach(input => {
        // Calls the correct function: validateSizeCodeLength
        input.addEventListener('input', () => validateSizeCodeLength(input));
        input.addEventListener('blur', () => validateSizeCodeLength(input));
    });

    // --- 4. Color Code Validation (CHAR(4) / 4 bytes) ---
    row.querySelectorAll('.color-code').forEach(input => {
        // Calls the correct function: validateColorCodeLength
        input.addEventListener('input', () => validateColorCodeLength(input));
        input.addEventListener('blur', () => validateColorCodeLength(input));
    });

    
    const skuJan = row.querySelector('.jan-code');
    if (skuJan) {
        skuJan.addEventListener('input', () => validateSkuJan(skuJan));
        skuJan.addEventListener('blur', () => validateSkuJan(skuJan));
    }

    const stock = row.querySelector('.stock-quantity')
    if(stock){
        stock.addEventListener('input',()=> validateStockLength(stock) );
        stock.addEventListener('blue', ()=> validateStockLength(stock));
    }
}

function sanitizeAllSkuFields() {
    document.querySelectorAll('#skuModalBody input').forEach(i => {
        i.value = i.value.trim();
        if (i.classList.contains('size-code') ||
            i.classList.contains('color-code') ||
            i.classList.contains('stock-quantity') ||
            i.classList.contains('jan-code')) {
            i.value = i.value.replace(/\D/g, '');
        }
    });
}

document.getElementById('saveSkusBtn').addEventListener('click', () => {
    sanitizeAllSkuFields();
    // closeSkuModal();
});



// length validation

function getStringByteLength(str) {
    if (!str) return 0;

    let length = 0;

    for (let i = 0; i < str.length; i++) {
        const codePoint = str.codePointAt(i);

       if (codePoint > 0xFFFF) {
            length += 2; // surrogate pair
            i++;
        } else {
            length += 1;
        }
    }

    return length;
}


function validateByteLength(input, maxChars, name, isRequired = true) {
    const value = input.value;

    if (isRequired && value.length === 0) {
        setInvalid(input);
        showInputError(input, `${name} is required.`);
        return false;
    }

    let chars = 0;
    let truncated = '';

    for (let i = 0; i < value.length; i++) {
        const codePoint = value.codePointAt(i);
        const charSize = codePoint > 0xFFFF ? 2 : 1;

        if (chars + charSize > maxChars) break;

        truncated += String.fromCodePoint(codePoint);
        chars += charSize;

        if (codePoint > 0xFFFF) i++;
    }

    if (chars < getStringByteLength(value)) {
        input.value = truncated;
        setInvalid(input);
        showInputError(input, `${name} exceeds ${maxChars} characters.`);
        return false;
    }

    setValid(input);
    return true;
}



//before change nothings.

// for english text
// function validateByteLength(input, maxBytes, name, isRequired = true) {
//     const maxChars = maxBytes / 2;
//     let value = input.value;

//     // 1. Check for OVER LIMIT
//     if (value.length > maxChars) {
//         input.value = value.substring(0, maxChars);
//         setInvalid(input); // Ensure this function exists or use showInputError
//         showInputError(input, `${name} cannot exceed ${maxChars} characters.`);
        
//         // STOP HERE! Don't let the code reach setValid()
//         return false; 
//     }

//     // 2. Check for REQUIRED
//     if (isRequired && value.length === 0) {
//         setInvalid(input);
//         showInputError(input, `${name} is required.`);
//         return false;
//     }

//     // 3. If it got this far, it is VALID
//     setValid(input);
//     return true;
// }








function validateItemCodeLength(input) {
    // NVARCHAR(50) = 100 bytes
    return validateByteLength(input, 50, "Item Code", true); 
    console.log("length is working")
}

function validateItemNameLength(input) {
    // NVARCHAR(100) = 200 bytes
    if (!validateRequiredText(input)) {
        return false;
    }
    return validateByteLength(input, 100, "Item Name", true);
}


function validateMakerNameLength(input) {
    // NVARCHAR(50) = 100 bytes
    if (!validateRequiredText(input)) {
        return false;
    }
    return validateByteLength(input, 50, "Maker Name", false); // Assuming not required
}

    function validateMemoLength(input) {
        // NVARCHAR(255) = 510 bytes
        return validateByteLength(input, 255, "Memo", false); // Assuming not required
    }

function validateSizeCodeLength(input) {
    // CHAR(4) = 4 bytes
    if (!validateSkuDigits(input)) {
        return false;
    }
    return validateByteLength(input, 4, "Size Code", true); 
}

function validateColorCodeLength(input) {
    // CHAR(4) = 4 bytes
    if (!validateSkuDigits(input)) {
        return false;
    }
    return validateByteLength(input, 4, "Color Code", true); 
}

function validateSizeNameLength(input) {
    // NVARCHAR(100) = 200 bytes

    if (!validateRequiredText(input)) {
        return false;
    }
    return validateByteLength(input, 100, "Size Name", false); // Assuming not required
}

function validateColorNameLength(input) {
    // NVARCHAR(100) = 200 bytes
     if (!validateRequiredText(input)) {
        return false;
    }
    return validateByteLength(input, 100, "Color Name", false); // Assuming not required
}


function validateStockLength(input) {
    // 1. Remove non-numeric characters
    input.value = input.value.replace(/\D/g, '');

    // 2. Set the limit (9 is safest for INT, 10 is absolute max)
    const MAX_LENGTH = 9; 

    // 3. RESTRICTION: If length exceeds limit, truncate and show error
    if (input.value.length > MAX_LENGTH) {
        input.value = input.value.slice(0, MAX_LENGTH); 
        setInvalid(input);
        showInputError(input, `Stock cannot exceed ${MAX_LENGTH} digits.`);
        return false;
    }

    // 4. Required check
    if(!validateRequiredText(input)){
        return false;
    }

    // 5. Standard validation (if it passes everything above)
    setValid(input);
    return true;
}

function validateSkuJanCodeLength(input) {
    
    return validateJanCDLength(input);
}


function validateFile(input) {
    const file = input.files[0];
    if (file) {
        const allowedExtensions = /(\.jpg|\.jpeg)$/i;
        if (!allowedExtensions.exec(file.name)) {
            alert('Only JPG files are allowed!');
            input.value = '';
            return false;
        }
    }
}
function submitButtonValidate(){
        const janCode = document.getElementById("jan-code")
        const submitButton = document.getElementById("submitButton");
        if(janCode == null){
            submitButton.disable = true;
        }
                    submitButton.disable = false;
    }

    


