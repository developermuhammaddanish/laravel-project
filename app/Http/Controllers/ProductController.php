<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ProductController extends Controller
{
     //show product
     public function ShowProduct(){
        $products = Product::with('category')->get();
        
        return response()->json([
            'message' => 'Product Successfully Loaded',
            'data' => $products
        ],201);
    }

    //fatch product
    public function FatchProduct($id){
        $product = Product::find($id);
        return response()->json([
            'message'=> 'Product Fatch Successfully',
            'data'=> $product
        ],201);
    }

    //add products
    public function AddProduct(Request $req){
    $rule = [
        "product_name"=> "required",
        "product_price"=> "required",
        "category_id"=> "required",
    ];

    $validator = Validator::make($req->all(), $rule);

    if ($validator->fails()) {
        return $validator->errors();
    }
    else{
        $data = new Product;
        $data->product_name = $req->product_name;
        $data->product_price = $req->product_price;
        $data->category_id = $req->category_id;
        $result = $data->save();
        if($result){
            return response()->json(['result'=>'Data Successfully Added!']);
        }
        else{
            return response()->json(['result'=> 'Data Not Successfully Added']);
        }
    }
}

    //update product
    public function UpdateProduct(Request $req,$id){

        $data = Product::find($id);
        if(isset($data)){
            $rule = [
                "product_name"=> "required",
                "product_price"=> "required",
                "category_id"=> "required",
            ];
    
            $validator = Validator::make($req->all(), $rule);
    
            if ($validator->fails()) {
                return $validator->errors();
            }
            else{
                $data = Product::find($id);
                $data->product_name = $req->product_name;
                $data->product_price = $req->product_price;
                $data->category_id = $req->category_id;
                $result = $data->save();
                if($result){
                    return response()->json(['result'=>'Data Successfully Updated!']);
                }
                else{
                    return response()->json(['result'=> 'Data Not Successfully Updated']);
                }
            }
        }
        else{
            return response()->json(['result'=> 'ID not found']);
        }
    }

    // delete product
    public function DeleteProduct($id){
        $data = Product::find($id);
            if(isset($data)){
                $result = Product::destroy($id);
            if($result){
                return response()->json(['result'=>'Data Successfully Deleted']);
            }
            else{
                return response()->json(['result'=> 'Data Not Successfully Deleted']);
            }
        }
        else{
            return response()->json(['result'=> 'ID not found']);
        }
    }

    // search product
    public function searchProduct(Request $request)
    {
        $search = $request->search;
        $products = Product::where('product_name', 'like', '%' . $search . '%')->with('category')
            ->orWhere('product_price', $search)
            ->orWhereHas('category', function ($query) use ($search) {
                $query->where('product_category', 'like', '%' . $search . '%');
            })
            ->get();
    
        if ($products->isEmpty()) {
            return response()->json([
                'message' => 'Search Not Found',
            ], 404);
        } else {
            return response()->json([
                'message' => 'Data Found Successfully',
                'data' => $products
            ], 200);
        }
    }
    
}
