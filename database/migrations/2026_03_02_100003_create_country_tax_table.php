<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_tax', function (Blueprint $table) {
            $table->uuid('country_id');
            $table->uuid('tax_id');

            $table->foreign('country_id')->references('id')->on('countries')->cascadeOnDelete();
            $table->foreign('tax_id')->references('id')->on('taxes')->cascadeOnDelete();

            $table->primary(['country_id', 'tax_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_tax');
    }
};
