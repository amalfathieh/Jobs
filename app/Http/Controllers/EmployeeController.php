<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Jobs\InviteEmployeeJob;
use App\Models\Employee;
use App\Models\User;
use App\services\FileService;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    use ResponseTrait;
    //ADD EMPLOYEE BY ADMIN FROM DASHBOURD
    public function addEmployee(EmployeeRequest $request) {
        $data = $request->all();
        $roles = ['employee'];

        foreach ($data['roles_name'] as $role){
            $roles[] = $role;
        }

        $password = Str::random(8);
        $user = User::query()->create([
            'user_name' => $request['first_name'],
            'email' => $request['email'],
            'password' => bcrypt($password),
            'roles_name' => $roles,
        ]);

        Employee::create([
            'user_id'=> $user->id,
            'first_name'=> $request['first_name'],
            'middle_name'=> $request['middle_name'],
            'last_name'=> $request['last_name'],
            'gender'=> $request['gender'],
        ]);
        $link='';
        $user->assignRole($roles);
        InviteEmployeeJob::dispatch($request->email, $password ,$link);
        return $this->apiResponse($user,'Employee has been invite successfully',201);
    }

    //EDIT EMPLOYEE
    public function editEmployee(Request $request , FileService $fileService){
        $employee_image = null;
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();
        $old_file = $employee['image'];
        if ($request->hasFile('image') && $request->image != '') {
            $employee_image = $fileService->update($request->image, $old_file ,'employees');
        }
        $user->update([
            'user_name' =>$request['user_name'] ?? $employee['user_name'],
            'email' =>$request['email'] ?? $employee['email'],
            'password' =>$request['password'] ?? $employee['password'],
        ]);
        $employee->update([
            'phone' =>$request['phone'] ?? $employee['phone'],
            'image' =>$employee_image ?? $employee['image'],
        ]);
        return $this->apiResponse($employee , 'success' , 201);
    }

    //
    public function delete(){

    }
}
