<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('event_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('title', 150);
            $table->enum('demand_type', ['coverage', 'art', 'both']);
            $table->string('location')->index();
            $table->string('target_audience');
            $table->text('description')->nullable();
            $table->dateTime('start_at')->index();
            $table->dateTime('end_at')->index();
            $table->unsignedSmallInteger('duration_minutes');
            $table->dateTime('original_start_at')->nullable();
            $table->enum('status', ['pending', 'approved', 'rescheduled', 'rejected', 'cancelled'])
                ->default('pending')->index();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('moderation_notes')->nullable();
            $table->dateTime('moderated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'start_at', 'end_at'], 'idx_calendar_lookup');
            $table->index(['location', 'start_at', 'end_at', 'status'], 'idx_conflict_lookup');
            $table->index(['user_id', 'status', 'created_at'], 'idx_user_requests');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_requests');
    }
};
