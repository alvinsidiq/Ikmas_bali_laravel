<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // e.g., LPR-202510-0001
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->string('kategori')->nullable()->index(); // Pengaduan|Saran|Kegiatan|Fasilitas|Keuangan|Lainnya
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['open','in_progress','resolved','rejected'])->default('open')->index();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('rejected_reason')->nullable();
            $table->unsignedInteger('attachments_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('laporan_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('laporans')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->string('file_mime')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('laporan_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('laporans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_internal')->default(false)->index(); // untuk admin; anggota hanya melihat yang false
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_comments');
        Schema::dropIfExists('laporan_attachments');
        Schema::dropIfExists('laporans');
    }
};

