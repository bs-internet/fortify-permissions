<?php

use App\Enums\UserStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('title')->nullable()->after('email');
            $table->tinyInteger('status')->default(UserStatus::ACTIVE->value)->after('title')->index();
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->foreignUuid('language_id')->nullable()->constrained('languages')->nullOnDelete()->after('last_login_at');
            $table->index('language_id');
            $table->softDeletes()->after('language_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['language_id']);
            $table->dropConstrainedForeignId('language_id');
            $table->dropColumn([
                'title',
                'status',
                'last_login_at',
            ]);
            $table->dropSoftDeletes();
        });
    }
};
