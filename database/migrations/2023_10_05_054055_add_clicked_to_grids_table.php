<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('grids', function (Blueprint $table) {
            $table->boolean('clicked')->default(false);
            $table->unsignedBigInteger('reward_item_id')->nullable();
            $table->foreign('reward_item_id')->references('id')->on('rewards')->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('grids', function (Blueprint $table) {
            $table->dropColumn('clicked');
            $table->dropForeign(['reward_item_id']);
            $table->dropColumn('reward_item_id');
        });
    }

};
