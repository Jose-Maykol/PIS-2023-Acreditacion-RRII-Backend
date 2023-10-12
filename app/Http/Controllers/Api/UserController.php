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

use Spatie\Permission\Models\Role;

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
			'role'=> 'required|string|in:administrador,docente',
            'email' => 'required|email'
        ]);

		if(User::where('email', $request->email)->exists())
		{
			return response()->json([
				'status' => 0,
                'message' => 'Correo existente',
            ], 422);
		}

		$userAuth = auth()->user();

        if ($userAuth->isAdmin()) {
            $user = new User();
            $user->name = "NOMBRES";
            $user->lastname = "APELLIDOS";
            $user->email = $request->email;
            $user->password = "null";
			$user->registration_status_id = RegistrationStatusModel::registrationAuthenticationPendingId();
            $user->save();
			$user->assignRole($request->role);

            return response()->json([
				'status' => 1,
                'message' => 'Correo registrado exitosamente',
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
    public function listUser()
	{
		$users = User::join('registration_status', 'users.registration_status_id', '=', 'registration_status.id')
			->select("users.id", "users.name", "users.lastname", "users.email", "registration_status.description as status")
			->orderBy('users.id', 'asc')
			->get();
		foreach ($users as $user) {
			$roles = $user->getRoleNames();
			$user->role = $roles->isNotEmpty() ? $roles[0] : null;
			$user->unsetRelation('roles'); 
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
    public function listEnabledUsers()
	{
		$users = User::whereNotIn("name",["null"])
			->where("registration_status_id",RegistrationStatusModel::registrationActiveId())
			->join('registration_status', 'users.registration_status_id', '=', 'registration_status.id')
    		->select("users.id", "users.name", "users.lastname", "users.email", "registration_status.description as status")
			->orderBy('users.id', 'asc')
			->get();
		foreach ($users as $user) {
			$roles = $user->getRoleNames();
    		$user->role = $roles->isNotEmpty() ? $roles[0] : null;
    		$user->unsetRelation('roles'); 
		}
        return response([
						"status" => 1,
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
            "role" => "present|nullable",
            "registration_status_id" => "present|nullable|numeric|min:1|max:2"
        ]);
		if(auth()->user()->isAdmin()){

			$user = User::find($request->id);
			$user->update([
				'registration_status_id' =>$request->registration_status_id,
			]);
			if($request->role != null){
				$user->syncRoles([]);
				$user->assignRole($request->role);
			}
			
			return response([
	            "message" => "Usuario actualizado exitosamente",
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

	/*
		ruta(put): localhost:8000/api/users/updateRole/{user_id}
		ruta(put): localhost:8000/api/users/updateRole/8
		datos:
			{
				"id":"8",
				"role_id":"2",
				"registration_status_id":"1"
			}
	*/
    public function updateRole($user_id, Request $request)
	{
		$request->validate([
			'role_id' => 'present|nullable|numeric|min:1|max:2',
		]);
	
		// Verificar si el usuario actual tiene permisos de administrador
		if(auth()->user()->isAdmin()) {
			$user = User::find($user_id);
	
			if (!$user) {
				return response()->json([
					'status' => 0,
					'message' => 'Usuario no encontrado',
				], 404);
			}
	
			// Verificar si el rol proporcionado existe
			$role = null;
	
			switch ($request->role_id) {
				case 1:
					$role = Role::where('name', 'administrador')->first();
					break;
	
				case 2:
					$role = Role::where('name', 'docente')->first();
					break;
	
				default:
					return response()->json([
						'status' => 0,
						'message' => 'El valor de role_id no es válido',
					], 422);
			}
	
			if (!$role) {
				return response()->json([
					'status' => 0,
					'message' => 'El rol proporcionado no existe',
				], 422);
			}
	
			// Asignar el rol al usuario
			$user->assignRole($role);
	
			return response()->json([
				'status' => 1,
				'message' => 'Usuario actualizado exitosamente',
			], 200);
		} else {
			return response()->json([
				'status' => 0,
				'message' => 'No tienes permisos de administrador',
			], 403);
		}
	}

	public function updateStatus($user_id, Request $request)
	{
		$request->validate([
			"registration_status_id" => "present|nullable|numeric|min:1|max:2"
		]);

		if(auth()->user()->isAdmin()){
			$user = User::find($user_id);
			$user->update([
				'registration_status_id' =>$request->registration_status_id,
			]);
			return response([
				"status" => 1,
	        	"message" => "Usuario actualizado exitosamente",
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
