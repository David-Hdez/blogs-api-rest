<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use App\Helpers\JWTAuth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {     
        $this->middleware('jwt')->except(
            'index', 
            'show',
            'showAvatar',
            'showByCategory',
            'showByUser');
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
            $user_decoded=$this->identifying($request);

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
     * Store a image from post.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeImage(Request $request)
    {
        //
        $file = $request->file('file0');

        $validator = \Validator::make($request->all(), [
            'file0' => 'required|image',    
            'file0' => 'mimes:jpeg,jpg,png,gif'                     
        ]);

        if (!$file || $validator->fails()) {
            $resp=array(
                'status'=>'error',
                'code'=>400,
                'message'=>'In file image post'
            ); 
        }else{
            $image_name=time().$file->getClientOriginalName();

            Storage::disk('images')->put($image_name, \File::get($file));

            $resp=array(
                'status'=>'success',
                'code'=>200,
                'avatar'=>$image_name
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
     * Display the image in post.
     *
     * @param  string  $name     
     * @return \Illuminate\Http\Response
     */
    public function showAvatar($image)
    {      
        $exists = Storage::disk('images')->exists($image);

        if ($exists) {
            $image=Storage::disk('images')->get($image);
          
            return response($image, 200);
        } else {          
            return response()->json([
                'message'=>'Image not exists'
            ], 404);
        }                
    }

    /**
     * Display the posts by specific category.
     *
     * @param  int  $category     
     * @return \Illuminate\Http\Response
     */
    public function showByCategory($category)
    {      
        $posts_by_category = Post::where('category_id', $category)->get();

        return response()->json(
            $posts_by_category, 
            200);              
    }
    
    /**
     * Display the posts by specific category.
     *
     * @param  int  $user     
     * @return \Illuminate\Http\Response
     */
    public function showByUser($user)
    {      
        $posts_by_user = Post::where('user_id', $user)->get();

        return response()->json(
            $posts_by_user, 
            200);              
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

            $user_identified=$this->identifying($request);   

            $post=Post::where('id',$id)
                ->where('user_id',$user_identified->sub)
                ->first();

            if (!empty($post) && is_object($post)) {
                $post->update($post_array);

                $resp=array(
                    'status'=>'updated',
                    'code'=>200,     
                    'post'=>$post,           
                    'updates'=>$post_array
                );
            }                       
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
        // Solo usuario dueño del post puede eliminar 
        $user_identified=$this->identifying($request);       

        $post = Post::where('id',$id)
            ->where('user_id',$user_identified->sub)
            ->first();

        if (!empty($post)) {
            $post->delete();

            $resp=array(
                'status'=>'success',                
                'post'=>$post,
                'code'=>200     
            );
        } else {
            $resp=array(
                'status'=>'error',                
                'message'=>'Post not exists or user is not the owner',
                'code'=>404     
            );
        }                

        return response()->json($resp, $resp['code']);
    }

    /**
     * De token tomando usuario
     * 
     * Poder identificar y verificar que pueda actualizar y borrar post que le pertenezcan                   
     * @param  \Illuminate\Http\Request  $request
     * @return $user_identified
     */
    private function identifying($request){
        $auth=new JWTAuth();

        $token_req=$request->header('Authorization', null);

        $user_identified=$auth->checkToken($token_req,true);

        return $user_identified;
    }
}
