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
        // Forum Categories (General Discussion, Course Help, Technical Support, Off-Topic)
        if (!Schema::hasTable('forum_categories')) {
            Schema::create('forum_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->string('color')->default('#007fc9');
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Forum Threads
        if (!Schema::hasTable('forum_threads')) {
            Schema::create('forum_threads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('forum_categories')->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->string('slug');
                $table->text('body');
                $table->boolean('is_pinned')->default(false);
                $table->boolean('is_locked')->default(false);
                $table->boolean('is_resolved')->default(false);
                $table->integer('views_count')->default(0);
                $table->integer('replies_count')->default(0);
                $table->timestamp('last_reply_at')->nullable();
                $table->foreignId('last_reply_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['category_id', 'is_pinned', 'created_at']);
                $table->index(['user_id', 'created_at']);
            });
        }

        // Forum Posts (Replies)
        if (!Schema::hasTable('forum_posts')) {
            Schema::create('forum_posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('thread_id')->constrained('forum_threads')->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('parent_id')->nullable()->constrained('forum_posts')->nullOnDelete();
                $table->text('body');
                $table->boolean('is_best_answer')->default(false);
                $table->integer('upvotes_count')->default(0);
                $table->integer('downvotes_count')->default(0);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['thread_id', 'created_at']);
                $table->index(['user_id', 'created_at']);
            });
        }

        // Forum Post Votes
        if (!Schema::hasTable('forum_post_votes')) {
            Schema::create('forum_post_votes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained('forum_posts')->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->enum('vote_type', ['up', 'down']);
                $table->timestamps();

                $table->unique(['post_id', 'user_id']);
            });
        }

        // Forum Thread Subscriptions
        if (!Schema::hasTable('forum_thread_subscriptions')) {
            Schema::create('forum_thread_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('thread_id')->constrained('forum_threads')->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->timestamps();

                $table->unique(['thread_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_thread_subscriptions');
        Schema::dropIfExists('forum_post_votes');
        Schema::dropIfExists('forum_posts');
        Schema::dropIfExists('forum_threads');
        Schema::dropIfExists('forum_categories');
    }
};
