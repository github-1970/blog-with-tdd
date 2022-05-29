<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotTaggablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taggables', function (Blueprint $table) {
            $table->foreignId('tag_id')->nullable()->constrained('tags', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->morphs('taggable');

            $table->primary(['tag_id', 'taggable_id', 'taggable_type']);

            // if you use null on delete and update, use unique instead of primary.
            // $table->unique(['post_id', 'tag_id']);
            // $table->unique(['tag_id', 'taggable_id', 'taggable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taggables');
    }
}
