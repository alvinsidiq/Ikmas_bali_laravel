<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('iuran_pembayarans', function (Blueprint $table) {
            $table->string('channel')->default('manual')->after('method');
            $table->string('gateway')->nullable()->after('channel');
            $table->string('gateway_reference')->nullable()->after('gateway');
            $table->string('gateway_receipt_url')->nullable()->after('gateway_reference');
            $table->json('gateway_payload')->nullable()->after('gateway_receipt_url');
        });
    }

    public function down(): void
    {
        Schema::table('iuran_pembayarans', function (Blueprint $table) {
            $table->dropColumn(['channel','gateway','gateway_reference','gateway_receipt_url','gateway_payload']);
        });
    }
};
