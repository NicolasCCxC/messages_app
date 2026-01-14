<?php

 use App\Models\Prefix;
 use Illuminate\Database\Migrations\Migration;

 class UpdateDataTypePrefixToPrefixesTable extends Migration
 {
     /**
      * Run the migrations.
      *
      * @return void
      */
     public function up()
     {
         $prefixes = Prefix::where('type', 'ELECTRONIC_SUPPORTING_DOCUMENT')->get();
         foreach ($prefixes as $prefix) {
             $prefix->update(['type' => Prefix::SUPPORTING_DOCUMENT]);
         }
     }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down()
     {
         $prefixes = Prefix::where('type', Prefix::SUPPORTING_DOCUMENT)->get();
         foreach ($prefixes as $prefix) {
             $prefix->update(['type' => 'ELECTRONIC_SUPPORTING_DOCUMENT']);
         }
     }
 }