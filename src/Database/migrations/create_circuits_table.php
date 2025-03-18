<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('circuits', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('circuit', 199);
            $table->string('slug', 199);
            $table->integer('district_id');
            $table->integer('reference');
            $table->integer('plan_month');
            $table->string('contact', 199)->nullable();
            $table->string('showphone', 10)->nullable();
            $table->string('activated', 10)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('circuits');
    }
};
