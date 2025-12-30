<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('kategori')->nullable()->index();
            $table->longText('body')->nullable(); // opsi simpan konten pembuka
            $table->boolean('is_open')->default(true)->index();
            $table->boolean('is_pinned')->default(false)->index();
            $table->boolean('is_solved')->default(false)->index();
            $table->unsignedBigInteger('solved_post_id')->nullable();
            $table->unsignedInteger('posts_count')->default(0);
            $table->timestamp('last_post_at')->nullable()->index();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('forum_topics')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->longText('content');
            $table->boolean('is_solution')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_posts');
        Schema::dropIfExists('forum_topics');
    }
};
