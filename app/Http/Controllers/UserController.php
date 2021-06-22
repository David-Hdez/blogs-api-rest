<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;

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
                $response=array(
                    'status'=>'error',
                    'code'=>400,
                    'message'=>'Usuario no se pudo registrar',
                    'validator'=>$validator->errors()              
                );
            }else{
                $options = [
                    'cost' => 4,
                ];
                $pwd_hashed = password_hash($user_object->password, PASSWORD_BCRYPT, $options);

                $user = new \App\User([
                    'name'=>$user_array['name'],
                    'surname'=>$user_array['surname'],
                    'email'=>$user_array['email'],
                    'password'=>$pwd_hashed,
                    'role'=>'ROLE_USER'
                ]);                
                
                $user->save();

                $response=array(
                    'status'=>'success',
                    'code'=>201,
                    'message'=>'Usuario registrado',
                    'created'=>$user
                );
            }   
        }else{
            $response=array(
                'status'=>'error',
                'code'=>400,
                'message'=>'Datos incorrectos'                
            );
        }            

        return response()->json(
            $response,
            $response['code']
        );
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
}
