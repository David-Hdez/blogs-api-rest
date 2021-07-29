<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use App\Helpers\JWTAuth;

class PostController extends Controller
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
        $posts = Post::all()->load('category');

        return response()->json([
            'posts'=>$posts
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
        $post_req=$request->input('post',null);
        
        $post_object=json_decode($post_req); 
        $post_array=json_decode($post_req,true); 

        if (!empty($post_array)) {
            $auth=new JWTAuth();
            $token_req=$request->header('Authorization', null);
            $user_decoded=$auth->checkToken($token_req,true);

            $validator = \Validator::make($post_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',                
                'image' => 'required',    
            ]);

            if ($validator->fails()) {
                $resp=array(
                    'code'=>400,
                    'message'=>'Post validation',
                    'status'=>'error'
                );
            } else {
                $post = new Post();       

                $post->user_id=$user_decoded->sub;
                $post->category_id=$post_object->category_id;
                $post->title=$post_object->title;
                $post->content=$post_object->content;
                $post->image=$post_object->image;
                
                $post->save();

                $resp=array(
                    'status'=>'success',
                    'code'=>201,
                    'message'=>'Post registrado',
                    'created'=>$post
                );
            }
            
        } else {
            $resp=array(
                'code'=>400,
                'message'=>'Post not received'
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
        $post = Post::find($id)->load('category');

        if (is_object($post)) {
            $resp=array(
                'code'=>200,
                'post'=>$post
            );
        } else {
            $resp=array(
                'code'=>404,
                'message'=>'Post does not exists'
            );
        }
        

        return response()->json($resp, $resp['code']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }
}
