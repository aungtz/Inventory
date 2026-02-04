<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; 
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Sku;
use App\Models\ItemImage;
use App\Exports\ItemExport; // Correct import




class ItemController extends Controller
{
    public function create(){
        return view('items.itemsCreate');
    }

public function store(Request $request)
{
    // -----------------------------
    // 1️⃣ VALIDATION
    // -----------------------------
    $request->validate([
    'Item_Code'  => 'required|string|max:50',
    'Item_Name'  => 'required|string|max:100',
    'JanCD'      => 'required|string|size:13',
    'MakerName'  => 'required|string|max:50',
    'Memo'       => 'nullable|string|max:255',
    'ListPrice'  => 'required|numeric|min:0',
    'SalePrice'  => 'nullable|numeric|min:0',
    'images.*'   => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
]);

\Log::info('REQUEST DATA', $request->all());
\Log::info('FILES', $request->file());
        // -----------------------------
    // 2️⃣ PREPARE IMAGE FILES
    // -----------------------------
    $finalImageRecords = [];
    $disk = Storage::disk('public');

    if ($request->hasFile('images')) {
       $serial = 1;

            foreach ($request->file('images') as $image) {

                if (! $image || ! $image->isValid()) continue;

                $ext = strtolower($image->getClientOriginalExtension());

                $base = preg_replace('/[^A-Za-z0-9\-_]+/', '-', $request->Item_Code);
                $base = trim($base, '-_');
                if ($base === '') $base = 'item';

                $filename = "{$base}-{$serial}.{$ext}";
                $path = "items/{$filename}";

                $counter = 1;
                while ($disk->exists($path)) {
                    $filename = "{$base}-{$serial}-{$counter}.{$ext}";
                    $path = "items/{$filename}";
                    $counter++;
                }

                $image->storeAs('items', $filename, 'public');

                $finalImageRecords[] = [
                    'fileName' => $filename,
                    'serial'   => $serial
                ];

                $serial++; // ✅ increment by upload order
            }

    }

    // -----------------------------
    // 3️⃣ JSON PREPARATION
    // -----------------------------
    $skusJson   = $request->filled('skus_json') ? $request->skus_json : null;
    $imagesJson = !empty($finalImageRecords)
        ? json_encode($finalImageRecords)
        : null;

    // -----------------------------
    // 4️⃣ CALL STORED PROCEDURE
    // -----------------------------
    $result = DB::select(
        'EXEC sp_InsertItemWithSkusAndImages ?,?,?,?,?,?,?,?,?,?,?',
        [
            $request->Item_Code,
            $request->Item_Name,
            $request->JanCD,
            $request->MakerName,
            $request->Memo,
            $request->ListPrice,
            $request->SalePrice ?? $request->ListPrice,
             'system',
            'system',
            $skusJson,
            $imagesJson
        ]
    );

    // Optional: get Item ID
    $itemID = $result[0]->NewItemID ?? null;

    // -----------------------------
    // 5️⃣ REDIRECT
    // -----------------------------
    return redirect()
        ->route('itemList')
        ->with('success', 'Item saved successfully!');
}
public function itemList(Request $request)
{
    $sort = $request->query('sort', null);
    $direction = $request->query('direction', 'asc') === 'desc' ? 'desc' : 'asc';
    
    // Only allow specific columns for sorting
    $allowedSorts = ['item_code'];
    if ($sort && !in_array($sort, $allowedSorts)) {
        $sort = null;
    }
    
    $pdo = DB::getPdo();
    
    // Call stored procedure with sort parameters
    $stmt = $pdo->prepare("EXEC sp_GetAllItemData @Sort = :sort, @Direction = :direction");
    $stmt->bindValue(':sort', $sort ?: null, \PDO::PARAM_STR);
    $stmt->bindValue(':direction', $direction ?: 'asc', \PDO::PARAM_STR);
    $stmt->execute();
    
    $items = $stmt->fetchAll(\PDO::FETCH_OBJ);
    
    $stmt->nextRowset();
    $images = $stmt->fetchAll(\PDO::FETCH_OBJ);
    
    $stmt->nextRowset();
    $skus = $stmt->fetchAll(\PDO::FETCH_OBJ);
    
    $skuMatrixByItem = [];
    
    foreach ($skus as $sku) {
        $itemCode = isset($sku->Item_Code) ? trim((string)$sku->Item_Code) : null;
        
        if (!$itemCode) continue;
        
        $sizeKey  = trim((string)$sku->Size_Code);
        $colorKey = trim((string)$sku->Color_Code);
        
        if (!isset($skuMatrixByItem[$itemCode])) {
            $skuMatrixByItem[$itemCode] = [
                'colors' => [],
                'sizes'  => [],
                'matrix' => []
            ];
        }
        
        $skuMatrixByItem[$itemCode]['colors'][$colorKey] = $sku->Color_Name;
        $skuMatrixByItem[$itemCode]['sizes'][$sizeKey]   = $sku->Size_Name;
        $skuMatrixByItem[$itemCode]['matrix'][$sizeKey][$colorKey] = $sku;
    }
    
    // Pass sort parameters to view
    return view('items.itemList', compact('items', 'images', 'skuMatrixByItem', 'sort', 'direction'));
}


public function skuList(Request $request)
{
    $sort      = $request->query('sort');        // item_code
    $direction = $request->query('direction', 'asc');

    $query = DB::table('M_SKU');

    if ($sort === 'item_code') {
        $query->orderByRaw("
            CASE 
                WHEN Item_Code LIKE '%[0-9]%' AND CHARINDEX('-', Item_Code) > 0
                THEN LEFT(Item_Code, CHARINDEX('-', Item_Code) - 1)
                WHEN Item_Code LIKE '%[0-9]%'
                THEN LEFT(Item_Code, PATINDEX('%[0-9]%', Item_Code) - 1)
                ELSE Item_Code
            END {$direction},
            CASE 
                WHEN Item_Code LIKE '%[0-9]%' AND CHARINDEX('-', Item_Code) > 0
                THEN TRY_CAST(SUBSTRING(
                    Item_Code,
                    CHARINDEX('-', Item_Code) + 1,
                    LEN(Item_Code)
                ) AS INT)
                WHEN Item_Code LIKE '%[0-9]%'
                THEN TRY_CAST(SUBSTRING(
                    Item_Code,
                    PATINDEX('%[0-9]%', Item_Code),
                    LEN(Item_Code)
                ) AS INT)
                ELSE 0
            END {$direction},
            Item_Code {$direction}
        ");
    } else {
        $query->orderBy('UpdatedDate', 'desc');
    }

    $skus = $query->paginate(10)->withQueryString();

    \Log::info('SKU pagination', [
        'page' => $skus->currentPage(),
        'sort' => $sort,
        'dir'  => $direction
    ]);

    return view('items.skuList', compact('skus', 'sort', 'direction'));
}

public function getSkuMatrix(Request $request)
{
    $itemCode = $request->item_code;
    
    // Fetch only the SKUs for this specific item
    $skus = DB::table('M_SKU')
        ->where('Item_Code', $itemCode)
        ->get();

    $data = [
        'colors' => [],
        'sizes'  => [],
        'matrix' => []
    ];

    foreach ($skus as $sku) {
        $sizeKey  = trim((string)$sku->Size_Code);
        $colorKey = trim((string)$sku->Color_Code);

        // Map names and codes
        $data['colors'][$colorKey] = $sku->Color_Name;
        $data['sizes'][$sizeKey]   = $sku->Size_Name;
        
        // Build the intersection
        $data['matrix'][$sizeKey][$colorKey] = [
            'Quantity' => (float)$sku->Quantity,
            'JanCode'  => $sku->JanCode
        ];
    }

    // Return as JSON so JavaScript can build the table
    return response()->json($data);
}


public function updateStock(Request $request)
{
    $quantities = $request->input('quantities');

    if (!$quantities || count($quantities) === 0) {
        Log::warning('UpdateStock: No quantities submitted', [
            'request_quantities' => $quantities,
            'user_id' => auth()->id(),
        ]);

        return back()->with('error', 'Please check at least one item to update.');
    }

    try {
        DB::beginTransaction();

        Log::info('UpdateStock started', [
            'count' => count($quantities),
            'user_id' => auth()->id(),
        ]);

        foreach ($quantities as $adminCode => $quantity) {
            Log::info('Updating SKU stock', [
                'Item_AdminCode' => $adminCode,
                'Quantity' => $quantity,
            ]);

            DB::table('M_SKU')
                ->where('Item_AdminCode', $adminCode)
                ->update([
                    'Quantity'   => $quantity,
                    'UpdatedDate' => now()
                ]);
        }

        DB::commit();

        Log::info('UpdateStock committed successfully', [
            'updated_rows' => count($quantities),
        ]);

        return back()->with('success', 'Successfully updated ' . count($quantities) . ' items.');

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('UpdateStock failed', [
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        ]);

        return back()->with('error', 'Update failed: ' . $e->getMessage());
    }
}


public function update(Request $request, $id)
{
            try {
                $validated = $request->validate([
            'Item_Code'   => 'required|string|max:50|unique:M_Item,Item_Code,' . $id . ',Item_Code',
            'Item_Name'   => 'required|string|max:100',
            'JanCD'       => 'required|string|size:13',
            'MakerName'   => 'required|string|max:50',
            'Memo'        => 'nullable|string|max:255',
            'ListPrice'   => 'required|numeric|min:0',
            'SalePrice'   => 'required|numeric|min:0',
            'images_json' => 'nullable|string',
            'skus_json'   => 'nullable|string'
        ]);

        \Log::info('REQUEST DATA', $request->all());


        $images = json_decode($request->images_json ?? '[]', true) ?: [];
        
        $skus   = json_decode($request->skus_json ?? '[]', true) ?: [];

        $existingSkuAdminCodes = DB::table('M_SKU')
            ->where('Item_Code', $validated['Item_Code'])
            ->pluck('Item_AdminCode')
            ->toArray();



        $skus = array_map(function ($sku, $index) use ($existingSkuAdminCodes) {

            // 1️⃣ Add Item_AdminCode
            // Existing SKU → use existing admin code
            // New SKU → set NULL (SQL will insert)
            $sku['itemAdminCode'] = $sku['itemAdminCode']
                ?? ($existingSkuAdminCodes[$index] ?? null);

            // 2️⃣ Normalize sizeCode
            if (isset($sku['sizeCode'])) {
                $sku['sizeCode'] = str_pad((string)(int)$sku['sizeCode'], 4, '0', STR_PAD_LEFT);
            }

            // 3️⃣ Normalize colorCode
            if (isset($sku['colorCode'])) {
                $sku['colorCode'] = str_pad((string)(int)$sku['colorCode'], 4, '0', STR_PAD_LEFT);
            }

            return $sku;

        }, $skus, array_keys($skus));



        // --- SKU DEBUG LOG START ---
        \Log::info("--- SKU DEBUG TRACE (ID: {$id}) ---");
        \Log::info("RAW SKUs from Request: " . ($request->skus_json ?? 'EMPTY'));
        \Log::info("Decoded SKU Count: " . count($skus));
        
        foreach ($skus as $index => $sku) {
            $state = $sku['state'] ?? 'existing';
             \Log::info(
        "SKU data log[{$index}]: Admin={$sku['itemAdminCode']} | {$sku['sizeCode']}-{$sku['colorCode']} | State=" . ($sku['state'] ?? 'existing')
    );
        }
        // --- SKU DEBUG LOG END ---

        foreach ($images as &$img) {

    // ✅ serial must come from frontend
    if (!isset($img['serial'])) {
        // safety fallback (should NOT happen)
        continue;
    }

    $imageSerial = (int)$img['serial'];

    if (($img['state'] ?? '') === 'new') {

        $fileKey = 'product_image_file_' . $imageSerial;

        if ($request->hasFile($fileKey) && $request->file($fileKey)->isValid()) {

            $file = $request->file($fileKey);
            $ext  = $file->getClientOriginalExtension();

            $newImageName = $validated['Item_Code'] . "-" . $imageSerial . "." . $ext;

            // delete old image if exists
            $oldImage = DB::table('M_ItemImage')
                ->where('Item_Code', $validated['Item_Code'])
                ->where('Image_Serial', $imageSerial)
                ->value('Image_Name');

            if ($oldImage && Storage::disk('public')->exists("items/" . $oldImage)) {
                Storage::disk('public')->delete("items/" . $oldImage);
            }

            Storage::disk('public')->putFileAs('items', $file, $newImageName);

            $img['Image_Name'] = $newImageName;
            $img['path'] = $newImageName;

        } else {
            $img['state'] = 'skip';
        }

    } elseif (($img['state'] ?? '') === 'delete') {

        $oldImage = DB::table('M_ItemImage')
            ->where('Item_Code', $validated['Item_Code'])
            ->where('Image_Serial', $imageSerial)
            ->value('Image_Name');

        if ($oldImage && Storage::disk('public')->exists("items/" . $oldImage)) {
            Storage::disk('public')->delete("items/" . $oldImage);
        }
    }
}


        $images = array_filter($images, fn($img) => ($img['state'] ?? null) !== 'skip');

        \DB::statement("EXEC M_Item_UpdateItemWithSkusAndImages @Item_Code=?, @Item_Name=?, @JanCD=?, @MakerName=?, @ListPrice=?,@SalePrice=?, @Memo=?, @ImagesJson=?, @SkusJson=?", [
            $validated['Item_Code'], $validated['Item_Name'], $validated['JanCD'],
            $validated['MakerName'], $validated['ListPrice'],$validated['SalePrice'], $validated['Memo'],
            json_encode(array_values($images)), json_encode($skus)
        ]);

        return $request->ajax() ? response()->json(['success' => true, 'message' => 'Updated successfully']) : redirect()->route('/itemList');

    } catch (\Exception $e) {
        \Log::error("--- CRITICAL ERROR in Update: " . $e->getMessage());
        return $request->ajax() ? response()->json(['success' => false, 'message' => $e->getMessage()], 500) : redirect()->back();
    }
}





// update controller codes.
// public function update(Request $request, $id)
// {
//     \Log::info('📦 Update Request Data: ' . json_encode($request->all()));
    
//     // Debug uploaded files
//     $uploadedFiles = $request->file('images', []);
//     \Log::info('📁 Uploaded files count: ' . count($uploadedFiles));
    
//     foreach ($uploadedFiles as $key => $file) {
//         if ($file) {
//             \Log::info("  File [{$key}]: {$file->getClientOriginalName()}, Size: {$file->getSize()} bytes, Valid: " . ($file->isValid() ? 'yes' : 'no'));
//         }
//     }

//     try {
//         $item = Item::findOrFail($id);
        
//         // Log current images in database BEFORE update
//         \Log::info('📊 Current images in database BEFORE update:');
//         $currentImages = ItemImage::where('Item_Code', $item->Item_Code)->get();
//         foreach ($currentImages as $img) {
//             \Log::info("  Slot {$img->slot}: {$img->Image_Name} -> {$img->path}");
//         }

//         // Validate request - USING CORRECT VALIDATOR
//         $validator = \Validator::make($request->all(), [  // Added backslash
//             'Item_Code' => 'required|string|max:255|unique:M_Item,Item_Code,' . $id . ',Item_Code',
//             'Item_Name' => 'required|string|max:255',
//             'JanCD' => 'required|string|max:13',
//             'MakerName' => 'required|string|max:255',
//             'ListPrice' => 'required|numeric',
//             'Memo' => 'nullable|string',
//             'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
//         ]);

//         if ($validator->fails()) {
//             \Log::error('❌ Validation failed:', $validator->errors()->toArray());
            
//             if ($request->ajax() || $request->wantsJson()) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Validation failed',
//                     'errors' => $validator->errors()
//                 ], 422);
//             }
//             return redirect()->back()->withErrors($validator)->withInput();
//         }

//         $validated = $validator->validated();

//         // Update item
//         $item->update($validated);

//         $imageStates = $request->input('imageStates', []);
//         $imageNames  = $request->input('imageNames', []);
        
//         \Log::info('🎯 Image States: ' . json_encode($imageStates));
//         \Log::info('🏷️  Image Names: ' . json_encode($imageNames));

//         foreach ($imageStates as $slot => $state) {
//             $slot = (int)$slot;
//             $imageName = $imageNames[$slot] ?? null;
//             $file = $uploadedFiles[$slot] ?? null;

//             \Log::info("🔄 Processing slot {$slot}: state={$state}, imageName={$imageName}, hasFile=" . ($file ? 'yes' : 'no'));

//             // Get existing image for this slot
//             $existingImage = ItemImage::where('Item_Code', $item->Item_Code)
//                                       ->where('slot', $slot)
//                                       ->first();

//             \Log::info("  Existing image in DB: " . ($existingImage ? "Yes (ID: {$existingImage->id})" : "No"));

//             if ($state === 'delete') {
//                 if ($existingImage) {
//                     \Log::info("  Deleting image: {$existingImage->path}");
//                     if (!empty($existingImage->path) && \Storage::disk('public')->exists($existingImage->path)) {
//                         \Storage::disk('public')->delete($existingImage->path);
//                         \Log::info("  File deleted from storage");
//                     }
//                     $existingImage->delete();
//                     \Log::info("✅ Deleted image in slot {$slot}");
//                 } else {
//                     \Log::info("  No image to delete in slot {$slot}");
//                 }
//             } 
//             elseif ($state === 'new') {
//                 if ($file && $file->isValid()) {
//                     \Log::info("  Processing new file: {$file->getClientOriginalName()}");
                    
//                     // Delete old image if exists
//                     if ($existingImage && !empty($existingImage->path)) {
//                         \Log::info("  Deleting old image: {$existingImage->path}");
//                         if (\Storage::disk('public')->exists($existingImage->path)) {
//                             \Storage::disk('public')->delete($existingImage->path);
//                             \Log::info("  Old file deleted from storage");
//                         }
//                     }
//                     // OLD CODE:
//                     // Save new file
//                     $userFileName = $imageName ?: $file->getClientOriginalName();
//                     $uniqueStoredName = time() . '_' . uniqid() . '_' . $userFileName;
//                     $path = $file->storeAs('items', $uniqueStoredName, 'public');

//                     // NEW CODE:
//                     // Save new file with original name
//                     $userFileName = $imageName ?: $file->getClientOriginalName();
//                     $path = $file->storeAs('items', $userFileName, 'public');
                    

 
                    
//                     \Log::info("  Saved new file to: {$path}");
//                     \Log::info("  File exists in storage: " . (\Storage::disk('public')->exists($path) ? 'Yes' : 'No'));
                    
//                     // Create or update
//                     if ($existingImage) {
//                         $existingImage->update([
//                             'Image_Name' => $userFileName,
//                             'path' => $path
//                         ]);
//                         \Log::info("✅ Updated existing record in slot {$slot}");
//                     } else {
//                         ItemImage::create([
//                             'Item_Code' => $item->Item_Code,
//                             'slot' => $slot,
//                             'Image_Name' => $userFileName,
//                             'path' => $path
//                         ]);
//                         \Log::info("✅ Created new record in slot {$slot}");
//                     }
//                 } else {
//                     \Log::warning("⚠️ State is 'new' but no valid file for slot {$slot}");
//                     if ($file) {
//                         \Log::warning("  File error: " . ($file->getErrorMessage() ?: 'Unknown error'));
//                     }
                    
//                     // If state is 'new' but no file, this is an error
//                     if ($request->ajax() || $request->wantsJson()) {
//                         return response()->json([
//                             'success' => false,
//                             'message' => "Image slot {$slot} marked as 'new' but no valid file provided"
//                         ], 400);
//                     }
//                 }
//             } 
//             elseif ($state === 'existing') {
//                 if ($existingImage) {
//                     \Log::info("  Keeping existing image: {$existingImage->path}");
//                     if ($imageName && $existingImage->Image_Name !== $imageName) {
//                         $existingImage->update(['Image_Name' => $imageName]);
//                         \Log::info("✅ Updated image name in slot {$slot}: {$imageName}");
//                     }
//                 }
//             }
//         }

//         // Log current images in database AFTER update
//         \Log::info('📊 Current images in database AFTER update:');
//         $updatedImages = ItemImage::where('Item_Code', $item->Item_Code)->get();
//         foreach ($updatedImages as $img) {
//             $exists = !empty($img->path) && \Storage::disk('public')->exists($img->path) ? '✅' : '❌';
//             \Log::info("  {$exists} Slot {$img->slot}: {$img->Image_Name} -> {$img->path}");
//         }

//         // Handle SKUs
//         $skus = json_decode($request->input('skus_json', '[]'), true);
//         if ($skus) {
//             $item->skus()->delete();
//             foreach ($skus as $sku) {
                
//                 $item->skus()->create([
//                     'Size_Name'  => $sku['sizeName'] ?? null,
//                     'Color_Name' => $sku['colorName'] ?? null,

//                      // 👇 PAD HERE
//     'Size_Code'  => isset($sku['sizeCode']) ? $this->pad4($sku['sizeCode']) : null,
//     'Color_Code' => isset($sku['colorCode']) ? $this->pad4($sku['colorCode']) : null,
//                     'JanCode'    => $sku['janCode'] ?? null,
//                     'Quantity'   => $sku['stockQuantity'] ?? 0
//                 ]);
//             }
//         }

//         // Return JSON response if it's an AJAX request
//         if ($request->ajax() || $request->wantsJson()) {
//             return response()->json([
//                 'success' => true,
//                 'message' => 'Item updated successfully',
//                 'redirect' => route('itemList')
//             ]);
//         }
// //update
//         return redirect()->route('itemList')->with('success', 'Item updated successfully');

//     } catch (\Exception $e) {
//         \Log::error('❌ Update error: ' . $e->getMessage());
//         \Log::error($e->getTraceAsString());
        
//         if ($request->ajax() || $request->wantsJson()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Server error: ' . $e->getMessage()
//             ], 500);
//         }
        
//         return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
//     }
// }
// private function pad4($value)
// {
//     return str_pad((string)$value, 4, '0', STR_PAD_LEFT);
// }




public function edit($id)
{
    $item = Item::with(['skus', 'images'])->findOrFail($id);
        return view('items.itemsEdit', compact('item'));

}

public function destroySku(Request $request)
{
    try {
        $itemsToDelete = $request->input('items'); // This is the array from JS

        // Validation
        if (empty($itemsToDelete) || !is_array($itemsToDelete)) {
            return response()->json(['success' => false, 'message' => 'No items provided'], 400);
        }

        $totalDeleted = 0;

        foreach ($itemsToDelete as $item) {
            // Use the keys matching your JavaScript object: item_code, size_code, color_code
            $deleted = \DB::table('M_SKU')
                ->where('Item_Code', $item['item_code'])
                ->where('Size_Code', $item['size_code'])
                ->where('Color_Code', $item['color_code'])
                ->delete();
            
            $totalDeleted += $deleted;
        }

        return response()->json([
            'success' => true, 
            'message' => 'SKUs processed successfully',
            'deleted_count' => $totalDeleted
        ]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function destroyByItemAdminCode(Request $request)
{
    try {
        // Expecting an array of strings: ['ITEM001', 'ITEM002']
        $itemCodesToDelete = $request->input('itemAdmin-codes');

        if (empty($itemCodesToDelete) || !is_array($itemCodesToDelete)) {
            return response()->json(['success' => false, 'message' => 'No item codes provided'], 400);
        }

        // Single Query: Deletes all SKUs where Item_Code is in the list
        $deletedCount = \DB::table('M_SKU')
            ->whereIn('Item_AdminCode', $itemCodesToDelete)
            ->delete();

            
        return response()->json([
            'success' => true, 
            'message' => 'Items and their associated SKUs deleted',
            'deleted_rows' => $deletedCount
        ]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function itemSelectDelete(Request $request)
    {
        $ids = $request->ids; 

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No items selected'], 400);
        }

        try {
            DB::beginTransaction();

            // Log the start of the process
            Log::info('Bulk delete initiated', [
                'user_id' => Auth::id() ?? 'System',
                'item_codes' => $ids
            ]);

            // 1. Delete related SKUs
            $skuDeleted = DB::table('M_SKU')->whereIn('Item_Code', $ids)->delete();

            // 2. Delete related Images
            $imagesDeleted = DB::table('M_ItemImage')->whereIn('Item_Code', $ids)->delete();

            // 3. Delete the Items
            $itemsDeleted = Item::whereIn('Item_Code', $ids)->delete();

            DB::commit();

            // Log successful completion
            Log::info('Bulk delete successful', [
                'items_count' => $itemsDeleted,
                'skus_deleted' => $skuDeleted,
                'images_deleted' => $imagesDeleted
            ]);

            return response()->json(['success' => true, 'message' => 'Items deleted successfully']);
            
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error with full stack trace for debugging
            Log::error('Bulk delete failed', [
                'error' => $e->getMessage(),
                'ids' => $ids,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['success' => false, 'message' => 'Delete failed. Check logs for details.'], 500);
        }
    }



    
    public function export(Request $request)
{
    $itemCode = trim($request->query('item_code'));
    $itemName = trim($request->query('item_name'));
    $viewType = $request->query('view_type', 'item');
    $format   = $request->query('format', 'excel');

    // SKU logic
    if ($viewType === 'sku') {
        if (!$itemCode && $itemName) {
            $itemCodes = \App\Models\Item::where('Item_Name', 'like', '%' . $itemName . '%')
                ->pluck('Item_Code')
                ->toArray();

            if (empty($itemCodes)) {
                return back()->with('error', 'No items found for this Item Name.');
            }

            $itemCode = implode(',', $itemCodes);
        }

        // if (!$itemCode) {
        //     return back()->with('error', 'Item Code is required to export SKU.');
        //     \Log('item code is required to export sku');
        // }
    }

    // Validation query
    $query = $viewType === 'sku'
        ? \App\Models\Sku::query()
        : \App\Models\Item::query();

    if ($itemCode) {
        $query->where(function ($q) use ($itemCode) {
            foreach (explode(',', $itemCode) as $code) {
                $q->orWhere('Item_Code', 'like', '%' . trim($code) . '%');
            }
        });
    }

    if ($itemName && $viewType === 'item') {
        $query->where('Item_Name', 'like', '%' . $itemName . '%');
    }

    if ($query->count() === 0) {
        return back()->with('error', 'No data found to export.');
    }

    $fileName = ($viewType === 'sku' ? 'sku_' : 'item_') . 'export';

    // CSV
    if ($format === 'csv') {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ItemExport($itemCode, $itemName, $viewType),
            $fileName . '.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    // Excel
    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\ItemExport($itemCode, $itemName, $viewType),
        $fileName . '.xlsx'
    );
}






            public function checkCode(Request $request)
        {
            $exists = Item::where('Item_Code', $request->Item_Code)->exists();
            return response()->json(['exists' => $exists]);
        }


// public function search(Request $request)
// {
//     $q = trim($request->query('q'));

//     if ($q === '') {
//         return response()->json([]);
//     }

//     $keywords = array_filter(array_map('trim', explode(',', $q)));

//     $items = Item::query()
//         ->where(function ($query) use ($keywords) {
//             foreach ($keywords as $word) {
//                 $query->orWhere('Item_Code', 'like', "%{$word}%")
//                       ->orWhere('Item_Name', 'like', "%{$word}%")
//                       ->orWhere('JanCD', 'like', "%{$word}%")
//                       ->orWhere('MakerName', 'like', "%{$word}%")
//                       ->orWhere('Memo', 'like', "%{$word}%");
//             }
//         })
//         ->limit(50)
//         ->get();

//     return response()->json($items);
// }



public function search(Request $request)
{
    $itemCode = trim($request->query('item_code'));
    $itemName = trim($request->query('item_name'));
    $isLive   = (int) $request->query('live', 1); // 1 = live, 0 = exact

    // If no search input, return empty
    if ($itemCode === '' && $itemName === '') {
        return response()->json([]);
    }

    $items = DB::select(
        'EXEC M_ItemSearching @Item_Code = ?, @Item_Name = ?, @IsLiveSearch = ?',
        [
            $itemCode ?: null,
            $itemName ?: null,
            $isLive
        ]
    );

    return response()->json($items);
}

public function searchSku(Request $request)
{
    $itemCode   = trim($request->query('item_code', ''));
    $itemName   = trim($request->query('item_name', ''));
    $janCode    = trim($request->query('jan_code', ''));
    $adminCode  = trim($request->query('item_admin_code', ''));
    $isLive     = (int) $request->query('live', 1); // 1 = LIKE, 0 = EXACT

    // If all inputs empty → return empty
    if ($itemCode === '' && $itemName === '' && $janCode === '' && $adminCode === '') {
        return response()->json([]);
    }
    \Log::info('SKU SEARCH - Raw Request', [
    'item_code'       => $request->query('item_code'),
    'item_name'       => $request->query('item_name'),
    'jan_code'        => $request->query('jan_code'),
    'item_admin_code' => $request->query('item_admin_code'),
    'live'            => $request->query('live'),
]);


    $items = DB::select(
        'EXEC M_ItemSearching_SKU
            @Item_Code      = ?,
            @Item_Name      = ?,
            @JanCode        = ?,
            @Item_AdminCode = ?,
            @IsLiveSearch   = ?',
        [
            $itemCode   ?: null,
            $itemName   ?: null,
            $janCode    ?: null,
            $adminCode  ?: null,
            $isLive
        ]
    );

    return response()->json($items);
}




}
//04-feb-2026 Fixed Update



