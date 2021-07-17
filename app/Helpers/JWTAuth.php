<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JWTAuth 
{
   private $key;

   public function __construct() {
      $this->key='v487k899b53n8e7BJj8D';
   }

   public function signup($email, $password, $getToken=null){
      $user=User::where([
         'email'=>$email,
         'password'=>$password
      ])->first();

      $signed_in=false;

      if (is_object($user)) {
         $signed_in=true;
      }

      if ($signed_in) {
         $payload = array(
            'sub' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'surname' => $user->surname,
            'iat' => time(),
            'exp' => time()+(7*24*60*60)
         );
        
         $jwt = JWT::encode($payload, $this->key, 'HS256');

         $decoded = JWT::decode($jwt, $this->key, array('HS256'));

         if (is_null($getToken)) {
            $response= array(
               'jwt'=>$jwt,
               'code'=>200
            );
         } else {
            $response= array(
               'jwt'=>$decoded,
               'code'=>200
            );
         }
         
      } else {
         $response=array(
            'status'=>'error',
            'code'=>404,            
            'jwt'=>'Usuario no esta registrado'
         );
      }      

      return $response;
   }
}
