<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_kategori', function (Blueprint $table) {
            $table->foreignId('master_item_id')
                ->constrained('master_items')
                ->cascadeOnDelete();

            $table->foreignId('kategori_item_id')
                ->constrained('kategori_items')
                ->cascadeOnDelete();

            $table->unique([
                'master_item_id',
                'kategori_item_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_kategori', function (Blueprint $table) {
            $table->dropForeign(['master_item_id']);
            $table->dropForeign(['kategori_item_id']);

            $table->dropUnique([
                'master_item_id',
                'kategori_item_id'
            ]);

            $table->dropColumn([
                'master_item_id',
                'kategori_item_id'
            ]);
        });
    }
};
