<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\PostingProductJob;
use App\Models\Device;
use App\Models\Product;

class SafAutomation
{
    public static function make()
    {
        return new static();
    }

    public function postProduct(Product $product, Device $device)
    {
        dispatch(new PostingProductJob($product, $device));
    }
}
