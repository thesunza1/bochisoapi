<?php

namespace App\Http\Controllers;

use App\Models\BscTargets;
use App\Models\BscTopics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BscTargetsController extends Controller
{
    public function index(Request $request)
    {
        $targets = BscTargets::where('id', '>', 0)->orderByDesc('id')->with('createdUser', 'topic')->get();
        return response()->json([
            'statuscode' => 1,
            'targets' => $targets
        ]);
    }
    public function getWithArrTopic(Request $request)
    {
        $topicStr = $request->topics;
        $topicArr = explode(',', $topicStr);
        $topics = BscTopics::select('*')->whereIn('id', $topicArr)->with('targets.targets')->get()->toArray();
        // $topics = BscTopics::whereIn('id',$topicArr)->with(['targets' => function($query) {
        //     $query->select('id', 'name' , 'order', 'active');
        //     $query->with('targets');
        // }])->get()->toArray();
        return response()->json([
            'statuscode' => 1,
            'topics' => $topics
        ]);
    }
    public function getWithTopic(Request $request)
    {
        $type = $request->type; // 1 get tiêu chí cha. 2 get tiêu chí con và cha.
        $topicId = $request->topic_id;
        if ($type == 1) {

            $topic = BscTopics::find($topicId)->with('targets')->get();
        } else {

            $topic = BscTopics::find($topicId)->with('targets.targets')->get();
        }
        return  response()->json([
            'statuscode' => 1,
            'topic' => $topic
        ], 200);
    }
    public function createWithTopic(Request $request)
    {
        $target = [
            'name' => $request->name,
            'order' => $request->order,
            'comment' => $request->comment,
            'username_created' => $request->user()->username,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $topic = BscTopics::find($request->topic_id);
        $nTarget = $topic->targets()->create($target);

        return response()->json([
            'statuscode' => 1,
            'target' => $nTarget,
        ]);
    }
    public function createWithThis(Request $request)
    {
        $target = [
            'name' => $request->name,
            'order' => $request->order,
            'comment' => $request->comment,
            'username_created' => $request->user()->username,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $pTarget = BscTargets::find($request->target_id);

        $nTarget = $pTarget->targets()->create($target);

        return response()->json([
            'statuscode' => 1,
            'target' => $nTarget,
        ]);
    }

    public function update(Request $request)
    {
        $target = [
            'name' => $request->name,
            'order' => $request->order,
            'comment' => $request->comment,
            'username_updated' => $request->user()->username,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $nowTarget = BscTargets::find($request->target_id);
        $nowTarget->update($target);
        return response()->json([
            'statuscode' => 1,
            'target' => $nowTarget,
        ]);
    }
}
