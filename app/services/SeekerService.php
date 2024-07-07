<?php


namespace App\services;


use App\Http\Controllers\UserController;
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
        $id = Auth::user()->id;
        $seeker = Seeker::where('user_id', $id)->first();
        return $seeker;
        $old_file = $seeker->image;
        if ($request->hasFile('image') && $request->image != '') {
            $seeker_image = $this->fileService->update($request->image, $old_file ,'images/job_seeker/profilePhoto');
        }
        $skills = json_decode($request['skills']);
        $certificates = json_decode($request['certificates']);
        $seeker->update([
            'first_name' =>$request['first_name'] ?? $seeker['first_name'],
            'last_name' =>$request['last_name'] ?? $seeker['last_name'],
            'birth_day' =>$request['birth_day'] ?? $seeker['birth_day'],
            'location' =>$request['location'] ?? $seeker['location'],
            'image' =>$seeker_image ?? $seeker['image'],
<<<<<<< HEAD
            'skills' =>$skills ?? $seeker['skills'],
            'certificates'=>$certificates ?? $seeker['certificates'],
=======
            'skills' =>$request['skills'] ?? $seeker['skills'],
            'certificates'=>$request['certificates'] ?? $seeker['certificates'],
            'specialization'=>$request['specialization'] ?? $seeker['specialization'],
>>>>>>> 42cf080c5755eb3df5b0b135efbb4f7ecbc008c1
            'about' =>$request['about'] ?? $seeker['about'],
            'gender' =>$request['gender'] ?? $seeker['gender']
        ]);
    }
}
