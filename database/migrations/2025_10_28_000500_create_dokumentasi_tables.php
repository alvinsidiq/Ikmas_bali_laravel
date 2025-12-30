<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dokumentasi_albums', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->date('tanggal_kegiatan')->nullable()->index();
            $table->string('lokasi')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('tags')->nullable();
            $table->string('cover_path')->nullable();
            $table->unsignedInteger('media_count')->default(0);
            $table->unsignedBigInteger('view_count')->default(0);
            $table->boolean('is_published')->default(false)->index();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('dokumentasi_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained('dokumentasi_albums')->cascadeOnDelete();
            $table->string('media_path');
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('caption')->nullable();
            $table->boolean('is_cover')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumentasi_media');
        Schema::dropIfExists('dokumentasi_albums');
    }
};
