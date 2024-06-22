<?php

namespace App\Http\Controllers;

use App\Http\Resources\OpportunityResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Apply;
use App\Models\Opportunity;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use App\Notifications\SendNotification;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

use function Symfony\Component\String\u;

class NotificationController extends Controller
{
    use responseTrait;
    //
    public function displayNotification()
    {
        try {
            $data = [];
            $notifications = DB::table('notifications')
                ->where('notifiable_id',Auth::user()->id)->latest()->get();
            foreach ($notifications as $notification) {
                $notificationData = json_decode($notification->data);
                $data [] = [
                    'obj_id' => $notificationData->obj_id,
                    'title' => $notificationData->title,
                    'body' => $notificationData->body,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                ];
            }
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
        return $this->apiResponse($data, "successfully", 200);
    }

    public function getNotificationContent(Request $request){
        $request->validate([
            'obj_id'=>'required|integer',
            'title'=>'required',
            ]);
        if($request['title'] == 'New Post'){
            $post = Post::find($request->obj_id);

            if($post){
                $notifications_id=DB::table('notifications')->where('notifiable_id',Auth::user()->id)->where('data->obj_id',$request->obj_id)->pluck('id');
                DB::table('notifications')->where('id',$notifications_id)->update(["read_at"=>now()]);
                return $this->apiResponse(new PostResource($post) , 'success' ,200);
            }

        }
        //فوصة عمل
        else if($request['title'] == 'Job Opportunity'){
            $opportunity = Opportunity::find($request->obj_id);

            if($opportunity) {
                $getId = DB::table('notifications')->where('notifiable_id', Auth::user()->id)->where('data->obj_id', $request->obj_id)->pluck('id');
                DB::table('notifications')->where('id', $getId)->update(['read_at' => now()]);
                return $this->apiResponse(new OpportunityResource($opportunity) , 'success' ,200);
            }
        }
        //طلب توظيف
        else if($request['title'] == 'Job Application'){
            $job_application = Apply::find($request->obj_id);
            if($job_application) {
                $getId = DB::table('notifications')->where('notifiable_id', Auth::user()->id)->where('data->obj_id', $request->obj_id)->pluck('id');
                DB::table('notifications')->where('id', $getId)->update(['read_at' => now()]);
                return $this->apiResponse($job_application , 'success' ,200);
            }
        }
        //تنبيه الدخول الى الداشبورد
        else if($request['title'] == 'Login Alert'){
            $user = User::find($request->obj_id);
            if($user) {
                $getId = DB::table('notifications')->where('notifiable_id', Auth::user()->id)->where('data->obj_id', $request->obj_id)->pluck('id');
                DB::table('notifications')->where('id', $getId)->update(['read_at' => now()]);
                return $this->apiResponse(new UserResource($user) , 'success' ,200);
            }
        }

        if($request['title'] == 'Report'){
            $report = Report::find($request->obj_id);
            if($report) {
                $getId = DB::table('notifications')->where('notifiable_id', Auth::user()->id)->where('data->obj_id', $request->obj_id)->pluck('id');
                DB::table('notifications')->where('id', $getId)->update(['read_at' => now()]);
                return $this->apiResponse($report , 'success' ,200);
            }
        }

        return $this->apiResponse(null , __('strings.not found notification') ,404);

    }

    //delete all notification
    public function delete(){
        $user = User::find(Auth::user()->id);
        //get unRead notification    or read
        //$user->unreadNotifications || readNotifications;
        foreach($user->notifications as $notification){
            $notification->delete();
        }
        return $this->apiResponse(null,'success',200);
    }

    public function makeAsRead(){
        $userid=User::find(auth()->user()->id);
        foreach($userid->unreadNotifications as $notification) {
            $notification->markAsRead();
        }
        return $this->apiResponse(null,'success',200);
    }

    public function testStore(){
        try {
            $users=User::where('id','!=',auth()->user()->id)->get();
            $user2 = User::find(1);
            $noti =[
                'obj_id'=>Auth::user()->id,
                'title'=>'Login',
                'body'=>'to22 notification',
            ];

            Notification::send($users,new SendNotification($noti));

            $notifications = DB::table('notifications')
                ->where('notifiable_id',Auth::user()->id)->get();

            $data = [];
            foreach ($notifications as $notification){
                $notificationData = json_decode($notification->data);
                $data [] = [
                    'obj_id' => $notificationData->obj_id,
                    'title' => $notificationData->title,
                    'body' => $notificationData->body,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                ];
            }
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
        return $this->apiResponse($data, "successfully", 200);
    }

}
