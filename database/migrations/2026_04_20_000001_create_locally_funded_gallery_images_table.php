<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('locally_funded_gallery_images')) {
            return;
        }

        Schema::create('locally_funded_gallery_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('category', 100);
            $table->string('image_path');
            $table->unsignedInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('project_id')
                ->references('id')
                ->on('locally_funded_projects')
                ->onDelete('cascade');

            $table->foreign('uploaded_by')
                ->references('idno')
                ->on('tbusers')
                ->nullOnDelete();

            $table->index(['project_id', 'category']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('locally_funded_gallery_images')) {
            return;
        }

        Schema::table('locally_funded_gallery_images', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['uploaded_by']);
        });

        Schema::dropIfExists('locally_funded_gallery_images');
    }
};
