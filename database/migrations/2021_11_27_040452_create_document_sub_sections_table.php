<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentSubSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_sub_sections', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('section_id',false,true);
            $table->string('title');
            $table->longText('contents');
            $table->timestamps();
        });

        Schema::table('document_sub_sections', function(Blueprint $table){
            $table->foreign('section_id')->references('id')->on('document_sections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_sub_sections');
    }
}
