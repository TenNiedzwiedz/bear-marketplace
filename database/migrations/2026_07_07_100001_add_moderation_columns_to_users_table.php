<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Staff flag: grants access to the moderation panel (/admin).
            $table->boolean('is_admin')->default(false)->after('account_type');
            // When set, the account is blocked: it cannot sign in and its
            // listings drop out of public view. Null means active.
            $table->timestamp('blocked_at')->nullable()->after('is_admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_admin', 'blocked_at']);
        });
    }
};
