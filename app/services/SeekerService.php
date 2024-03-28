<?php


namespace App\services;


use App\Http\Controllers\responseTrait;
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
        $about){

        $seeker_image = null;
        if ($image && $image->isValid()) {
            $filenameWithExt = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('seeker/profile'), $filenameWithExt);
            $seeker_image = 'seeker/profile/' . $filenameWithExt;
        }

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
