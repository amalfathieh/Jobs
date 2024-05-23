<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditEmployeeRequest;
use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\UserResource;
use App\Jobs\InviteEmployeeJob;
use App\Models\Employee;
use App\Models\User;
use App\services\FileService;
use App\services\UserService;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    use ResponseTrait;
    public $userService;
    public function __construct(UserService $userService){
        $this->userService = $userService;
    }

    //ADD EMPLOYEE BY ADMIN FROM DASHBOURD
    public function add(Request $request) {
        $data = $request->all();
        $roles = ['employee'];

        foreach ($data['roles_name'] as $role){
            $roles[] = $role;
        }

        $password = Str::random(8);
        $user = User::query()->create([
            'user_name' => $request['first_name'] . '_' . Str::random(3),
            'email' => $request['email'],
            'password' => $password,
            'roles_name' => $roles,
            'is_verified'=>true,
        ]);
        $employee = $this->userService->storeEmployee($request , $user->id );

        $link='';
        $user->assignRole($roles);
        InviteEmployeeJob::dispatch($request->email, $password ,$link);
        return $this->apiResponse($user,'Employee has been invite successfully',201);
    }

    //EDIT EMPLOYEE
    public function edit(EditEmployeeRequest $request , FileService $fileService){
        $employee_image = null;
        $user = Auth::user();
        $user = $this->userService->updateUser($request , $user);

        $employee = Employee::where('user_id', $user->id)->first();
        $old_file = $employee->image;
        if ($request->hasFile('image') && $request->image != '') {
            $employee_image = $fileService->update($request->image, $old_file ,'employees');
        }

        $employee->update([
            'phone' =>$request['phone'] ?? $employee['phone'],
            'image' =>$employee_image ?? $employee['image'],
        ]);
        return $this->apiResponse($user->employee , 'success' , 201);
    }


    public function getEmployee(){
        $employees = User::role('employee')->latest()->get();
        return UserResource::collection($employees);
    }

}
