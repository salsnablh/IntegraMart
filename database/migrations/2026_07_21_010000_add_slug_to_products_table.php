<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'slug')) {
                $table->string('slug')->nullable()->after('name')->unique();
            }
        });

        DB::table('products')
            ->select('id', 'name')
            ->orderBy('id')
            ->get()
            ->each(function (object $product) {
                $baseSlug = Str::slug($product->name);
                $slug = $baseSlug;
                $counter = 2;

                while (DB::table('products')->where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = $baseSlug.'-'.$counter;
                    $counter++;
                }

                DB::table('products')->where('id', $product->id)->update(['slug' => $slug]);
            });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'slug')) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            }
        });
    }
};
