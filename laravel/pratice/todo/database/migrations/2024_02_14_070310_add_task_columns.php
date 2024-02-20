<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaskColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('title',20);
            $table->string('description',100);
            $table->boolean('urgency')->default(false);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('status')->default(false);
            $table->dateTime('time_completed')->nullable();

            $table->foreign('parent_id')->references('id')->on('tasks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('description');
            $table->dropColumn('urgency');
            $table->dropColumn('parent_id');
            $table->dropColumn('status');
            $table->dropColumn('time_completed');
        });
    }
}
