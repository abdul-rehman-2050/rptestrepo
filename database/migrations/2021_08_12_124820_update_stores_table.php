<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
           
            $table->string('phone')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->dropColumn(['code']);
        });
        Schema::table('repairs', function (Blueprint $table) {
            $table->integer('store_id')->unsigned()->nullable();
            $table->foreign('store_id')->references('id')->on('stores');
            $table->string('store')->nullable();
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->integer('store_id')->unsigned()->nullable();
            $table->foreign('store_id')->references('id')->on('stores');
            $table->string('store')->nullable();
        });
        Schema::table('costing', function (Blueprint $table) {
            $table->integer('store_id')->unsigned()->nullable();
            $table->foreign('store_id')->references('id')->on('stores');
            $table->string('store')->nullable();
        });
        Schema::table('pos_registers', function (Blueprint $table) {
            $table->integer('store_id')->unsigned()->nullable();
            $table->foreign('store_id')->references('id')->on('stores');
            $table->string('store')->nullable();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->integer('store_id')->unsigned()->nullable();
            $table->foreign('store_id')->references('id')->on('stores');
            $table->string('store')->nullable();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->integer('store_id')->unsigned()->nullable();
            $table->foreign('store_id')->references('id')->on('stores');
            $table->string('store')->nullable();
            $table->integer('default_store')->nullable();
            $table->longText('stores')->nullable();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
