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
            'image' => $user->img,
            'description' => $user->description,
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
            'code'=>404,            
            'jwt'=>'Usuario no esta registrado'
         );
      }      

      return $response;
   }

   public function checkToken($jwt, $getIdentity=false){
      $authorized=false;

      try {
         $jwt=str_replace('"','',$jwt);

         $decoded = JWT::decode($jwt, $this->key, array('HS256'));
      } catch (\UnexpectedValueException $uve) {
         $authorized=false;         
      }catch(\DomainException $de){
         $authorized=false;
      }  
      
      if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
         $authorized=true;
      } else {
         $authorized=false;
      }

      if ($getIdentity) {
         return $decoded;
      }
      
      return $authorized;
   }
}
