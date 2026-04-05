<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bus', function (Blueprint $table) {
            $table->string('numero_motor', 20)->change();
        });
    }

    public function down(): void
    {
        Schema::table('bus', function (Blueprint $table) {
            $table->string('numero_motor', 14)->change();
        });
    }
};