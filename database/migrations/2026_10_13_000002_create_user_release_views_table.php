<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('user_release_views', function (Blueprint $table): void {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('version', 32);
            $table->dateTime('seen_at');
            $table->primary(['user_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_release_views');
    }
};
