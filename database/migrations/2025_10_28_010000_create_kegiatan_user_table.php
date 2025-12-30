<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kegiatan_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('registered')->index(); // registered|canceled|attended (opsional)
            $table->string('kode', 16)->unique(); // kode pendaftaran
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();
            $table->unique(['kegiatan_id','user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatan_user');
    }
};

