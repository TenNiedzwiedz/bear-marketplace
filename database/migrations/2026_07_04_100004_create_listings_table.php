<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // A category with listings cannot be deleted out from under them.
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 10, 2)->nullable(); // null = cena do uzgodnienia
            $table->string('currency', 3)->default('PLN');
            $table->boolean('is_negotiable')->default(false);
            $table->string('location');
            // 'draft' | 'active' | 'ended' | 'hidden' — see App\Enums\ListingStatus.
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            // Public listing queries filter by status and sort by publish time.
            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
