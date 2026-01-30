<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ImportLogController;
use App\Http\Controllers\ImportValidation;



Route::get('/', [ItemController::class, 'itemList'])->name('itemList');
Route::get('/items-create', [ItemController::class, 'create'])->name('items.create');
Route::get('/import-log',[ImportLogController::class,'index'])->name('import.log');
Route::get('/itemList',[ItemController::class,'itemList'])->name('itemList');
Route::get('/skuList',[ItemController::class,'skuList'])->name('skuList');

Route::post('/items', [ItemController::class, 'store'])->name('items.store');

Route::get('/sku-master/import', [ImportLogController::class, 'importSkuPage'])->name('sku-master.import');
Route::get('/item-master/import',[ImportLogController::class,'importItemPage'])->name('item-master.import');

Route::get('/itemPreview',[ImportLogController::class,'itemPreview'])->name('item-master.preview');
Route::get('/skuPreview',[ImportLogController::class,'skuPreview'])->name('sku-master.preview');


Route::get('/check-item-code', function (Request $request) {
    $exists = \App\Models\Item::where('Item_Code', $request->code)->exists();
    return response()->json(['exists' => $exists]);
});
Route::get('/check-itemcode', function (\Illuminate\Http\Request $request) {
    $itemCode = $request->query('item_code');
    $id = $request->query('id'); // current item ID when editing

    $query = \App\Models\Item::where('Item_Code', $itemCode);

    if ($id) {
        // exclude current item during edit
        $query->where('id', '!=', $id);
    }

    return ['exists' => $query->exists()];
});

Route::get('/items/{id}/sku-matrix', [ItemController::class, 'skuMatrix']);
Route::get('/items/{id}/edit', [ItemController::class, 'edit'])->name('items.edit');


Route::put('/items/{id}', [ItemController::class, 'update'])->name('items.update');
Route::delete('/items/sku/delete', [ItemController::class, 'destroySku'])->name('items.sku.destroy');

Route::delete('/items/select-delete', [ItemController::class, 'itemSelectDelete'])->name('items.selectDelete');
Route::get('/items/export', [ItemController::class, 'export'])->name('items.export');
Route::post('/check-item-code', [ItemController::class, 'checkCode'])->name('check.item.code');
Route::post('/get-sku-matrix', [ItemController::class, 'getSkuMatrix'])->name('get.sku.matrix');



Route::get('/items/search', [ItemController::class, 'search'])->name('items.search');

Route::post('/sku-import/validate', [ImportValidation::class, 'validateSkuImport'])
    ->name('sku-import.validate');

    Route::post('/import/process', [ImportLogController::class, 'processImport'])
    ->name('import.process');


    Route::get('/import-log/{id}/item-details', [ImportLogController::class, 'itemDetails'])->name('item-details');
Route::get('/import-log/{id}/sku-details', [ImportLogController::class, 'skuDetails'])->name('sku-details');
Route::get('/import-log/{id}/item-errors', [ImportLogController::class, 'errorDetails'])->name('item-errors');
Route::get('/import-log/{id}/sku-errors', [ImportLogController::class, 'skuErrorDetails'])->name('sku-errors');

Route::post('/sku-update', [ItemController::class, 'updateStock'])->name('sku.updateStock');


Route::get('/test-error', function() {
    throw new \Exception("My First SQL Error Log!");
});