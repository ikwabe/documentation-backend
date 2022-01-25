<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentMainSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_main_sections', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('document_id',false,true);
            $table->longText('contents');
            $table->timestamps();
        });

        Schema::table('document_sections', function(Blueprint $table){
            $table->foreign('content_id')->references('id')->on('document_main_sections')->onDelete('cascade');
        });

        Schema::table('document_main_sections', function(Blueprint $table){
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_main_sections');
    }
}
