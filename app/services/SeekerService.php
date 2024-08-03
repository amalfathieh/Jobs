<?php


namespace App\services;


use App\Http\Controllers\UserController;
use App\Http\Resources\UserResource;
use App\Models\Seeker;
use App\Models\User;
use App\Traits\responseTrait;
use http\Env\Request;
use Illuminate\Support\Facades\Auth;
use function Laravel\Prompts\search;

class SeekerService
{
    protected $fileService;
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    use responseTrait;
    public function createSeeker(
        $first_name,
        $last_name,
        $birth_day,
        $gender,
        $location,
        $image,
        $skills,
        $certificates,
        $specialization,
        $about) {
        $seeker_image = $this->fileService->store($image,'images/job_seeker/profilePhoto');
        $skills = json_decode($skills);
        $certificates = json_decode($certificates);
        Seeker::create([
            'user_id' => Auth::user()->id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'gender' => $gender,
            'birth_day' => $birth_day,
            'location' => $location,
            'image' => $seeker_image,
            'skills' => $skills,
            'certificates' => $certificates,
            'specialization' => $specialization,
            'about' => $about
        ]);
    }
    public function update($request){
        $seeker_image = null;
        $user = Auth::user();
        $seeker = Seeker::where('user_id', $user->id)->first();
        $old_file = $seeker->image;

        if ($request->hasFile('image') && $request->image != '') {
            $seeker_image = $this->fileService->update($request->image, $old_file ,'images/job_seeker/profilePhoto');
        } else {
            $seeker_image = $old_file;
        }
        $skills = json_decode($request['skills']);
        $certificates = json_decode($request['certificates']);
        $seeker->update([
            'first_name' =>$request['first_name'] ?? $seeker['first_name'],
            'last_name' =>$request['last_name'] ?? $seeker['last_name'],
            'birth_day' =>$request['birth_day'] ?? $seeker['birth_day'],
            'location' =>$request['location'] ?? $seeker['location'],
            'image' => $seeker_image,
            'skills' =>$skills ?? $seeker['skills'],
            'specialization'=>$request['specialization'] ?? $seeker['specialization'],
            'certificates'=>$certificates ?? $seeker['certificates'],
            'about' =>$request['about'] ?? $seeker['about'],
            'gender' =>$request['gender'] ?? $seeker['gender']
        ]);

        return new UserResource($user);
    }
}
