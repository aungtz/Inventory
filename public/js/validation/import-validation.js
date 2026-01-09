function validateImportedRows(rows) {
    const jpRegex = /[\u3000-\u30FF\u4E00-\u9FFF\uFF00-\uFFEF]/;
    const spaceRegex = /\s/;

    return rows.map((raw, index) => {
        let errors = [];
        let warnings = [];
        let vaild = [];

        const lineNo = index + 1;
        const rawAdminCode = raw.Item_AdminCode ?? raw["Item_AdminCode"] ?? raw["Item_Admin_Code"] ?? "";

        // If it's empty, send an empty string so the SP knows it's a NEW record
        const Item_AdminCode = rawAdminCode !== "" ? rawAdminCode.toString().trim() : "";
        // ------------------------------------------
        // Normalize Excel column names ↓↓↓
        // ------------------------------------------
        const row = {
            Item_AdminCode: Item_AdminCode, 
            Item_Code: (raw.Item_Code ?? raw["Item Code"] ?? raw["item_code"] ?? "").toString(),
            Item_Name: (raw.Item_Name ?? raw["Item Name"] ?? raw["item_name"] ?? "").toString(),
            JanCD: (raw.JanCD ?? raw["Jan Code"] ?? raw["JAN"] ?? "").toString(),
            MakerName: (raw.MakerName ?? raw["Maker Name"] ?? raw["maker_name"] ?? "").toString(),
            Memo: (raw.Memo ?? raw["Description"] ?? "").toString(),
            ListPrice: (raw.ListPrice ?? raw["List Price"] ?? raw["List_Price"] ?? "").toString(),
            SalePrice: (raw.SalePrice ?? raw["Sale Price"] ?? raw["Sale_Price"] ?? "").toString(),
        };

        // ------------------------------------------
        // Force scientific notation → full number string
        // ------------------------------------------
        function fixExcelNumber(val) {
            if (!val) return "";
            val = val.toString();

            // Excel scientific notation like "1.2345E+12"
            if (/e\+/i.test(val)) {
                return Number(val).toString();
            }
            return val;
        }

        row.JanCD = fixExcelNumber(row.JanCD);
        row.ListPrice = row.ListPrice.replace(/,/g, "").trim() ? row.ListPrice : "";
        row.SalePrice = row.SalePrice.replace(/,/g, "").trim() ? row.SalePrice : "";

        // Trim all safely
        const Item_Code = row.Item_Code.trim();
        const Item_Name = row.Item_Name.trim();
        const JanCD = row.JanCD.trim();
        const MakerName = row.MakerName.trim();
        const Memo = row.Memo.trim();
        const ListPrice = row.ListPrice.trim();
        const SalePrice = row.SalePrice.trim();

        // -------------------------
        // 1. Item_Code
        // -------------------------
        // if (!Item_Code) {
        //     errors.push("Item_Code is required");
        // } else {
        //     if (spaceRegex.test(Item_Code)) errors.push("Item_Code cannot contain spaces");
        //     if (jpRegex.test(Item_Code)) errors.push("Item_Code cannot contain Japanese characters");
        //     if (Item_Code.length > 50) errors.push("Item_Code max length is 50");
        //     if (!/^[A-Za-z0-9\-_]+$/.test(Item_Code))
        //         errors.push("Item_Code allowed: A-Z, 0-9, -, _");
        // }

        // -------------------------
        // 2. Item_Name
        // -------------------------
        if (!Item_Name) {
            errors.push("Item_Name is required");
        } else if (Item_Name.length > 255) {
            errors.push("Item_Name max length is 255");
        }

        // -------------------------
        // 3. JanCD
        // -------------------------
        if (JanCD !== "") {
            let janStr = JanCD.replace(/,/g, "");
            if (!/^[0-9]+$/.test(janStr)) {
                errors.push("JanCD must contain digits only");
            } else if (!(janStr.length === 8 || janStr.length === 13)) {
                errors.push(`JAN Code must be 8 or 13 digits (got ${janStr.length})`);
            }
        }

        // -------------------------
        // 4. MakerName
        // -------------------------
        if (!MakerName) {
            errors.push("MakerName is required");
        } else if (MakerName.length > 255) {
            errors.push("MakerName max length is 255");
        }

        // -------------------------
        // 5. Memo
        // -------------------------
        if (Memo && Memo.length > 500) {
            errors.push("Memo max length is 500");
        }

        // -------------------------
        // 6. ListPrice  (keep commas)
        // -------------------------
        if (!ListPrice) {
            errors.push("ListPrice is required");
        } else {
            const numCheck = ListPrice.replace(/,/g, "");
            if (!/^[0-9]+(\.[0-9]+)?$/.test(numCheck)) {
                errors.push("ListPrice must be a valid number");
            }
        }

        // -------------------------
        // 7. SalePrice (keep commas)
        // -------------------------
        if (!SalePrice) {
            errors.push("SalePrice is required");
        } else {
            const numCheck = SalePrice.replace(/,/g, "");
            if (!/^[0-9]+(\.[0-9]+)?$/.test(numCheck)) {
                errors.push("SalePrice must be a valid number");
            } else if (parseFloat(numCheck) === 0) {
                warnings.push("SalePrice is zero — check if intentional");
            }
        }

        // Status decision
        let status = "Valid";
        if (errors.length > 0) status = "Error";
        else if (warnings.length > 0) status = "Warning";

        return {
            lineNo,
            ...row,
            errors,
            warnings,
            status
        };
    });
}


