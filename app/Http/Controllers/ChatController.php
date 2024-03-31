<?php

namespace App\Http\Controllers;

//use http\Client\Curl\User;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    //
    public function chats(){
        //return "skn";
        $id = Auth::user()->id;
       // $user = User::with('chats')->where('id',$id)->get();
        $chats = Chat::with('user')->where('user1_id',$id)->orWhere('user2_id',$id)->get();
        return $chats;
    }
    public function first($user_id,Request $request){
        $chat = Chat::create([
            'user1_id'=>Auth::user()->id,
            'user2_id'=>$user_id
        ]);
        Message::create([
            'chat_id'=>$chat->id,
            'sender_id'=>Auth::user()->id,
            'message'=>$request->message,
            'seen'=>false
        ]);
    }
    public function sendMessage($chat_id,$user_id,Request $request){
        Message::create([
            'chat_id'=>$chat_id,
            'sender_id'=>Auth::user()->id,
            'message'=>$request->message,
            'seen'=>false
        ]);
       // return $user_id;
    }
}
