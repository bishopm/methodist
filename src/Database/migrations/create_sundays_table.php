<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sundays', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->date('servicedate');
            $table->string('sunday');
            $table->json('readings');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('sundays');
    }
};
