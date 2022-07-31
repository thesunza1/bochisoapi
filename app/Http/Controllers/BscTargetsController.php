<?php

namespace App\Http\Controllers;

use App\Models\BscSetIndicators;
use App\Models\BscTargets;
use App\Models\BscTopics;
use App\Models\BscUnits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

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
            $str = 'targets';
        } else {
            $str = 'targets.targets';
        }
        $topic = BscTopics::where('id', $topicId)->with($str)->get();
        return  response()->json([
            'statuscode' => 1,
            'topic' => $topic
        ], 200);
    }

    public function create(Request $request)
    {
        $parentId =  $request->parent_id == -1 ? null : $request->parent_id;
        $name = $request->name;
        $comment = $request->comment;
        $topicId = $request->topic_id;
        //get id target first
        $targetIdFirst = BscTargets::where('topic_id', $topicId)->first()->id;
        //create new target
        $target = BscTargets::create([
            'name' => $name,
            'comment' => $comment,
            'target_id' => $parentId,
        ]);
        // find array unit_id use topic use target id
        $year = now()->year;
        $year_set = new Carbon('01-01-' . $year);
        $year_set->format('d-M-y');

        $unitIdArr = BscSetIndicators::select('id')->distinct()
            ->whereDate('year_set', $year)
            ->whereNull('month_set')
            ->where('target_id', $targetIdFirst)
            ->pluck('id');

        foreach ($unitIdArr as $unitId) {
            for ($i = 0; $i <= 12; $i++) {
                BscTargetsController::addToMonth($i, $target->id, $unitId, $year, $parentId);
            }
        }

        return response()->json(['statuscode' => 1]);
    }

    public static function  addToMonth($month, $targetId, $unitId, $year, $parentId)
    {
        $monthSet = null;
        if ($month > 0) {
            $monthSet = new Carbon('01-' . $month . '-' . $year);
            $monthSet->format('d-M-y');
        }
        $parentIdSetIndicator = null;
        $yearSet = new Carbon('01-01-' . $year);
        $yearSet->format('d-M-y');
        if ($parentId !== null) {
            $parentIdSetIndicator = BscSetIndicators::whereDate('year_set', $yearSet)
                ->where('unit_id', $unitId)
                ->where('target_id', $targetId);
            if ($monthSet == null) $parentIdSetIndicator->whereNull('month_set');
            else $parentIdSetIndicator->whereDate('month_set', $monthSet);
            $parentIdSetIndicator = $parentIdSetIndicator->first()->id;
        }
        $data = [
            'month_set' => $monthSet,
            'year_set' => $yearSet,
            'unit_id' => $unitId,
            'target_id' => $targetId,
            'set_indicator_id' => $parentIdSetIndicator
        ];
        BscSetIndicators::insert($data);
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
