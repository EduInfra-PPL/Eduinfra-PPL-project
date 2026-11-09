<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('email', 'admin@eduinfra.test')
            ->whereNull('role_enum')
            ->update(['role_enum' => 'admin_dinas']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('email', 'admin@eduinfra.test')
            ->where('role_enum', 'admin_dinas')
            ->update(['role_enum' => null]);
    }
};