<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ministers', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('firstname', 199);
            $table->string('surname', 199);
            $table->string('status', 199);
            $table->string('title', 199)->nullable();
            $table->string('phone', 199)->nullable();
            $table->integer('circuit_id');
            $table->tinyinteger('active');
            $table->string('role', 199)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('ministers');
    }
};
