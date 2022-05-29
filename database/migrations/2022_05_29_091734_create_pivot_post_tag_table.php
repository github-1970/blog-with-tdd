<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotPostTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_tag', function (Blueprint $table) {
            $table->foreignId('post_id')->nullable()->constrained('posts', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('tag_id')->nullable()->constrained('tags', 'id')->onDelete('cascade')->onUpdate('cascade');

            $table->primary(['post_id', 'tag_id']);

            // if you use null on delete and update, use unique instead of primary.
            // $table->unique(['post_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_tag');
    }
}
