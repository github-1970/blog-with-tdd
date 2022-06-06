<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users', 'id')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('category_id')->nullable()->constrained('categories', 'id')->nullOnDelete()->cascadeOnUpdate();
            $table->string('title');
            $table->string('description', 200);
            $table->text('body');
            // comment this, because it very slow for get all data
            // $table->fullText('body');
            $table->timestamp('published_at')->useCurrent();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
