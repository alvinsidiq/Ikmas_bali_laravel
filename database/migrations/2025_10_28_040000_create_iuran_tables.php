<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('iuran_tagihans', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // INV-202510-0001
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul'); // Iuran Bulanan Okt 2025
            $table->string('periode')->nullable()->index(); // 2025-10
            $table->unsignedBigInteger('nominal'); // Rupiah
            $table->unsignedBigInteger('denda')->default(0);
            $table->unsignedBigInteger('diskon')->default(0);
            $table->unsignedBigInteger('terbayar_verified')->default(0); // cache terbayar (verified)
            $table->date('jatuh_tempo')->nullable()->index();
            $table->enum('status', ['unpaid','partial','paid','overdue'])->default('unpaid')->index();
            $table->timestamp('paid_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('iuran_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // PAY-202510-0001
            $table->foreignId('tagihan_id')->constrained('iuran_tagihans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('amount'); // Rupiah
            $table->dateTime('paid_at')->nullable();
            $table->string('method', 20)->nullable(); // transfer|cash|qris
            $table->string('bukti_path')->nullable();
            $table->string('bukti_mime')->nullable();
            $table->unsignedBigInteger('bukti_size')->nullable();
            $table->enum('status', ['submitted','verified','rejected'])->default('submitted')->index();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iuran_pembayarans');
        Schema::dropIfExists('iuran_tagihans');
    }
};

