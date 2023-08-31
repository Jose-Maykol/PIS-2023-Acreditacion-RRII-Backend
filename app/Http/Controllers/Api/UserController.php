<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Estandar;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /*
		ruta(post): /api/users/register
		ruta(post): /api/users/register
		datos:
			{
				"email":"jpaniura@unsa.edu.pe"
                "rol":"1"
                "access_token": "5082e3108d0e4d8cdd948c42102aabd0768fe993b86240569aa5130e373f3b8a"
			}
	*/
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
			'rol'=> 'required|numeric|min:1|max:2'
        ]);

        $userAuth = auth()->user()->roles[0]->name;

        if ($userAuth == "Admin") {
            $user = new User();
            $user->name = "null";
            $user->lastname = "null";
            $user->email = $request->email;
            $user->password = "null";
			$user->estado = true;
            $user->save();
            $user->roles()->attach($request->rol);

            return response()->json([
                'message' => 'Correo registrado exitosamente',
                'userAuth' => $user,
            ], 201);
        } else {
            return response()->json([
                "status" => 0,
                "message" => "No eres administrador: Correo no registrado",
            ], 403);
        }
    }



    /*
		ruta(get): /api/users/profile
		ruta(get): /api/users/profile
		datos:
			{
				"access_token": "5082e3108d0e4d8cdd948c42102aabd0768fe993b86240569aa5130e373f3b8a"
			}
	*/
    public function userProfile()
    {
        return response()->json([
            "status" => 1,
            "message" => "Perfil de usuario obtenido exitosamente",
            "data" => auth()->user(),
        ], 200);
    }

	/*
		ruta(get): /api/users/user
		ruta(get): /api/users/user
		datos:
			{
				"access_token": "5082e3108d0e4d8cdd948c42102aabd0768fe993b86240569aa5130e373f3b8a"
			}
	*/
    public function listUser(){
		$users = User::all();
		foreach ($users as $user) {
			$user->rol=User::find($user->id)->roles[0]->name;
		}
        return response([
            "status" => 1,
            "msg" => "Lista de usuarios obtenida exitosamente",
            "data" => $users,
        ], 200);
    }

	/*
		ruta(get): /api/users/enabled_users
		ruta(get): /api/users/enabled_users
		datos:
			{
				"access_token": "5082e3108d0e4d8cdd948c42102aabd0768fe993b86240569aa5130e373f3b8a"
			}
	*/
    public function listUserHabilitados(){
		$users = User::whereNotIn("name",["null"])->where("estado",true)->get();
		foreach ($users as $user) {
			$user->rol=User::find($user->id)->roles[0]->name;
		}
        return response([
            "status" => 1,
            "msg" => "Lista de usuarios no nulos y habilitados obtenida exitosamente",
            "data" => $users,
        ], 200);
    }


	/*
		ruta(put): /api/users/update
		ruta(put): /api/users/update
		datos:
			{
				"id":"jpaniura@unsa.edu.pe"
                "role":"1"
                "estado":"true"
                "access_token": "5082e3108d0e4d8cdd948c42102aabd0768fe993b86240569aa5130e373f3b8a"
			}
	*/
    public function updateRoleEstado(Request $request){
		$request->validate([
			"id"=>"exists:users",
            "role" => "present|nullable|numeric|min:1|max:2",
            "estado" => "present|nullable|boolean"
        ]);
		if(auth()->user()->isAdmin()){
			$user = User::find($request->id);
			$user->update(['estado' =>$request->estado]);
			$user->roles()->sync([$request->role]);
			return response([
	            "status" => 1,
	            "msg" => "Usuario actualizado exitosamente",
	            "data" => $user,
	        ], 200);
		}
		else{
			return response()->json([
				"status" => 0,
				"message" => "No tienes permisos de administrador",
			], 403);
		}
	}
}
