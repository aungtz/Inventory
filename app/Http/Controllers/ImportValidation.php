<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ImportValidation extends Controller
{
  public function validateSkuImport(Request $request)
{
    $request->validate([
        'rows' => 'required|array',
    ]);

    $rows = $request->input('rows');
    \Log::info('XML Validation is Working!!');
    // Build XML
    $xml = new \SimpleXMLElement('<Rows/>');

    foreach ($rows as $row) {
        $item = $xml->addChild('Row');
        $item->addChild('LineNo', $row['lineNo'] ?? '');
        $item->addChild('Item_AdminCode', $row['Item_AdminCode'] ?? '');
        $item->addChild('Item_Code', htmlspecialchars($row['Item_Code'] ?? ''));
        $item->addChild('Size_Name', htmlspecialchars($row['SizeName'] ?? ''));
        $item->addChild('Color_Name', htmlspecialchars($row['ColorName'] ?? ''));
        $item->addChild('Size_Code', $row['SizeCode'] ?? '');
        $item->addChild('Color_Code', $row['ColorCode'] ?? '');
        $item->addChild('JanCode', $row['JanCD'] ?? '');
        $item->addChild('Quantity', $row['Quantity'] ?? 0);
    }

    $xmlString = $xml->asXML();
    
    // Call SP with XML
    $errors = DB::select(
        'EXEC sp_Validate_SKU_Import_XML ?',
        [$xmlString]
    );
    \Log::info('XML String:', [$xmlString]);
    \Log::info($errors);

    return response()->json($errors);
}


}
