<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            // What is being reported: a listing or a user account. The morph
            // type stores a short alias ('listing' | 'user') via the morph map
            // registered in AppServiceProvider.
            $table->morphs('reportable');
            // Reporter; kept nullable so a report survives the reporter's deletion.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            // 'spam' | 'prohibited' | 'fraud' | 'offensive' | 'other' — see
            // App\Enums\ReportReason.
            $table->string('reason');
            $table->text('description')->nullable();
            // 'pending' | 'resolved' | 'dismissed' — see App\Enums\ReportStatus.
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
