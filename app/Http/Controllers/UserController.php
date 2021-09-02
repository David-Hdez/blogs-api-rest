<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $user_req=$request->input('user',null);
        
        $user_object=json_decode($user_req);
        $user_array=json_decode($user_req,true);        

        if(!empty($user_object) && !empty($user_array)){
            $user_array=array_map('trim',$user_array);

            $validator = Validator::make($user_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                $resp=array(
                    'status'=>'error',
                    'code'=>400,
                    'message'=>'Usuario no se pudo registrar',
                    'validator'=>$validator->errors()              
                );
            }else{               
                $pwd_hashed = hash('sha256',$user_object->password);

                $user = new \App\User([
                    'name'=>$user_array['name'],
                    'surname'=>$user_array['surname'],
                    'email'=>$user_array['email'],
                    'password'=>$pwd_hashed,
                    'role'=>'ROLE_USER'
                ]);                
                
                $user->save();

                $resp=array(
                    'status'=>'success',
                    'code'=>201,
                    'message'=>'Usuario registrado',
                    'created'=>$user
                );
            }   
        }else{
            $resp=array(
                'status'=>'error',
                'code'=>400,
                'message'=>'Datos incorrectos'                
            );
        }            

        return response()->json(
            $resp,
            $resp['code']
        );
    }

    /**
     * Upload avatar for User.
     *
     * @param  \Illuminate\Http\Request  $request   
     */
    public function storeAvatar(Request $request)
    {
        $file = $request->file('file0');

        $validator = \Validator::make($request->all(), [
            'file0' => 'required|image',    
            'file0' => 'mimes:jpeg,jpg,png,gif'                     
        ]);

        if (!$file || $validator->fails()) {
            $resp=array(
                'status'=>'error',
                'code'=>400,
                'message'=>'file avatar'
            ); 
        }else{
            $avatar_name=time().$file->getClientOriginalName();

            Storage::disk('avatars')->put($avatar_name, \File::get($file));

            $resp=array(
                'status'=>'success',
                'code'=>200,
                'avatar'=>$avatar_name
            );             
        }              

        return response()->json($resp, $resp['code'])
            ->header('Content-Type', 'text/plain');
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
        $user = User::find($id);

        if (is_object($user)) {
            $resp=array(
                'status'=>'success',
                'code'=>200,
                'user'=>$user
            );
        } else {
            $resp=array(
                'status'=>'success',
                'code'=>404,
                'message'=>'User not exists'
            );
        } 
        
        return response()->json($resp, $resp['code']);
    }

    /**
     * Display the avatar.
     *
     * @param  string  $name     
     * @return \Illuminate\Http\Response
     */
    public function showAvatar($image)
    {      
        $exists = Storage::disk('avatars')->exists($image);

        if ($exists) {
            $avatar=Storage::disk('avatars')->get($image);
          
            return response($avatar, 200);
        } else {          
            return response()->json([
                'message'=>'Avatar not exists'
            ], 404);
        }                
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request  
     * @return \Illuminate\Http\Response 
     */
    public function update(Request $request)
    {
        //
        $token_req=$request->header('Authorization');

        $auth=new \JWTAuth();

        $checkToken=$auth->checkToken($token_req);

        $user_req=$request->input('user',null);
            
        $user_array=json_decode($user_req,true); 

        if ($checkToken && !empty($user_array)) {            
            //checkToken puede devolver el objeto del usuario ya validado
            $user_decoded=$auth->checkToken($token_req,true);

            $validator = \Validator::make($user_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users,'.$user_decoded->sub,                
            ]);

            //Columnas que no se van a actualizar
            unset($user_array['id']);
            unset($user_array['role']);
            unset($user_array['password']);
            unset($user_array['created_at']);
            unset($user_array['remember_token']);

            $user_updated=User::where('id',$user_decoded->sub)
                ->update($user_array);

            $resp=array(
                'status'=>'success',
                'code'=>200,
                'user'=>$user_decoded,
                'updates'=>$user_array
            );
        }else{
            $resp=array(
                'status'=>'error',
                'code'=>401,
                'message'=>'Usuario no esta identificado'
            );
        }        

        return response()->json($resp, $resp['code']);
    }    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        //
        $auth=new \JWTAuth();

        $user_req=$request->input('user',null);
        
        $user_object=json_decode($user_req);
        $user_array=json_decode($user_req,true); 

        $validator = Validator::make($user_array, [            
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $resp=array(
                'status'=>'error',
                'code'=>401,
                'message'=>'Usuario no se pudo autorizar',
                'validator'=>$validator->errors(),
                'jwt'=>'error'              
            );
        }else{
            $pwd_hashed=hash('sha256',$user_object->password); 
                       
            $resp=$auth->signup($user_object->email,$pwd_hashed);

            if (!empty($user_object->getToken)) {
                $resp=$auth->signup($user_object->email,$pwd_hashed,true);
            }
        }        

        return response()->json($resp['jwt'], $resp['code']);
    }
}
