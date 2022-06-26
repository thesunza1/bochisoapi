<?php

namespace App\Http\Controllers;

use App\Models\BscSetIndicators;
use App\Models\BscTargets;
use App\Models\BscTopicOrders;
use App\Models\BscTopics;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BscSetIndicatorsController extends Controller
{
    //year, month, topic[(active, stt)]
    public function create(Request $request)
    {
        //get id,  stt;
        $arrTopic = [];
        $arrTopicArr = [];
        $objTopics = $request->topics;
        foreach ($objTopics as  $topic) {
            array_push($arrTopic, [""]);
        }
        //create bcs with name , month, unit, year
        //add bcs with bcs with id taget,
    }
    public function fastCreate(Request $request)
    {
        $topic_id = 1;
        $username = 'venlm.hgi';
        $unit_id =  1;
        $thang = new Carbon('2022-06-23');
        $nam = new Carbon('2022-06-23');

        $order = [1];
        DB::transaction(function () use ($topic_id, $username, $unit_id, $thang, $nam, $order) {
            //topic width tagert
            $targets = BscTargets::select('id')->where('topic_id', $topic_id)->get();
            foreach ($targets as $target) {
                $dtSetIndicator = [
                    "username_created" => $username,
                    "month_set" => $thang,
                    "year_set" => $nam,
                    'unit_id' => $unit_id,
                    'plan' => 5000,
                    'target_id' => $target->id,
                ];
                $setIndicator = BscSetIndicators::create($dtSetIndicator);

                BscTopicOrders::create([
                    'name' => $order[0],
                    'set_indicator_id' => $setIndicator->id,
                    'topic_id' => $topic_id
                ]);

                foreach ($target->targets as $childTarget) {
                    $dtSetIndicator = [
                        "username_created" => $username,
                        "month_set" => $thang,
                        "year_set" => $nam,
                        'unit_id' => $unit_id,
                        'plan' => 5000,
                        'target_id' => $childTarget->id,
                    ];
                    $clild = $setIndicator->setIndicators()->create($dtSetIndicator);
                    BscTopicOrders::create([
                        'name' => $order[0],
                        'set_indicator_id' => $clild->id,
                        'topic_id' => $topic_id,
                    ]);
                }
            }
        });

        return response()->json([
            'statuscode' => 1,
        ]);
    }
    //thang, năm , unit_id
    public function index(Request $request)
    {
        //get arr set_indicator
        $month = new Carbon('2022/06/23');
        $year = new Carbon('2022/06/01');
        $mt = $month->format('d-M-y');
        $yt = $year->format('d-M-y');
        $arrSetIndicatorid = BscSetIndicators::where('unit_id', 1)->whereDate('month_set', $month->toDateString())->whereDate('year_set', $year->toDateString())->pluck('id');
        // $arrSetIndicatorid = BscSetIndicators::where('unit_id', 1)->where('year_set', $month)->where('month_set', $month)->pluck('id');
        //get arr topic_id from arr set_indicator.
        $arrTopicId =  BscTopicOrders::select('topic_id')->whereIn('set_indicator_id', $arrSetIndicatorid)->distinct()->pluck('topic_id');
        //get topic from topic_id array -> with all chitieu.
        $topics = BscTopics::select('id', 'name')->whereIn('id', $arrTopicId)->with([
            'targets' => function ($query) {
                $query->select('id', 'target_id', 'topic_id', 'order');
            }
        ])->with([
            'targets.setindicators' => function ($query) use ($request, $year,  $month) {
                $query->select('id', 'set_indicator_id', 'target_id', 'active', 'total_plan', 'plan')->where('unit_id', 1)->whereDate('year_set', $year->toDateString())->whereDate('month_set', $month->toDateString());
            }
        ])->with([
            'targets.targets.setindicators' => function ($query) use ($request, $year, $month) {
                $query->select('id', 'set_indicator_id', 'target_id', 'active', 'total_plan', 'plan')->where('unit_id', 1)->whereDate('year_set', $year->toDateString())->whereDate('month_set', $month->toDateString());
            }
        ])->get();

        return response()->json([
            'statuscode' => 1,
            'mt' => $mt,
            'setin' => $arrSetIndicatorid,
            'topicarr' => $arrTopicId,
            'topics' => $topics
        ]);
    }
}
