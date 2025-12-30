<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('anggota_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nik')->nullable()->index();
            $table->string('nama_lengkap')->nullable();
            $table->string('phone')->nullable();
            $table->text('alamat')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L','P'])->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('organisasi')->nullable();
            $table->string('avatar_path')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_profiles');
    }
};
