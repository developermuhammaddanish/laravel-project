<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CategoryController extends Controller
{
      public function ShowCategory(){
        return Category::all();
    }


    //fatch product
    public function FatchCategory($id){
        $category = Category::find($id);
        return response()->json([
            'message'=> 'Category Fatch Successfully',
            'data'=> $category
        ],201);
    }

    public function AddCategory(Request $req){
    $rule = [
        "product_category"=> "required",
    ];

    $validator = Validator::make($req->all(), $rule);

    if ($validator->fails()) {
        return $validator->errors();
    }
    else{
        $data = new Category;
        $data->product_category = $req->product_category;
        $result = $data->save();
        if($result){
            return response()->json(['result'=>'Data Successfully Added!']);
        }
        else{
            return response()->json(['result'=> 'Data Not Successfully Added']);
        }
    }
}

    public function UpdateCategory(Request $req,$id){

        $data = Category::find($id);
        if(isset($data)){
            $rule = [
                "product_category"=> "required",
            ];
    
            $validator = Validator::make($req->all(), $rule);
    
            if ($validator->fails()) {
                return $validator->errors();
            }
            else{
                $data = Category::find($id);
                $data->product_category = $req->product_category;
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

    public function DeleteCategory($id){
        $data = Category::find($id);
            if(isset($data)){
                $result = Category::destroy($id);
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

    public function SearchCategory($product_category){
        return Category::where('product_category','like','%'.$product_category.'%')->get();          
    }
}
