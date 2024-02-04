<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'video' => 'required|array',
            'raw_html' => 'required'
        ]);

        Product::query()->updateOrCreate(
            ['product_identity' => $request->product_identity],
            [
                'title' => $request->title,
                'description' => $request->description,
                'video_link' => $request->video['0'],
                'shopee_link' => $request->shopee_link,
                'description_ai' => $request->description_ai,
                'tags' => $request->tags,
                'raw_html' => $request->raw_html
            ]
        );

        return response()->json(['message' => 'Product has been posted successfully']);
    }
}
