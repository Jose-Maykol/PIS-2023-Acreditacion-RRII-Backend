<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Estandar;
use App\Models\RegistrationStatusModel;
use App\Models\RoleModel;
use App\Models\User;
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
		$users = User::all();
		foreach ($users as $user) {
			$user->role = RoleModel::where('id', $user->role_id)->value('name');
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
				"access_token": "11|s3NwExv5FWC7tmsqFUfyB48KFTM6kajH7A1oN3u3"
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
		ruta(put): localhost:8000/api/2023/A/users/update
		ruta(put): localhost:8000/api/2023/A/users/update
		datos:
			{
				"id":"4",
				"role_id":"1",
				"registration_status_id":"2"
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
