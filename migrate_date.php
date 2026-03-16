<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('usuario', function (Blueprint $table) {
    if (!Schema::hasColumn('usuario', 'fecha_nacimiento')) {
        $table->date('fecha_nacimiento')->nullable()->after('telefono');
    }
});
echo "Column added\n";
