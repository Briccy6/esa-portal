<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('live_class_signals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('live_class_id');
            $table->string('from_peer');       // e.g. "teacher" or "student_42"
            $table->string('to_peer');         // e.g. "student_42" or "all"
            $table->string('type');            // offer | answer | ice-candidate | chat | hand-raise | participant-update
            $table->longText('payload');       // JSON
            $table->timestamps();
            $table->index(['live_class_id', 'to_peer', 'created_at']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('live_class_signals');
    }
};