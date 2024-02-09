<?php

namespace App\Http\Middleware;

use App\Models\DateModel;
use App\Models\RegistrationStatusModel;
use Closure;
use Illuminate\Http\Request;

class DateSemesterIsOpenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $year = $request->route('year');
        $semester = $request->route('semester');

        // Buscar el semestre en la base de datos
        $semestre = DateModel::where('year', $year)
            ->where('semester', $semester)
            ->where('registration_status_id', RegistrationStatusModel::registrationActiveId())
            ->first();

        if (!$semestre) {
            return response()->json([
                "status" => 0, 
                "message" => "El periodo no existe."
            ], 404);
        }
        // Verificar si el semestre está abierto
        if ($semestre->is_closed) {
            return response()->json([
                "status" => 0, 
                "message" => "El periodo está cerrado."
            ], 403);
        }

        return $next($request);
    }
}
