<?php


namespace App\services;


use App\Models\Seeker;
use Illuminate\Support\Facades\Auth;
use function Laravel\Prompts\search;

class SeekerService
{
    public function createSeeker(
        $first_name,
        $last_name,
        $birth_day,
        $location,
        $image,
        $skills,
        $certificates,
        $about){
        $seeker_image = '';
        if ($image && $image->isValid()) {
            $filenameWithExt = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('seeker/images'), $filenameWithExt);
            $seeker_image = 'seeker/images/' . $filenameWithExt;
        }
        $job_seeker = Seeker::create([
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

        return $job_seeker;
    }
}
