<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Estandar;
use App\Models\RegistrationStatusModel;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
	//Login normal (correo y password)
	/*
		ruta(post): /api/auth/login
		ruta(post): /api/auth/login
		datos:
		localhost:8000/api/users/profile
			{
				"email":"jpaniura@unsa.edu.pe",
				"password":"null"
			}
	*/
	public function login(Request $request)
    {

        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        $user = User::where("email", "=", $request->email)
					->where("registration_status_id",RegistrationStatusModel::select('id')->where('description', 'active'))
					->first();

        if (isset($user->id)) {
            if (true) {//Hash::check($request->password, $user->password
                $token = $user->createToken("auth_token")->plainTextToken;
                return response()->json([
                    "message" => "Usuario logueado",
                    "access_token" => $token,
                    "nombre" => $user->name,
                    "apellido" => $user->lastname,
                ], 200);
            } else {
                return response()->json([
                    "message" => "Credenciales inválidas(password)",
                ], 401);
            }
        } else {
            return response()->json([
                "status" => 0,
                "message" => "Usuario no registrado o Usuario deshabilitado",
            ], 404);
        }
    }

	//Login con plataformas externas
	//Funcion de la recepcion del provider(google-facebook-github-twitter)
	/*
		ruta(get): /api/auth/login/{provider}
		ruta(get): /api/auth/login/{provider}
		datos:
			{
				"id_provider":"114480000560878434027"
			}
	*/
	public function redirectToProvider($provider){
		$validated = $this->validateProvider($provider);
		if (!is_null($validated)) {
			return $validated;
		}
		return Socialite::driver($provider)->stateless()->redirect();
		//return Socialite::driver($provider)->redirect();
	}

	//Funcion de la respuesta del provider
	/*
		ruta(get): /api/auth/login/{provider}/callback
		ruta(get): /api/auth/login/{provider}/callback
		datos:
			{
				"id_provider":"114480000560878434027"
			}
	*/
	public function handleProviderCallback($provider){
		$validated = $this->validateProvider($provider);
		if (!is_null($validated)) {
			return $validated;
		}

		try {
			$userProvider = Socialite::driver($provider)->stateless()->user();
		} catch (ClientException $exception) {
			return response()->json(['error' => 'Credenciales de google invalidas.'], 422);
		}

		$user = $user = User::where("email", "=", $userProvider->email)
							->where("registration_status_id",RegistrationStatusModel::select('id')->where('description', 'active'))
							->first();
		if (isset($user)) {
			$userCreated = User::updateOrCreate(
				[
					'email' => $userProvider->email
				],
				[
					//'email_verified_at' => now(),
					'name' => $userProvider->user['given_name'],
					'lastname' => $userProvider->user['family_name'],
					'status' => true
				]
			);

			$userCreated->providers()->updateOrCreate(
				[
					'provider' => $provider,
					'id_provider' => $userProvider->getId()
				],
				[
					'avatar' => $userProvider->getAvatar()
				]
			);

			$token = $userCreated->createToken('token-auth_token')->plainTextToken;
			return response()->json([
				"message" => "Usuario ha iniciado sesion",
				"user" =>  $userCreated,
				"image" =>  $userProvider->getAvatar(),
				"role" => $userCreated->role(),
				"access_token" => $token
			], 200);
		} else {
			return response()->json([
				"status" => 0,
				"message" => "Usuario no registrado o Usuario deshabilitado",
			], 404);
		}
	}


	protected function validateProvider($provider){
		//En caso se quiera iniciar sesion con facebook o github
		//if (!in_array($provider, ['facebook', 'github', 'google'])){
		//por el momento solo con google
		if (!in_array($provider, ['google'])) {
			return response()->json(['error' => 'Para iniciar sesion, usar su cuenta de google'], 422);
		}
	}

	//Logout
	/*
		ruta(get): /api/auth/logout
		ruta(get): /api/auth/logout
		datos:
			{
				"access_token": "5082e3108d0e4d8cdd948c42102aabd0768fe993b86240569aa5130e373f3b8a"
			}
	*/
	public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            "message" => "Sesion cerrada"
        ], 200);
    }

}
