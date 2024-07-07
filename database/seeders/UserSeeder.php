<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Opportunity;
use App\Models\Post;
use App\Models\Seeker;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // for ($i=0; $i < 50; $i++) {
        //     $user = User::factory(1)->has(Company::factory(1))->create();
        // }

        $user = User::create([
            'user_name'=> 'bing_ai',
            'email'=> 'bing_ai@gmail.com',
            'password'=>'Aa123123',
            'roles_name'=> ['user', 'company'],
            'is_verified'=> 1,
        ]);
        $user->syncRoles(['user', 'company']);
        $company = Company::create([
            'user_id'=>$user->id,
            'company_name'=>'Bing company',
            'location'=>'USA, California',
            'about' => 'AI Company',
            'contact_info' => 'bing_ai@gmail.com'
        ]);

        for ($i=0; $i < 50; $i++) {
            Opportunity::create([
                'company_id'=> $company->id,
                'title' => fake()->word(),
                'body' => fake()->sentence(),
                'location' => fake()->address(),
                'job_type' => fake()->randomElement(['full_time', 'part_time', 'contract', 'temporary', 'volunteer']),
                'work_place_type' => fake()->randomElement(['on_site', 'hybrid', 'remote']),
                'qualifications' => [fake()->slug(), fake()->slug()],
                'skills_req' => ["Python","Laravel","Arabic","English"],
                'salary' => fake()->numberBetween(500, 10000),
                'vacant' => 1,
                'job_hours' => fake()->numberBetween(8, 18)
            ]);
        }

        $user2 = User::create([
            'user_name'=> 'ali_mohammad',
            'email'=> 'alimohammad@gmail.com',
            'password'=>'Aa123123',
            'roles_name'=> ['user', 'job_seeker'],
            'is_verified'=> 1,
        ]);
        $user2->syncRoles(['user', 'job_seeker']);
        $seeker = Seeker::create([
            'user_id' => $user2->id,
            'first_name' => 'Ali',
            'last_name' => 'Zaitoun',
            'gender' => 'male',
            'birth_day' => '2004-1-1',
            'location' => 'Syria',
            'skills' => ['Python', 'Laravel', 'React JS', 'English'],
            'certificates' => ['IT', 'BLA BLA'],
            'about' => 'Back-end developer'
        ]);
        for ($i=0; $i < 50; $i++) {
            Post::create([
                'seeker_id' => $seeker->id,
                'body' => fake()->text()
            ]);
        }
    }


}
