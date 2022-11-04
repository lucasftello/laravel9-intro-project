<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        return view('admin.products', [
            'products' => $products
        ]);
    }

    public function edit(Product $product)
    {
        return view('admin.product-edit', [
            'product' => $product
        ]);
    }

    public function update(Product $product, ProductRequest $request)
    {
        $input = $request->validated();

        if (!empty($input['cover']) && $input['cover']->isValid()) {
            Storage::delete($product->cover ?? '');

            $file = $input['cover'];
            $path = $file->store('products');
            $input['cover'] = $path;
        }

        $product->fill($input);
        $product->save();

        return Redirect::route('admin.products.index');
    }

    public function create()
    {
        return view('admin.product-create');
    }

    public function store(ProductRequest $request)
    {
        $input = $request->validated();
        $input['slug'] = Str::slug($input['name']);

        if (!empty($input['cover']) && $input['cover']->isValid()) {
            $file = $input['cover'];
            $path = $file->store('products');
            $input['cover'] = $path;
        }

        Product::create($input);

        return Redirect::route('admin.products.index');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        Storage::delete($product->cover ?? '');

        return Redirect::route('admin.products.index');
    }

    public function destroyImage(Product $product)
    {
        Storage::delete($product->cover ?? '');

        $product->cover = null;
        $product->save();

        return Redirect::back();
    }
}
