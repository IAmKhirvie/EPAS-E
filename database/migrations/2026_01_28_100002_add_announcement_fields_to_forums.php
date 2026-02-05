<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add announcement-specific fields to forum_categories
        if (Schema::hasTable('forum_categories')) {
            if (!Schema::hasColumn('forum_categories', 'is_announcement_category')) {
                Schema::table('forum_categories', function (Blueprint $table) {
                    $table->boolean('is_announcement_category')->default(false)->after('is_active');
                });
            }
            if (!Schema::hasColumn('forum_categories', 'admin_only_post')) {
                Schema::table('forum_categories', function (Blueprint $table) {
                    $table->boolean('admin_only_post')->default(false)->after('is_announcement_category');
                });
            }
        }

        // Add announcement-specific fields to forum_threads
        if (Schema::hasTable('forum_threads')) {
            Schema::table('forum_threads', function (Blueprint $table) {
                if (!Schema::hasColumn('forum_threads', 'is_announcement')) {
                    $table->boolean('is_announcement')->default(false)->after('is_resolved');
                }
                if (!Schema::hasColumn('forum_threads', 'is_urgent')) {
                    $table->boolean('is_urgent')->default(false)->after('is_announcement');
                }
                if (!Schema::hasColumn('forum_threads', 'target_roles')) {
                    $table->string('target_roles')->default('all')->after('is_urgent');
                }
                if (!Schema::hasColumn('forum_threads', 'deadline')) {
                    $table->timestamp('deadline')->nullable()->after('target_roles');
                }
                if (!Schema::hasColumn('forum_threads', 'publish_at')) {
                    $table->timestamp('publish_at')->nullable()->after('deadline');
                }
            });
        }

        // Add read tracking for forum threads
        if (!Schema::hasTable('forum_thread_reads')) {
            Schema::create('forum_thread_reads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('thread_id')->constrained('forum_threads')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->unique(['thread_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('forum_categories')) {
            Schema::table('forum_categories', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('forum_categories', 'is_announcement_category')) {
                    $cols[] = 'is_announcement_category';
                }
                if (Schema::hasColumn('forum_categories', 'admin_only_post')) {
                    $cols[] = 'admin_only_post';
                }
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        if (Schema::hasTable('forum_threads')) {
            Schema::table('forum_threads', function (Blueprint $table) {
                $columns = ['is_announcement', 'is_urgent', 'target_roles', 'deadline', 'publish_at'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('forum_threads', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        Schema::dropIfExists('forum_thread_reads');
    }
};
