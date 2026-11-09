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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_name');
            $table->string('email')->unique();
            $table->string('plan')->default('Growth');
            $table->timestamps();
        });
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject', 160);
            $table->text('description');
            $table->enum('status', ['open', 'in_progress', 'waiting', 'resolved'])->default('open');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->text('resolution')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedInteger('version')->default(0);
            $table->timestamps();
            $table->index(['status', 'updated_at']);
            $table->index('priority');
        });
        Schema::create('ticket_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('summary', 1000);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_events');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('customers');
    }
};
