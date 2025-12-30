<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('arsips', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('kategori')->nullable()->index();
            $table->unsignedSmallInteger('tahun')->nullable()->index();
            $table->string('nomor_dokumen')->nullable()->index();
            $table->string('tags')->nullable(); // simpan koma-pisah, opsional diubah ke JSON
            $table->text('ringkasan')->nullable();
            // metadata file
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_mime')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arsips');
    }
};
