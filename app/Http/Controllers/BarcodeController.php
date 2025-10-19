<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Response;
use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodeController
{
    public function show(InventoryItem $item): Response
    {
        $generator = new BarcodeGeneratorPNG();
        $code = $item->barcode ?: $item->sku;
        $png = $generator->getBarcode($code, $generator::TYPE_CODE_128, 2, 60);
        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'max-age=86400',
        ]);
    }
}
