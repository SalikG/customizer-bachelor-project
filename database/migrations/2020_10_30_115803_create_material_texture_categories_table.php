<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialTextureCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_texture_categories', function (Blueprint $table) {
            $table->primary(['mesh_material_id', 'texture_category_id'], 'mesh_material_id_texture_category_id_primary');
            $table->foreignId('mesh_material_id')->constrained('mesh_materials')->onDelete('cascade');
            $table->foreignId('texture_category_id')->constrained('texture_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mesh_material_texture_categories');
    }
}
