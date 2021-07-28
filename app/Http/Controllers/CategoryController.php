<?php

namespace App\Http\Controllers;

use Validator;
use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {     
        $this->middleware('jwt')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $categories = Category::all();

        return response()->json([
            'categories'=>$categories
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $category_req=$request->input('category',null);
        
        $category_array=json_decode($category_req,true); 

        if (!empty($category_array)) {
            $validator = Validator::make($category_array, [
                'name' => 'required',                                    
            ]);
    
            if ($validator->fails()) {
                $resp=array(
                    'code'=>400,
                    'message'=>'Category validation incorrect'
                );
            } else {
                $category = new Category(); 
                
                $category->name=$category_array['name'];
                
                $category->save();
    
                $resp=array(
                    'code'=>201,
                    'message'=>'Category created',  
                    'category'=>$category,
                );
            }
        } else {
            $resp=array(
                'code'=>400,
                'message'=>'Category not received'
            );
        }                        

        return response()->json($resp, $resp['code']);        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $category = Category::find($id);

        if (is_object($category)) {
            $resp=array(
                'code'=>200,
                'category'=>$category
            );
        } else {
            $resp=array(
                'code'=>404,
                'message'=>'Category does not exists'
            );
        }
        

        return response()->json($resp, $resp['code']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
    }
}
