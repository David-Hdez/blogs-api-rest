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
        $user=$request->input('user',null);
        
        $user_object=json_decode($user);
        $user_array=json_decode($user,true);        

        if(!empty($user_object) && !empty($user_array)){
            $user_array=array_map('trim',$user_array);

            $validator = Validator::make($user_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email',
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
                $response=array(
                    'status'=>'success',
                    'code'=>201,
                    'message'=>'Usuario registrado'                
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

    public function login(Request $request)
    {
        //
    }
}