function parseAndValidate(file) {
    const reader = new FileReader();

    reader.onload = function (e) {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });

        const firstSheet = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[firstSheet];

        const importedData = XLSX.utils.sheet_to_json(worksheet, {
            defval: ""   // <-- CRITICAL
        });

        // Validate
        const validatedRows = validateImportedRows(importedData);

        // Save
        sessionStorage.setItem('previewData', JSON.stringify(validatedRows));

        // Check status counts
        const hasError = validatedRows.some(r => r.status === "Error");
        const hasWarning = validatedRows.some(r => r.status === "Warning");

        let message = "";

        // if (hasError) {
        //     message = "Some rows contain ERRORS.\n\nYou can review them in the preview page.";
        // } else if (hasWarning) {
        //     message = "Import contains WARNING rows.\n\nYou can review them in the preview page.";
        // } else {
        //     message = "All rows are valid!\n\nClick OK to continue.";
        // }

        // // Show alert, then redirect
        // alert(message);
        window.location.href = '/itemPreview';
    };

    reader.readAsArrayBuffer(file);
}

function validateSKUImported(rows) {
    const jpRegex = /[\u3000-\u30FF\u4E00-\u9FFF\uFF00-\uFFEF]/;
    const spaceRegex = /\s/;

    function fixExcelNumber(val) {
        if (!val) return "";
        val = val.toString();
        if (/e\+/i.test(val)) return Number(val).toString(); // Excel scientific notation fix
        return val;
    }

    return rows.map((raw, index) => {
        let errors = [];
        let warnings = [];

        const lineNo = index + 1;

        // Normalize column names
        const row = {
             Item_AdminCode: (raw.Item_AdminCode ?? raw["Item_AdminCode"] ?? raw["Item_Admin_Code"] ?? "").toString(),
            Item_Code: (raw.Item_Code ?? raw["Item_Code"] ?? "").toString(),
            SizeName: (raw.SizeName ?? raw["Size_Name"] ?? "").toString(),
            ColorName: (raw.ColorName ?? raw["Color_Name"] ?? "").toString(),
            SizeCode: (raw.SizeCode ?? raw["Size_Code"] ?? raw.size_code ?? "").toString(),
            ColorCode: (raw.ColorCode ?? raw["Color_Code"] ?? raw.color_code ?? "").toString(),
            JanCD: fixExcelNumber(raw.JanCD ?? raw["JanCode"] ?? raw["JAN"] ?? ""),
            Quantity: (raw.Quantity ?? raw["Quantity"] ?? raw["Quantity"] ?? "").toString(),
        };

        // Trim safely
        const Item_Code = row.Item_Code.trim();
        const SizeName = row.SizeName.trim();
        const ColorName = row.ColorName.trim();
        const SizeCode = row.SizeCode.trim();
        const ColorCode = row.ColorCode.trim();
        const JanCD = row.JanCD.trim();
        const Quantity = row.Quantity.trim();

        // -----------------------------
        // 1. Item_Code
        // -----------------------------
        // if (!Item_Code) {
        //     errors.push("Item Code is required");
        // } else {
        //     if (spaceRegex.test(Item_Code)) errors.push("Item Code cannot contain spaces");
        //     if (jpRegex.test(Item_Code)) errors.push("Item Code cannot contain Japanese characters");
        // }

        // -----------------------------
        // 2. Size Name
        // -----------------------------
        if (!SizeName) errors.push("Size Name is required");
        else if (SizeName.length > 50) errors.push("Size Name max length is 50");

        // -----------------------------
        // 3. Color Name
        // -----------------------------
        if (!ColorName) errors.push("Color Name is required");
        else if (ColorName.length > 50) errors.push("Color Name max length is 50");

        // -----------------------------
        // 4. Size Code
        // -----------------------------
        if (!SizeCode) errors.push("Size Code is required");
        else if (!/^[0-9]+$/.test(SizeCode)) errors.push("Size Code must be digits only");

        // -----------------------------
        // 5. Color Code
        // -----------------------------
        if (!ColorCode) errors.push("Color Code is required");
        else if (!/^[0-9]+$/.test(ColorCode)) errors.push("Color Code must be digits only");

        // -----------------------------
        // 6. Jan Code
        // -----------------------------
        if (JanCD !== "") {
            if (!/^[0-9]+$/.test(JanCD)) {
                errors.push("JAN Code must contain digits only");
            } else if (!(JanCD.length === 8 || JanCD.length === 13)) {
                errors.push(`JAN Code must be 8 or 13 digits (got ${JanCD.length})`);
            }
        }

        // -----------------------------
        // 7. Quantity
        // -----------------------------
        if (!Quantity) {
            errors.push("Quantity is required");
        } else if (!/^[0-9]+$/.test(Quantity)) {
            errors.push("Quantity must be a number");
        } else if (parseInt(Quantity) === 0) {
            warnings.push("Quantity is zero — check if intentional");
        }

        let status = "Valid";
        if (errors.length > 0) status = "Error";
        else if (warnings.length > 0) status = "Warning";

        return {
            lineNo,
            ...row,
            errors,
            warnings,
            status
        };
    });
}

