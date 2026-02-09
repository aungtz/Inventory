<?php

namespace App\Http\Controllers;
use \App\Models\ItemImportErrorLog;
use \App\Models\ItemImportLog;
use \App\Models\ItemImportDataLog;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class ImportLogController extends Controller
{
   public function importLog(){
    return view('import.importLog');
   }
    public function index()
    {
    $logs = ItemImportLog::orderBy('ImportLog_ID', 'DESC')->paginate(10);
    return view('import.importLog', compact('logs'));
    }
   public function importItemPage(){
      return view('import.itemMasterImport');
   }
   public function importSkuPage(){
      return view('import.skuMasterImport');
   }
   public function itemPreview(){
      return view('import.ItemPreview');
   }
   public function skuPreview(){
      return view('import.skuPreview');
   }


        

     public function itemDetails($id)
        {
            $log = ItemImportLog::findOrFail($id);

            $items = ItemImportDataLog::where('ImportLog_ID', $id)->paginate(10);
            
            foreach ($items as $row) {
                $row->Status = 'Valid';
                $row->Error_Msg = null;

                $row->ListPrice = $row->ListPrice ?? null;
                $row->JanCD     = $row->JanCD ?? $row->JanCode ?? null;
            }
            \Log::info($items);
            // dd($items);
            return view('import.itemDetails', compact('log', 'items'));
        }                       

            public function errorDetails($id)
        {
            $log = ItemImportLog::findOrFail($id);

            $items = ItemImportErrorLog::where('ImportLog_ID', $id)->paginate(10);

            foreach ($items as $row) {
                $row->Status = 'Error';

                $row->ListPrice = $row->ListPrice ?? null;
                $row->JanCD     = $row->JanCD ?? $row->JanCode ?? null;
            }

            return view('import.itemErrorDetails', compact('log', 'items'));
        }




  // Display SKU valid rows
public function skuDetails($id)
{
    $log = ItemImportLog::findOrFail($id);

\Log::info('Requested ImportLog_ID', ['id' => $id]);

$items = ItemImportDataLog::where('ImportLog_ID', $id)->paginate(10);

\Log::info('Found items count', ['count' => $items->count()]);

    foreach ($items as $row) {
        $row->Status = 'Valid';

        // Normalize fields
        $row->SizeName   = $row->Size_Name ?? null;
        $row->ColorName  = $row->Color_Name ?? null;
        $row->SizeCode   = $row->Size_Code ?? null;
        $row->ColorCode  = $row->Color_Code ?? null;
        $row->JanCD      = $row->JanCD ?? $row->JanCode ?? null;
        $row->Quantity   = $row->Quantity ?? 0;
    }

    return view('import.skuDetails', compact('log', 'items'));
}

// Display SKU error rows
public function skuErrorDetails($id)
{
    $log = ItemImportLog::findOrFail($id);

    $items = ItemImportErrorLog::where('ImportLog_ID', $id)->paginate(10);

    foreach ($items as $row) {
        $row->Status = 'Error';

        // Normalize fields
        $row->SizeName   = $row->Size_Name ?? null;
        $row->ColorName  = $row->Color_Name ?? null;
        $row->SizeCode   = $row->Size_Code ?? null;
        $row->ColorCode  = $row->Color_Code ?? null;
        $row->JanCD      = $row->JanCD ?? $row->JanCode ?? null;
        $row->Quantity   = $row->Quantity ?? 0;
    }

    return view('import.skuErrorDetails', compact('log', 'items'));
}


public function processImport(Request $request)
{
    \Log::info("PROCESS IMPORT START");

    try {
        $valid  = $request->input('valid', []);
        $errors = $request->input('errors', []);
        $type   = (int)$request->input('import_type');
        $user   = auth()->user()->name ?? 'SYSTEM';

        // 1️⃣ Create import log
        $importLog = ItemImportLog::create([
            'Import_Type'   => $type,
            'Record_Count'  => count($valid),
            'Error_Count'   => count($errors),
            'Imported_By'   => $user,
            'Imported_Date' => now(),
        ]);

        $importLogId = $importLog->ImportLog_ID;
        
        // 2️⃣ Insert VALID rows
        foreach ($valid as $row) {
           

            $rawItemCode = $row['Item_Code'] ?? '';
    $itemCode = trim((string)$rawItemCode);

    // If the entire row is empty or Item_Code is missing, skip it.
    // This prevents the "Cannot insert NULL" error.
    if ($itemCode === '') {
        \Log::info("Skipping an empty Item_Code row to prevent crash.");
        continue; 
    }

            $ListPrice = str_replace(',','', $row['ListPrice']??'');
            $SalePrice = str_replace(',','', $row['SalePrice']??'');

            $cleanListPrice = ($ListPrice !== '') ? (float)$ListPrice : null;
            $cleanSalePrice = ($SalePrice !== '') ? (float)$SalePrice : null;

            ItemImportDataLog::create([
                'ImportLog_ID' => $importLogId,
                'Item_AdminCode'=>$row['ItemAdminCode'] ?? $row['Item_AdminCode'] ?? null,
                'Item_Code'  => $row['Item_Code'] ?? null,
                'Item_Name'  => $row['Item_Name'] ?? null,
                'JanCD'      => $row['JanCD'] ?? null,
                'MakerName'  => $row['MakerName'] ?? null,
                'Memo'       => $row['Memo'] ?? null,
                'ListPrice'    => $cleanListPrice,
                'SalePrice'    => $cleanSalePrice,
                'Size_Name'  => $row['SizeName'] ?? null,
                'Color_Name' => $row['ColorName'] ?? null,
                'Size_Code'  => $row['SizeCode'] ?? null,
                'Color_Code' => $row['ColorCode'] ?? null,
                'JanCode'    => $row['JanCD'] ?? null,
                'Quantity'   => (int)($row['Quantity'] ?? 0),
            ]);
        }
        \Log::info($valid); //<== list price is carry this part .
        // 2.1️⃣ Commit valid rows into M_Item / M_Sku
        // 2.1️⃣ Commit valid rows into M_Item / M_Sku
        if (count($valid) > 0) {
            // You MUST include @ImportType = ? in the string
            DB::statement('EXEC SP_CommitImport @ImportLog_ID = ?, @UserName = ?, @ImportType = ?', [
                $importLogId,
                $user,
                $type // This maps to @ImportType
            ]);
        }

        // 3️⃣ Insert ERROR rows
        foreach ($errors as $row) {
            $errorMsg = is_array($row['errors'])
                ? implode('; ', $row['errors'])
                : ($row['errors'] ?? 'Unknown error');

            ItemImportErrorLog::create([
                'ImportLog_ID' => $importLogId,
                
                'Item_Code'  => $row['Item_Code'] ?? null,
                'Item_Name'  => $row['Item_Name'] ?? null,
                'JanCD'      => $row['JanCD'] ?? null,
                'MakerName'  => $row['MakerName'] ?? null,
                'Memo'       => $row['Memo'] ?? null,
                'ListPrice'  => $row['ListPrice'] ?? null,
                'SalePrice'  => $row['SalePrice'] ?? null,
                'Size_Name'  => $row['SizeName'] ?? null,
                'Color_Name' => $row['ColorName'] ?? null,
                'Size_Code'  => $row['SizeCode'] ?? null,
                'Color_Code' => $row['ColorCode'] ?? null,
                'JanCode'    => $row['JanCD'] ?? null,
                // 'Quantity'   => (int)($row['Quantity'] ?? 0),
                'Quantity' => $row['Quantity'] ?? null,

                'Error_Msg'  => $errorMsg,
            ]);
        }

        return response()->json([
            'success' => true,
            'ImportLog_ID' => $importLogId
        ]);

    } catch (\Throwable $e) {
        \Log::error("PROCESS IMPORT FAILED", [
            'message' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Import failed'
        ], 500);
    }
}

}
//06-Feb-2026