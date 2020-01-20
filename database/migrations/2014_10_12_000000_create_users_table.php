<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('avatar_id')->default(0);
            $table->string('system_name')->nullable()->index();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 60);
            $table->rememberToken();
            $table->timestamps();
        });

        // Create the initial admin user
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@cmps.jp',
            'password' => bcrypt('cmps@1192'),
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString()
        ]);

        // Insert our new public system user.
        $publicUserId = DB::table('users')->insertGetId([
            'email' => 'guest@example.com',
            'name' => 'Guest',
            'password' => bcrypt('cmps@1192'),
            'system_name' => 'public',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
