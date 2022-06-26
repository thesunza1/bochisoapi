<?php

namespace App\Http\Controllers;

use App\Models\BscTopics;
use App\Models\User;
use Illuminate\Http\Request;

class BscTopicsController extends Controller
{
    //
    public function index()
    {
        $topics = BscTopics::where('id','>',0)->orderByDesc('id')->with('createdUser')->get();
        return response([
            'statuscode' => 1 ,
            'topics' => $topics,
        ]);
    }
    public function create(Request $request)
    {
        $topic = [
            'name' => $request->name,
            'created_at' => now(),
            'updated_at' => now()
        ];
        $user = User::find($request->user()->username);
        $nTopic = $user->createdTopics()->create($topic);
        return response()->json(
            [
                'statuscode' => 1,
                'topic' => $nTopic
            ]
        );
    }
    public function update(Request $request)
    {
        $topic = BscTopics::find($request->id);
        $topic->name = $request->name;
        $topic->username_updated = $request->user()->username;
        $topic->save();
        return response()->json(
            [
                'statuscode' => 1,
                'topic' => $topic
            ]
        );

    }
}
