<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\Estandar;
use App\Models\RegistrationStatusModel;
use App\Models\RoleModel;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
	protected $userService;

	public function __construct(UserService $userService){
	
		$this->userService = $userService;
	}

    //Registrar usuario
    public function register(UserRequest $request)
    {
		try{

			$request->validated();
			$result = $this->userService->createUser($request);

			return response()->json([
                'status' => 1,
                'message' => 'Usuario creado con éxito'
            ], 201);
		}
		catch (\Illuminate\Validation\ValidationException $e){
			return response()->json(['errors' => $e->errors()], 400);
		}
		catch (\App\Exceptions\User\UserNotAuthorizedException $e){
			return response()->json([
				'status' => 0,
				'message' => $e->getMessage(),
			], $e->getCode());
		}
		catch (\App\Exceptions\User\EmailAlreadyExistsException $e){
			return response()->json([
				'status' => 0,
				'message' => $e->getMessage(),
			], $e->getCode());
		}
		catch (\App\Exceptions\User\RoleNotFoundException $e){
			return response()->json([
				'status' => 0,
				'message' => $e->getMessage(),
			], $e->getCode());
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
    public function listUser(UserRequest $request)
		{
			$user = auth()->user();

			$items = $request->input('items') ?? 8;
			$currentPage = $request->input('page') ?? 1;
			$search = $request->input('search', '');

			$usersQuery = User::where('users.id', '!=', $user->id)
        ->join('registration_status', 'users.registration_status_id', '=', 'registration_status.id')
        ->select("users.id", "users.name", "users.lastname", "users.email", "registration_status.description as status")
        ->orderBy('users.id', 'asc');

			if (!empty($search)) {
					$usersQuery->where(function ($query) use ($search) {
							$query->where('users.name', 'ilike', '%' . $search . '%')
									->orWhere('users.lastname', 'ilike', '%' . $search . '%');
					});
			}

			$users = $usersQuery->paginate($items, ['*'], 'page', $currentPage); 

			foreach ($users as $index => $user) {
					$roles = $user->getRoleNames();
					$user->role = $roles->isNotEmpty() ? $roles[0] : null;
					$user->unsetRelation('roles'); 
					$user->index = ($currentPage - 1) * $items + $index + 1;
			}

			return response([
					"status" => 1,
					"data" => [
							"users" => $users->items(),
							"total" => $users->total(),
							"current_page" => $users->currentPage(),
							"last_page" => $users->lastPage(),
							"has_more_pages" => $users->hasMorePages(),
					],
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

    public function updateUser(Request $request){
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
			$role = Role::find($request->role_id);
	
			if (!$role) {
				return response()->json([
					'status' => 0,
					'message' => 'El rol proporcionado no existe',
				], 404);
			}

			$role = $request->role_id == 1 ? 'administrador' : 'docente';
	
			 // Obtener el rol actual del usuario
			 $userRole = $user->getRoleNames()->first();

			 if ($userRole) {
					 // Eliminar el rol actual del usuario
					 $user->removeRole($userRole);
			 }

			 // Asignar el nuevo rol al usuario
			 $user->assignRole($role);
	
			return response()->json([
				'status' => 1,
				'message' => 'Rol de usuario actualizado exitosamente',
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
	        	"message" => "Estado de usuario actualizado exitosamente",
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
