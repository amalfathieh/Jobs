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
        $location,
        $image,
        $skills,
        $certificates,
        $about) {
        $seeker_image = $this->fileService->store($image,'job_seeker');
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
    public function update( $request ){
        $seeker_image = null;
            $id = Auth::user()->id;
            $seeker = Seeker::where('user_id', $id)->first();
        $old_file = $seeker['image'];
        if ($request->hasFile('image') && $request->image != '') {
            $seeker_image = $this->fileService->update($request->image, $old_file ,'job_seeker');
        }
        $seeker->update([
            'first_name' =>$request['first_name'] ?? $seeker['first_name'],
            'last_name' =>$request['last_name'] ?? $seeker['last_name'],
            'birth_day' =>$request['birth_day'] ?? $seeker['birth_day'],
            'location' =>$request['location'] ?? $seeker['location'],
            'image' =>$seeker_image ?? $seeker['image'],
            'skills' =>$request['skills'] ?? $seeker['skills'],
            'certificates'=>$request['certificates'] ?? $seeker['certificates'],
            'about' =>$request['about'] ?? $seeker['about']
        ]);

    }
}
