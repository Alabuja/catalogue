<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\User;
use App\Category;
use App\Product;
use App\ProductImage;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function products()
    {
        $products = Product::with('category')->get()->toArray();

        return response()->json([
            $products
        ],200);
    }

    public function product($id)
    {
        $product = Product::where('id', $id)->first();

        if(!empty($product))
        {
            $response = Product::where('id', $id)->with('category')->first();

            return response()->json([
                $response
            ], 200);
        }
        else
        {
            return response()->json([
                'Not Found'
            ], 404);
        }
    }

    public function destroy($id)
    {
        $product = Product::where('id', $id)->first();

        if(Auth::check())
        {
            if(!empty($product))
            {
                ProductImage::where('product_id', $id)->delete();

                Product::where('id', $id)->delete();

                return response()->json([
                    null
                ], 204);
            }
            else
            {
                return response()->json([
                    'Not Found'
                ], 404);
            }
        }
        else
        {
            return response()->json([
                'Not Authorized'
            ], 401);
        }
    }

    public function store(ProductRequest $request)
    {
        try {
            if(Auth::check())
            {
                DB::beginTransaction();
                $response  =  Product::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    "category_id" => $request->category_id
                ]);

                if (!empty($request->input('image'))) {
                    try {
                        foreach ($request->input('image') as $key => $val) {

                            // $product_image   =   $val->getRealPath();

                            // Cloudder::upload($product_image, null);
                            // list($width, $height) = getimagesize($product_image);

                            // $imageUrl = Cloudder::secureShow(Cloudder::getPublicId(), [
                            //     "crop" => "fit", "width" => 200, "height" => 202
                            // ]);

                            ProductImage::Create([
                                "product_id" => $response->id,
                                "image_url" => $val,
                            ]);
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json(['message' => $e->getMessage()], 500);
                    }
                }

                DB::commit();
                return response()->json([
                    $response
                ], 201);
            }
            else
            {
                return response()->json([
                    'Not Authorized'
                ], 401);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            $res['message'] = $ex->getMessage();
            return response($res, 500);
        }
    }

    public function edit(ProductRequest $request, $id)
    {
        try {
            if(Auth::check())
            {
                DB::beginTransaction();
                $product = Product::where('id', $id)->first();

                if(!empty($product))
                {
                    $updateProduct = Product::where('id', $id)->update([
                        'name' => $request->name,
                        'description' => $request->description,
                        'category_id'   =>  $request->category_id
                    ]);

                        if (!empty($request->input('image'))) 
                        {
                            try {
                                ProductImage::where('product_id', $id)->delete();

                                foreach ($request->input('image') as $key => $val) 
                                {
                                    // $product_image   =   $val->getRealPath();

                                    // Cloudder::upload($product_image, null);
                                    // list($width, $height) = getimagesize($product_image);

                                    // $imageUrl = Cloudder::secureShow(Cloudder::getPublicId(), [
                                    //     "crop" => "fit", "width" => 200, "height" => 202
                                    // ]);

                                    ProductImage::Create([
                                        "product_id" => $id,
                                        "image_url" => $val,
                                    ]);
            
                                }
                            } catch (\Exception $e) {
                                DB::rollBack();
                                return response()->json(['message' => $e->getMessage()], 500);
                            }
                        }
                        DB::commit();
                    return response()->json([
                        $updateProduct
                    ], 200);
                    
                }
                else
                {
                    return response()->json([
                        'Not Found'
                    ], 404);
                }

            }
            else
            {
                return response()->json([
                    'Not Authorized'
                ], 401);
            }
            
        } catch (\Illuminate\Database\QueryException $ex) {
            $res['message'] = $ex->getMessage();
            return response($res, 500);
        }
    }
}
