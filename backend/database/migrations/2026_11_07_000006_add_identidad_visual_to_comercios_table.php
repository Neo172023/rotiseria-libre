<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::table('comercios', function(Blueprint $table) {
   $table->string('lema', 180)->nullable();
   $table->string('color_primario', 7)->default('#662b20');
   $table->string('color_boton', 7)->default('#b33b22');
  });
 }
 public function down(): void {
  Schema::table('comercios', fn(Blueprint $table) => $table->dropColumn(['lema','color_primario','color_boton']));
 }
};
