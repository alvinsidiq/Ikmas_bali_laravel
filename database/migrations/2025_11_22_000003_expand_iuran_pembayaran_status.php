<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE iuran_pembayarans MODIFY COLUMN status ENUM('submitted','pending_gateway','verified','rejected') DEFAULT 'submitted'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE iuran_pembayarans MODIFY COLUMN status ENUM('submitted','verified','rejected') DEFAULT 'submitted'");
    }
};
