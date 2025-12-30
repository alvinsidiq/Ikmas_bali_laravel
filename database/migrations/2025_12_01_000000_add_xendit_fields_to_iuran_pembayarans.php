<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('iuran_pembayarans', function (Blueprint $table) {
            $table->string('xendit_transaction_id')->nullable()->unique()->after('gateway_reference');
            $table->string('invoice_url')->nullable()->after('xendit_transaction_id');
            $table->string('status_pembayaran')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('iuran_pembayarans', function (Blueprint $table) {
            $table->dropColumn(['xendit_transaction_id','invoice_url','status_pembayaran']);
        });
    }
};
