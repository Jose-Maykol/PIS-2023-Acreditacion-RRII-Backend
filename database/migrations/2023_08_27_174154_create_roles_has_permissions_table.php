<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles_has_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_role');
            $table->unsignedBigInteger('id_permission');
            $table->timestamps();

            $table->foreign('id_role')->references('id')->on('roles');
            $table->foreign('id_permission')->references('id')->on('permissions');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles_has_permissions');
    }
};
