<?php

use App\Http\Utilities\Utility;
use App\Models\Brand;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->text('fed_txn_status')->nullable()->after('sms_content');
            $table->text('fed_txn_message')->nullable()->after('fed_txn_status');
            $table->text('fed_txn_error_message')->nullable()->after('fed_txn_message');
            $table->text('fed_tpsl_txn_id')->nullable()->after('fed_txn_error_message');
            $table->text('fed_transaction_identifier')->nullable()->after('fed_tpsl_txn_id');
            $table->text('fed_worldline_identifier')->nullable()->after('fed_transaction_identifier');
            $table->text('fed_amount')->nullable()->after('fed_worldline_identifier');
            $table->text('fed_error_message')->nullable()->after('fed_amount');
            $table->text('fed_status_message')->nullable()->after('fed_error_message');
            $table->text('fed_status_code')->nullable()->after('fed_status_message');
            $table->dateTime('fed_date_time')->nullable()->after('fed_status_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('fed_txn_status');
            $table->dropColumn('fed_txn_message');
            $table->dropColumn('fed_txn_error_message');
            $table->dropColumn('fed_tpsl_txn_id');
            $table->dropColumn('fed_transaction_identifier');
            $table->dropColumn('fed_worldline_identifier');
            $table->dropColumn('fed_amount');
            $table->dropColumn('fed_error_message');
            $table->dropColumn('fed_status_message');
            $table->dropColumn('fed_status_code');
            $table->dropColumn('fed_date_time');
        });
    }
};
