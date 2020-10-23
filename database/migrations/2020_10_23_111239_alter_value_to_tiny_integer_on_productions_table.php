<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterValueToTinyIntegerOnProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('productions', function (Blueprint $table) {
            // $table->tinyInteger('value')->change();
            // $table->smallInteger('value')->change();
            // $table->tinyInteger('value')->change();
            DB::select('ALTER TABLE `productions` CHANGE `value` `value` TINYINT(11) NOT NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->integer('value')->change();
        });
    }
}
