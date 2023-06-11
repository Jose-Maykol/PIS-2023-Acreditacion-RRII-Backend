<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Estandar;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
	//Login normal (correo y password)
	public function login(Request $request)
    {

        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        $user = User::where("email", "=", $request->email)->where("estado",true)->first();

        if (isset($user->id)) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken("auth_token")->plainTextToken;
                return response()->json([
                    "message" => "Usuario logueado",
                    "access_token" => $token,
                    "nombre" => $user->name,
                    "apellido" => $user->lastname,
                ]);
            } else {
                return response()->json([
                    "message" => "La password es incorrecta",
                ], 404);
            }
        } else {
            return response()->json([
                "status" => 0,
                "message" => "Usuario no registrado o deshabilitado",
            ], 404);
        }
    }

	//Login con plataformas externas
	//Funcion de la recepcion del provider(google-facebook-github-twitter)
	public function redirectToProvider($provider){
		$validated = $this->validateProvider($provider);
		if (!is_null($validated)) {
			return $validated;
		}
		return Socialite::driver($provider)->stateless()->redirect();
		//return Socialite::driver($provider)->redirect();
	}

	//Funcion de la respuesta del provider
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

		$user = $user = User::where("email", "=", $userProvider->email)->where("estado",true)->first();

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
				"message" => "Usuario logueado",
				"user" =>  $userCreated,
				"image" =>  $userProvider->getAvatar(),
				"role" => $userCreated->roles[0]->name,
				"access_token" => $token
			]);
		} else {
			return response()->json([
				"status" => 0,
				"message" => "Usuario no registrado o deshabilitado",
			], 404);
		}
	}


	protected function validateProvider($provider){
		//En caso se quiera iniciar sesion con facebook o github
		//if (!in_array($provider, ['facebook', 'github', 'google'])){
		//por el momento solo con google
		if (!in_array($provider, ['google'])) {
			return response()->json(['error' => 'Por favor usar google para loguearse'], 422);
		}
	}

	//Logout
	public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            "message" => "Sesion cerrada"
        ]);
    }

}