function parseAndValidateSKU(file) {
    const reader = new FileReader();

    reader.onload = function (e) {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });

        const firstSheet = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[firstSheet];

        const importedData = XLSX.utils.sheet_to_json(worksheet, {
            defval: ""   // <-- CRITICAL
        });

        // Validate (SKU version)
        const validatedRows = validateSKUImported(importedData);

        // Save to session for preview page
        sessionStorage.setItem('skuPreviewData', JSON.stringify(validatedRows));

        // Status counts
       sendPreviewToBackend().then(() =>{
        window.location.href = "/skuPreview"
       })
       
    };

    reader.readAsArrayBuffer(file);
}

function sendPreviewToBackend() {
    const skuPreviewData = JSON.parse(sessionStorage.getItem('skuPreviewData') || '[]');
    if (!skuPreviewData.length) return Promise.resolve();

    return fetch('/sku-import/validate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ rows: skuPreviewData })
    })
    .then(res => res.json())
    .then(spErrors => {

        spErrors.forEach(err => {
    const row = skuPreviewData.find(r => r.lineNo == err.LineNo);
    if (!row) return;

    row.errors = row.errors || [];

    if (!row.errors.includes(err.ErrorMessage)) {
        row.errors.push(err.ErrorMessage);
    }

    row.status = "Error";
});


        sessionStorage.setItem('skuPreviewData', JSON.stringify(skuPreviewData));
    });
}
// import-validation.js (TOP of file)

const SKU_HEADERS = [
    "Item_Code",
    "Size_Name",
    "Color_Name",
    "Size_Code",
    "Color_Code",
    "JanCode",
    "Quantity"
];

const ITEM_HEADERS = [
    "Item_Code",
    "Item_Name",
    "JanCD",
    "MakerName",
    "ListPrice",
    "SalePrice"
];

function validateExcelHeaders(file, expectedHeaders) {
    return new Promise((resolve) => {
        const reader = new FileReader();

        reader.onload = function (e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: "array" });
            const sheet = workbook.Sheets[workbook.SheetNames[0]];

            const headers = XLSX.utils.sheet_to_json(sheet, { header: 1 })[0] || [];

            const normalized = headers.map(h =>
                h?.toString().trim().toLowerCase()
            );

            const missing = expectedHeaders.filter(h =>
                !normalized.includes(h.toLowerCase())
            );

            if (missing.length > 0) {
                alert(
                    "Invalid file format.\n\nMissing columns:\n" +
                    missing.join(", ")
                );
                resolve(false); // ❌ STOP
            } else {
                resolve(true); // ✅ OK
            }
        };

        reader.readAsArrayBuffer(file);
    });
}

async function runUpload(file, expectedHeaders, onSuccess) {
    if (!file) return;

    uploadProgress.style.display = 'block';
    progressBar.style.width = '0%';
    progressPercent.textContent = '0%';
    submitBtn.disabled = true;
    submitBtn.innerHTML =
        '<i class="fas fa-spinner fa-spin mr-2"></i> Uploading...';

    let progress = 0;

    const interval = setInterval(() => {
        progress += Math.random() * 10;
        if (progress > 100) progress = 100;

        progressBar.style.width = progress + '%';
        progressPercent.textContent = Math.round(progress) + '%';

        if (progress >= 100) {
            clearInterval(interval);
        }
    }, 200);

    // 🔥 WAIT FOR HEADER VALIDATION
    const isValid = await validateExcelHeaders(file, expectedHeaders);

    if (!isValid) {
        clearInterval(interval);
        resetUploadUI();      // 🔥 STOP HERE
        return;               // ❌ NO PREVIEW PAGE
    }

    onSuccess(file);
}

function resetUploadUI() {
    uploadProgress.style.display = 'none';
    progressBar.style.width = '0%';
    progressPercent.textContent = '0%';
    submitBtn.disabled = false;
    submitBtn.innerHTML = 'Upload';
}


