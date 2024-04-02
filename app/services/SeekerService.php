<?php


namespace App\services;


use App\Http\Controllers\responseTrait;
use App\Http\Controllers\UserController;
use App\Models\Seeker;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use function Laravel\Prompts\search;

class SeekerService
{
    use responseTrait;
    public function createSeeker(
        $first_name,
        $last_name,
        $birth_day,
        $location,
        $image,
        $skills,
        $certificates,
        $about) {

            $user_controller = new UserController();
            $seeker_image = $user_controller->storeImage($image);

        Seeker::create([
            'user_id' => Auth::user()->id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'birth_day' => $birth_day,
            'location' => $location,
            'image' => $seeker_image,
            'skills' => $skills,
            'certificates' => $certificates,
            'about' => $about
        ]);

    }
}
