<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Estandar;
use App\Models\RegistrationStatusModel;
use App\Models\RoleModel;
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
            'email' => 'required|email|unique:users,email',
			'rol'=> 'required|numeric|min:1|max:2'
        ]);

		$userAuth = auth()->user();

        if ($userAuth->isAdmin()) {
            $user = new User();
            $user->name = "null";
            $user->lastname = "null";
            $user->email = $request->email;
            $user->password = "null";
			$user->registration_status_id = RegistrationStatusModel::registrationInactive();
			$user->role_id = RoleModel::roleAdmin();
            $user->save();

            return response()->json([
                'message' => 'Correo registrado exitosamente',
                'user' => $user,
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
			$user->role = RoleModel::where('id', $user->role_id)->value('name');
			$user->status = RegistrationStatusModel::where('id', $user->registration_status_id)->value('description');
		}
        return response([
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
		$users = User::whereNotIn("name",["null"])
					->where("registration_status_id",RegistrationStatusModel::registrationActive())
					->get();
		foreach ($users as $user) {
			$user->role = RoleModel::where('id', $user->role_id)->value('name');
		}
        return response([
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
    public function update(Request $request){
		$request->validate([
			"id"=>"exists:users,id",
            "role_id" => "present|nullable|numeric|min:1|max:2",
            "registration_status_id" => "present|nullable|numeric|min:1|max:2"
        ]);
		if(auth()->user()->isAdmin()){
			$user = User::find($request->id);
			$user->update([
				'registration_status_id' =>$request->registration_status_id,
				'role_id' => $request->role_id
			]);
			return response([
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
