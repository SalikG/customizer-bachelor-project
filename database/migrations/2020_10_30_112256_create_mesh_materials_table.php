<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeshMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mesh_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_3d_model_id')->constrained('product_3d_models')->onDelete('cascade');
            $table->string('material_name', 50)->nullable(false);
            $table->string('display_name', 50)->nullable(false);
            $table->string('texture_setting_wrap_s', 50)->nullable(false)->default('RepeatWrapping');
            $table->string('texture_setting_wrap_t', 50)->nullable(false)->default('RepeatWrapping');
            $table->decimal('texture_setting_repeat_u', 4, 2)->nullable(false)->default(1.0);
            $table->decimal('texture_setting_repeat_v', 4, 2)->nullable(false)->default(1.0);
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
        Schema::dropIfExists('mesh_materials');
    }
}
