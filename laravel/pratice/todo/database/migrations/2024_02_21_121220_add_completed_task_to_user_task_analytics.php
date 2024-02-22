<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompletedTaskToUserTaskAnalytics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_task_analytics', function (Blueprint $table) {
            $table->unsignedSmallInteger('completed_task')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_task_analytics', function (Blueprint $table) {
            $table->dropColumn('completed_task');
        });
    }
}
