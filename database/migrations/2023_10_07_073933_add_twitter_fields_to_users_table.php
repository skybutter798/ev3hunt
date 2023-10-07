<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwitterFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nickname')) {
                $table->string('nickname')->nullable();
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable();
            }
            if (!Schema::hasColumn('users', 'profile_banner_url')) {
                $table->string('profile_banner_url')->nullable();
            }
            if (!Schema::hasColumn('users', 'location')) {
                $table->string('location')->nullable();
            }
            if (!Schema::hasColumn('users', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('users', 'followers_count')) {
                $table->integer('followers_count')->default(0);
            }
            if (!Schema::hasColumn('users', 'friends_count')) {
                $table->integer('friends_count')->default(0);
            }
            if (!Schema::hasColumn('users', 'statuses_count')) {
                $table->integer('statuses_count')->default(0);
            }
            if (!Schema::hasColumn('users', 'created_at_twitter')) {
                $table->dateTime('created_at_twitter')->nullable();
            }
            // ... add other fields as needed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'nickname')) {
                $table->dropColumn('nickname');
            }
            if (Schema::hasColumn('users', 'avatar')) {
                $table->dropColumn('avatar');
            }
            if (Schema::hasColumn('users', 'profile_banner_url')) {
                $table->dropColumn('profile_banner_url');
            }
            if (Schema::hasColumn('users', 'location')) {
                $table->dropColumn('location');
            }
            if (Schema::hasColumn('users', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('users', 'followers_count')) {
                $table->dropColumn('followers_count');
            }
            if (Schema::hasColumn('users', 'friends_count')) {
                $table->dropColumn('friends_count');
            }
            if (Schema::hasColumn('users', 'statuses_count')) {
                $table->dropColumn('statuses_count');
            }
            if (Schema::hasColumn('users', 'created_at_twitter')) {
                $table->dropColumn('created_at_twitter');
            }
            // ... repeat for other columns
        });
    }
}
