<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('arsips', function (Blueprint $table) {
            $table->string('thumbnail_url')->nullable()->after('ringkasan');
        });

        Schema::table('forum_topics', function (Blueprint $table) {
            $table->string('banner_url')->nullable()->after('kategori');
        });
    }

    public function down(): void
    {
        Schema::table('arsips', function (Blueprint $table) {
            $table->dropColumn('thumbnail_url');
        });

        Schema::table('forum_topics', function (Blueprint $table) {
            $table->dropColumn('banner_url');
        });
    }
};
