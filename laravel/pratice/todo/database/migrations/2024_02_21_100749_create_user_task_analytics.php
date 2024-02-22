<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTaskAnalytics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_task_analytics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedSmallInteger('yet_to_do_task')->default(0);
            $table->unsignedSmallInteger('due_task')->default(0);
            $table->unsignedSmallInteger('weekly_complete_task')->default(0);
            $table->unsignedSmallInteger('monthly_complete_task')->default(0);
            $table->unsignedSmallInteger('quaterly_complete_task')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_task_analytics');
    }
}
