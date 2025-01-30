<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('catalogos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Nombre del catálogo
            $table->string('archivo_pdf'); // Ruta del PDF
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('catalogos');
    }
};
