<?php

namespace App\Http\Controllers;

use App\Models\Bsc;
use App\Models\BscDetailSetIndicators;
use App\Models\BscSetIndicators;
use App\Models\BscTopicOrders;
use App\Models\BscTopics;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BscDetailSetIndicatorsController extends Controller
{
    //
    public function index(Request $request)
    {
        $month = new Carbon('2022/06/23');
        $year = new Carbon('2022/06/01');
        $mt = $month->format('d-M-y');
        $yt = $year->format('d-M-y');

        $now = new Carbon(Carbon::now()->toDateString());
        $n = $now->format('d-M-y');
        $a = 1;
        $detailSetIndicators = BscSetIndicators::where('unit_id', 1)->whereDate('month_set', $month)
            ->whereDate('year_set', $year)->first()->detailSetIndicators()->pluck('id');
        $detailSetIndicators = BscDetailSetIndicators::whereIn('id', $detailSetIndicators)->whereDate('created_at', $now)->pluck('id');
        if ($detailSetIndicators->count() == 0) {
            $this->create($request);
            $a = 33;
        }
        $arrSetIndicatorid = BscSetIndicators::where('unit_id', 1)->whereDate('month_set', $month->toDateString())->whereDate('year_set', $year->toDateString())->pluck('id');
        //get arr topic_id from arr set_indicator.
        $arrTopicId =  BscTopicOrders::select('topic_id')->whereIn('set_indicator_id', $arrSetIndicatorid)->distinct()->pluck('topic_id');
        //get topic from topic_id array -> with all chitieu.
        $topics = BscTopics::select('id', 'name')->whereIn('id', $arrTopicId)->with([
            'targets.setindicators' => function ($query) use ($request, $year,  $month, $now) {
                $query->select('id', 'set_indicator_id', 'target_id', 'active', 'total_plan', 'plan')->where('unit_id', 1)->whereDate('year_set', $year->toDateString())->whereDate('month_set', $month->toDateString());
                $query->with(['detailSetIndicators' => function ($query) use ($now) {
                    $query->whereDate('created_at', $now);
                }]);
            }
        ])->with([
            'targets.targets.setindicators' => function ($query) use ($request, $year, $month, $now) {
                $query->select('id', 'set_indicator_id', 'target_id', 'active', 'total_plan', 'plan')->where('unit_id', 1)->whereDate('year_set', $year->toDateString())->whereDate('month_set', $month->toDateString());
                $query->with(['detailSetIndicators' => function ($query) use ($now) {
                    $query->whereDate('created_at', $now);
                }]);
            }
        ])->get();
        return response()->json([
            'statuscode' => 1,
            'detailSetIndicators' => $detailSetIndicators,
            'topics' => $topics,
        ]);
    }
    public function create(Request $request)
    {
        $month = new Carbon('2022/06/23');
        $year = new Carbon('2022/06/01');
        $mt = $month->format('d-M-y');
        $yt = $year->format('d-M-y');
        $now = now();
        $username = $request->user()->username;
        DB::transaction(function () use ($month, $year, $now, $username) {
            $setIndicators = BscSetIndicators::where('unit_id', 1)->whereDate('month_set', $month->toDateString())->whereDate('year_set', $year->toDateString())->get();
            foreach ($setIndicators as $setIndicator) {
                $setIndicator->detailsetindicators()->create([
                    'username_created' => $username,
                    'total_plan' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });
    }

    public function update(Request $request)
    {

        $id = $request->id;
        $username = $request->user()->username;
        $totalPlan = $request->total_plan;
        //update row detail detailSetIndicator
        $detailSetIndicator = BscDetailSetIndicators::find($id);
        $detailSetIndicator->total_plan = $totalPlan;
        $detailSetIndicator->username_updated = $username;
        $detailSetIndicator->save();
        //update value for
        $setIndicator = $detailSetIndicator->setIndicator;
        $yearSetSI = date('d-M-y', $setIndicator->year_set);
        $monthSetSI = date('d-M-y', $setIndicator->month_set);

        $month = new Carbon($monthSetSI);
        $year = new Carbon($yearSetSI);
        $mt = $month->format('d-M-y');
        $yr = $year->format('d-M-y');

        $unitIdSI = $setIndicator->unit_id;
        $setIndicators = BscSetIndicators::where('unit_id', $unitIdSI)->whereDate('year_set', $year)->whereDate('month_set', $month)->get();
        $detailSetIndicators = DB::transaction(function () use ($setIndicators) {
            foreach ($setIndicators as $setIndicator) {
                $plan = 0;
                $detailSetIndicators = $setIndicator->detailSetIndicators;
                foreach ($detailSetIndicators as $detailSetIndicator) {
                    $plan += $detailSetIndicator->total_plan;
                }
                $setIndicator->total_plan = $plan;
                $setIndicator->save();
            }
        });


        return response()->json([
            'statuscode' => 1,
            'detailSetIndicators' => $detailSetIndicators
        ]);
    }
}
