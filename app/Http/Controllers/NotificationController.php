<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use App\Models\Post;
use App\Models\User;
use App\Notifications\RealTimeNotification;
use App\Traits\responseTrait;
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
            $users=User::where('id','!=',auth()->user()->id)->get();
            $user2 = User::find(1);
            $noti =[
                'obj_id'=>Auth::user()->id,
                'title'=>'Login',
                'body'=>'to22 notification',
            ];

            $ss = Notification::send($users,new RealTimeNotification($noti));

            $notifications = DB::table('notifications')
            ->where('notifiable_id',Auth::user()->id)->get();

            $data = [];
            foreach ($notifications as $notification){
                $notificationData = json_decode($notification->data);
                $data[] = $notificationData;
            }
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
        return $this->apiResponse($data, "successfully", 200);
    }

    public function getNotificationContent($id,$title){
        if($title == 'Post'){
            $post = Post::find($id);

            if($post){
                $notifications_id=DB::table('notifications')->where('notifiable_id',Auth::user()->id)->where('data->obj_id', $id)->where('data->obj_id',$id)->pluck('id');
                DB::table('notifications')->where('id',$notifications_id)->update(["read_at"=>now()]);
                return $post;
            }

        }
        else if($title == 'Job Opportunity'){
            $opportunity = Opportunity::find($id);

            if($opportunity) {
                $getId = DB::table('notifications')->where('notifiable_id', Auth::user()->id)->where('data->obj_id', $id)->where('data->obj_id', $id)->pluck('id');
                DB::table('notifications')->where('id', $getId)->update(['read_at' => now()]);
                return $opportunity;
            }
        }

        else if($title == 'Job Application'){
            $job_application = Job_Application::find($id);
            if($job_application) {
                $getId = DB::table('notifications')->where('notifiable_id', Auth::user()->id)->where('data->obj_id', $id)->where('data->obj_id', $id)->pluck('id');
                DB::table('notifications')->where('id', $getId)->update(['read_at' => now()]);
                return $job_application;
            }
        }

        else if($title == 'Login'){
            $user = User::find($id);
            if($user) {
                $getId = DB::table('notifications')->where('notifiable_id', Auth::user()->id)->where('data->obj_id', $id)->where('data->obj_id', $id)->pluck('id');
                DB::table('notifications')->where('id', $getId)->update(['read_at' => now()]);
                return $user;
            }
        }

        if($title == 'Report'){
            $report = Report::find($id);
            if($report) {
                $getId = DB::table('notifications')->where('notifiable_id', Auth::user()->id)->where('data->title',$title)->where('data->obj_id', $id)->pluck('id');
                DB::table('notifications')->where('id', $getId)->update(['read_at' => now()]);
                return $report;
            }
        }

        $message ="Sorry, the content associated with this notification is no longer available.";
//        $message ="عذراً، المحتوى المرتبط بهذا الاشعار لم يعد موجود";

        return $this->apiResponse(null , $message ,404);

    }

    //delete all notification
    public function delete(){
        $user = User::find(Auth::user()->id);
        //get unRead notification    or read
        //$user->unreadNotifications || readNotifications;
        foreach($user->notifications as $notification){
            $notification->delete();
            //جعل جميع الاشعارات مقروئة
            //$notification->markAsRead();
        }
        return $this->apiResponse(null,'success',200);
    }
}
