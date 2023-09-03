<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\Estandar;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /*
		ruta(post): localhost:8000/api/2023/A/users/register
		ruta(post): localhost:8000/api/2023/A/users/register
		datos:
			{
				"email":"pfloresq5@unsa.edu.pe",
    			"role_id":"1"
                "access_token": "11|s3NwExv5FWC7tmsqFUfyB48KFTM6kajH7A1oN3u3"
			}
	*/
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
			'role_id'=> 'required|numeric|min:1|max:2'
        ]);

        $userAuth = auth()->user()->name;

        if ($userAuth == "Admin") {
            $user = new UserModel();
            $user->name = "null";
            $user->lastname = "null";
            $user->email = $request->email;
            $user->password = "null";
			$user->role_id = true;
			$user->registration_status_id = true;
            $user->save();
            $user->role()->attach($request->role_id);//check

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
		ruta(get): localhost:8000/api/2023/A/users/profile
		ruta(get): localhost:8000/api/2023/A/users/profile
		datos:
			{
				"access_token": "11|s3NwExv5FWC7tmsqFUfyB48KFTM6kajH7A1oN3u3"
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
				"access_token": "11|s3NwExv5FWC7tmsqFUfyB48KFTM6kajH7A1oN3u3"
			}
	*/
    public function listUser(){
		$users = UserModel::all();
		foreach ($users as $user) {
			$user->role_id = UserModel::find($user->id)->name;
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
				"access_token": "11|s3NwExv5FWC7tmsqFUfyB48KFTM6kajH7A1oN3u3"
			}
	*/
    public function listUserHabilitados(){
		$users = UserModel::whereNotIn("name",["null"])->where("registration_status_id",true)->get();
		foreach ($users as $user) {
			$user->role_id = UserModel::find($user->id)->name;
		}
        return response([
            "status" => 1,
            "msg" => "Lista de usuarios no nulos y habilitados obtenida exitosamente",
            "data" => $users,
        ], 200);
    }


	/*
		ruta(put): localhost:8000/api/2023/A/users/update
		ruta(put): localhost:8000/api/2023/A/users/update
		datos:
			{
				"id":"4",
				"role_id":"1",
				"registration_status_id":"2"
			}
	*/
    public function updateRoleEstado(Request $request){
		$request->validate([
			"id"=>"exists:users",
            "role_id" => "present|nullable|numeric|min:1|max:2",
            "registration_status_id" => "present|nullable|numeric|min:1|max:2"
        ]);
		if(auth()->user()->isAdmin()){
			$user = UserModel::find($request->id);
			$user->update(['registration_status_id' => $request->registration_status_id]);
			//$user->role()->sync([$request->role_id]); check
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
