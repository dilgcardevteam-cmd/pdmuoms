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
        if (!Schema::hasTable('locally_funded_gallery_images')) {
            return;
        }

        if (Schema::hasColumn('locally_funded_gallery_images', 'latitude')) {
            return;
        }

        Schema::table('locally_funded_gallery_images', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('image_path');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->decimal('accuracy', 8, 2)->nullable()->after('longitude');
            $table->index('latitude');
            $table->index('longitude');
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
            $table->dropIndex(['latitude']);
            $table->dropIndex(['longitude']);
            $table->dropColumn(['latitude', 'longitude', 'accuracy']);
        });
    }
};
