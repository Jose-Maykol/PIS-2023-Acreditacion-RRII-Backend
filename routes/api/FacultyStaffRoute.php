<?php

use App\Http\Controllers\Api\FacultyStaffController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware("auth:sanctum")->prefix('faculty-staff')->group(function () {
    Route::post('', [FacultyStaffController::class, 'createFacultyStaff']);
    Route::put('', [FacultyStaffController::class, 'updateFacultyStaff']);
    Route::get('', [FacultyStaffController::class, 'getFacultyStaff']);
});