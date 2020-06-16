<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Category;
use App\Product;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    public function __construct()
    {
        
    }

    public function categories()
    {
        $categories = Category::get()->toArray();

        return response()->json([
            $categories
        ],200);
    }

    public function category($id)
    {
        $category = Category::where('id', $id)->first();

        if(!empty($category))
        {
            $response = Category::where('id', $id)->first();

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
        $category = Category::where('id', $id)->first();

        if(Auth::check())
        {
            if(!empty($category))
            {
                Product::where('category_id', $id)->delete();

                Category::where('id', $id)->delete();

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
        }else{
            return response()->json([
                'Not Authorized'
            ], 401);
        }
    }

    public function store(CategoryRequest $request)
    {
        try {

            if(Auth::check())
            {
                $response = Category::create([
                    'name' => $request->name
                ]);

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

    public function edit(CategoryRequest $request, $id)
    {
        try {
            $category = Category::where('id', $id)->first();

            if(Auth::check())
            {
                if(!empty($category))
                {
                    if(strtolower($category->name) == strtolower($request->name))
                    {
                        return response()->json([
                            'Category Name already exist'
                        ], 422);
                    }else
                    {   
                        $updateCategory = Category::where('id', $id)->update([
                            'name' => $request->name
                        ]);

                        return response()->json([
                            $updateCategory
                        ], 200);
                    }
                    
                }
            else
            {
                return response()->json([
                    'Not Found'
                ], 404);
            }
            }else{
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
