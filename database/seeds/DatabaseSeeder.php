<?php

use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class)->create([
            'name' => 'SomeOne',
            'email' => 'someOne@gmail.com',
        ]);

        factory(Post::class,1000)->create();
        // $this->call(UserSeeder::class);
    }
}
