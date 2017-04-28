<?php namespace Ffte\Search\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateFfteSearchIndices extends Migration
{
    public function up()
    {
        Schema::create('ffte_search_indices', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('model_id')->index()->nullable();
            $table->string('locale')->index();
            $table->string('model_type')->index()->nullable();
            $table->string('item')->index()->nullable();
            $table->text('value')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('ffte_search_indices');
    }
}
