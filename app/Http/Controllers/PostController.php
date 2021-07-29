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
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request    
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        //
        $post_req=$request->input('post',null);
            
        $post_array=json_decode($post_req,true); 

        $resp=array(
            'status'=>'error',
            'code'=>400,  
            'message'=>'Post received bad'
        );

        if (!empty($post_array)) {                        
            $validator = \Validator::make($post_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',                                    
            ]);

            if ($validator->fails()) {
                $resp['validator']=$validator->errors();

                return response()->json($resp, 400);
            }

            unset($post_array['id']);
            unset($post_array['user_id']);
            unset($post_array['created_at']);
            unset($post_array['user']);

            $post_updated=Post::where('id',$id)
                ->updateOrCreate($post_array);

            $resp=array(
                'status'=>'updated',
                'code'=>200,
                'post'=>$post_updated,
                'updates'=>$post_array
            );
        }      

        return response()->json($resp, $resp['code']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        //        
        $post = Post::find($id);

        if (!empty($post)) {
            $post->delete();

            $resp=array(
                'status'=>'success',                
                'post'=>$post,
                'code'=>200     
            );
        } else {
            $resp=array(
                'status'=>'success',                
                'message'=>'Post not exists',
                'code'=>404     
            );
        }                

        return response()->json($resp, $resp['code']);
    }
}
