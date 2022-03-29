<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $faker = Factory::create();
        $wordsLengthMin = 5;
        $wordsLengthMax = 30;

        \DB::table('comments')->insert([
            [
                'id' => 1,
                'author_name' => 'Admin',
                'text' => $faker->sentence(rand($wordsLengthMin, $wordsLengthMax)),
                'parent_id' => null,
            ],
            [
                'id' => 2,
                'author_name' => $faker->name,
                'text' => $faker->sentence(rand($wordsLengthMin, $wordsLengthMax)),
                'parent_id' => 1,
            ],
            [
                'id' => 3,
                'author_name' => $faker->name,
                'text' => $faker->sentence(rand($wordsLengthMin, $wordsLengthMax)),
                'parent_id' => 2,
            ],
            [
                'id' => 11,
                'author_name' => null,
                'text' => 'Hello friend __ 1',
                'parent_id' => 2,
            ],
            [
                'id' => 4,
                'author_name' => null,
                'text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequuntur itaque alias voluptatum architecto fugiat, cum asperiores quae iure reprehenderit quibusdam! Eaque non molestias ullam nisi esse, aut quae ea ut, ab quo ipsa dignissimos amet nostrum hic quam odio perspiciatis harum eligendi dolorem adipisci velit veniam earum. Obcaecati amet tenetur nostrum necessitatibus veritatis sequi doloribus quo aut vel est illum dolorum omnis quod corporis excepturi quia optio quaerat distinctio non, delectus repudiandae impedit voluptatibus minus? Sed possimus natus optio quis iure temporibus velit repellendus libero fuga? Voluptates quam ullam excepturi et provident labore, iste quae nam, quasi quas maiores deleniti.',
                'parent_id' => 3,
            ],
            [
                'id' => 5,
                'author_name' => $faker->name,
                'text' => $faker->sentence(rand($wordsLengthMin, $wordsLengthMax)),
                'parent_id' => null,
            ],
            [
                'id' => 6,
                'author_name' => null,
                'text' => 'Lorem ipsum dolor sit amet.',
                'parent_id' => 5,
            ],
            [
                'id' => 10,
                'author_name' => null,
                'text' => 'Last one',
                'parent_id' => null,
            ],
        ]);
    }
}
