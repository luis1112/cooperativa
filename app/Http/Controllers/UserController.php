<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Str;

use App\Http\Helper\ResponseBuilder;
use App\Models\User;


class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function login(Request $request){
        $usuario=$request->username;
        $clave=$request->password;
        $user= User::where('username', $usuario)->first();
        if(!empty($user)){
            if($this->django_password_verify($clave, $user->password)){
                $user->api_token = Str::random(150);
                $user->save();
                $status=true;
                $info="USE IS OK";

              return ResponseBuilder::result($status, $info, $user);  
            }else{
                $status=false;
                $info="USER IS INCORRECT";
            }
        }else{
            $status=false;
            $info="USER IS INCORRECT";
        }
        return ResponseBuilder::result($status,$info);
    }
    public function logout(Request $request){
        $usuario = $request->username;
        $user = User::where('username', $usuario)->first();
        if (!empty($user)){
            $user->api_token = null;
            $user->save();
            $status = true;
            $info = "Logout correct";
        }else{
            $status = false;
            $info = "User is incorrect";
        }
        return ResponseBuilder::result($status,$info);
    }
    

    public function django_password_verify(string $password, string $djangoHash): bool{
        $pieces = explode('$', $djangoHash);
        if (count($pieces) !== 4) {
            throw new Exception("Illegal hash format");
        }
        list($header, $iter, $salt, $hash) = $pieces;
        // Get the hash algorithm used:
        if (preg_match('#^pbkdf2_([a-z0-9A-Z]+)$#', $header, $m)) {
            $algo = $m[1];
        } else {
            throw new Exception(sprintf("Bad header (%s)", $header));
        }
        if (!in_array($algo, hash_algos())) {
            throw new Exception(sprintf("Illegal hash algorithm (%s)", $algo));
        }
        //PBKDF2
        // algo = md5, sha256, haval1604.....
        // salt = generar valores aleatorios
        $calc = hash_pbkdf2(
            $algo,
            $password,
            $salt,
            (int) $iter,
            32,
            true
        );
        return hash_equals($calc, base64_decode($hash));
    }   
}