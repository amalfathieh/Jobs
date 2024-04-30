<?php

namespace App\Http\Controllers;

use App\Events\ChatEvent;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\chat_user_pivot;
use App\Models\Company;
use App\Models\Message;
use App\Models\Seeker;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    //
    public function sendMessage($OtherUserId , $chat_id,Request $request){
        if($chat_id == null ){
            //create chat
            $chat = Chat::create([
                'user1_id'=>Auth::user()->id,
                'user2_id'=>$OtherUserId,
            ]);
            chat_user_pivot::create([
                'chat_id'=>$chat->id,
                'user_id'=>Auth::user()->id
            ]);
        }
        //send message
        $data['chat_id'] = $chat_id == null ? $chat->id:$chat_id;
        $data['sender_id'] = Auth::user()->id;
        $data['message'] =$request->message;
        Message::create($data);
    }


    //GET LIST CHAT ROOMS
    public function allChats(){
        $user = Auth::user();
        $chats=Chat::where('user1_id',$user->id)->orWhere('user2_id',$user->id)->get();
        $chat_list=[] ;
        //find otherUser in chat
        foreach($chats as $chat){
            $otherUser = $chat->user1_id != $user->id ? $chat-> user1_id : $chat-> user2_id;
            $chat_list[$otherUser][] = $chat;
        }
        $result = [] ;

        foreach($chat_list as $otherUser=> $chats )
        {
            foreach ($chats as $chat) {
                $array = [
                    'chat_id'=>$chat->id,
                    'other_user_name'=>null,
                    'image'=>null,
                    'last_message' => $chat->Messages()->latest()->first()->message,//last message
                    'last_message_time'=>$chat->Messages()->latest()->first()->created_at->toDateTimeString(),
                ];
                $user=User::find($otherUser) ;
                if($user->role == 'company'){
                    $com=Company::where('user_id',$otherUser)->first();
                    $array['other_user_name']=$com->company_name;
                    $array['image']=$com->logo;
                }
                else  if($user->role == 'job_seeker'){
                    $se=Seeker::where('user_id',$otherUser)->first();
                    $array['other_user_name']= $se->first_name . ' ' .$se->last_name;
                    $array['image']=$se->image;
                }
                $result[] = $array;
            }
        }
        return $result;
    }

    //GET ALL MESSAGES IN CHAT
    public function shawAllMessages($chat_id){
        $messages = Chat::where('id',$chat_id)->first()->messages;
        return response()->json(MessageResource::collection($messages));
    }

}
