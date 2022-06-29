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
        $unit_id =  21;
        // $thang = new Carbon('2022-06-1');
        $thang = null;

        $nam = new Carbon('2022-01-1');

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
                    'plan' => 75000,
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
                        'plan' => 75000,
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
        // //get arr set_indicator
        // $month = new Carbon('2022/06/01');
        // // $month = new Carbon('2022/06/23');
        // $year = new Carbon('2022/06/01');
        // $mt = $month->format('d-M-y');
        // //get variable in request.
        // $year = $request->year == null  ? new Carbon('2022/06/01') : $request->year;
        // $yt = $year->format('d-M-y');

        $unit_id = $request->unit_id == null ? 21 : $request->unit_id;
        if ($request->month <> 13) {
            $month = $request->month == null ? Carbon::now()->month : $request->month;
            $year = $request->year == null ? Carbon::now()->year : $request->year;
            $monthset = "$year" . '-' . "$month" . '-01';
            $yearset = "$year" . '-' . "$month" . '-01';
            $month = new Carbon($monthset);
            $month->format('d-M-y');
            $year = new Carbon($yearset);
            $year->format('d-M-y');



            $arrSetIndicatorid = BscSetIndicators::where('unit_id', $unit_id)->whereDate('month_set', $month->toDateString())->whereDate('year_set', $year->toDateString())->pluck('id');
            // $arrSetIndicatorid = BscSetIndicators::where('unit_id', 1)->where('year_set', $month)->where('month_set', $month)->pluck('id');
            //get arr topic_id from arr set_indicator.
            $arrTopicId =  BscTopicOrders::select('topic_id')->whereIn('set_indicator_id', $arrSetIndicatorid)->distinct()->pluck('topic_id');
            //get topic from topic_id array -> with all chitieu.
            $topics = BscTopics::select('id', 'name')->whereIn('id', $arrTopicId)->with([
                'targets.setindicators' => function ($query) use ($request, $year,  $month, $unit_id) {
                    $query->select('id', 'set_indicator_id', 'target_id', 'active', 'total_plan', 'plan', 'year_plan', 'plan_warning')
                        ->where('unit_id', $unit_id)
                        ->whereDate('year_set', $year->toDateString())
                        ->whereDate('month_set', $month->toDateString())
                        ->with(['detailSetIndicator.userUpdated']);
                }
            ])->with([
                'targets.targets.setindicators' => function ($query) use ($request, $year, $month, $unit_id) {
                    $query->select('id', 'set_indicator_id', 'target_id', 'active', 'total_plan', 'plan', 'year_plan', 'plan_warning')
                        ->where('unit_id', $unit_id)
                        ->whereDate('year_set', $year->toDateString())
                        ->whereDate('month_set', $month->toDateString())
                        ->with(['detailSetIndicator.userUpdated']);
                }
            ])->get();
        } else {
            $month =  '01';
            $year = $request->year == null ? Carbon::now()->year : $request->year;
            $monthset = null;
            $yearset = "$year" . '-' . "$month" . '-01';
            $year = new Carbon($yearset);
            $year->format('d-M-y');
            $month = null;



            $arrSetIndicatorid = BscSetIndicators::where('unit_id', $unit_id)->whereNull('month_set')->whereDate('year_set', $year->toDateString())->pluck('id');
            // $arrSetIndicatorid = BscSetIndicators::where('unit_id', 1)->where('year_set', $month)->where('month_set', $month)->pluck('id');
            //get arr topic_id from arr set_indicator.
            $arrTopicId =  BscTopicOrders::select('topic_id')->whereIn('set_indicator_id', $arrSetIndicatorid)->distinct()->pluck('topic_id');
            //get topic from topic_id array -> with all chitieu.
            $topics = BscTopics::select('id', 'name')->whereIn('id', $arrTopicId)->with([
                'targets.setindicators' => function ($query) use ($request, $year,  $month, $unit_id) {
                    $query->select('id', 'set_indicator_id', 'target_id', 'active', 'total_plan', 'plan', 'year_plan', 'plan_warning')
                        ->where('unit_id', $unit_id)
                        ->whereDate('year_set', $year->toDateString())
                        ->whereNull('month_set')
                        ->with(['detailSetIndicator.userUpdated']);
                }
            ])->with([
                'targets.targets.setindicators' => function ($query) use ($request, $year, $month, $unit_id) {
                    $query->select('id', 'set_indicator_id', 'target_id', 'active', 'total_plan', 'plan', 'year_plan', 'plan_warning')
                        ->where('unit_id', $unit_id)
                        ->whereDate('year_set', $year->toDateString())
                        ->whereNull('month_set')
                        ->with(['detailSetIndicator.userUpdated']);
                }
            ])->get();
        }



        return response()->json([
            'statuscode' => 1,
            'topics' => $topics
        ]);
    }
}
