<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTextureCategoryItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('texture_category_items', function (Blueprint $table) {
            $table->primary(['texture_category_id', 'texture_id']);
            $table->foreignId('texture_category_id')->constrained('texture_categories')->onDelete('cascade');
            $table->foreignId('texture_id')->constrained('textures')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('texture_category_items');
    }
}
