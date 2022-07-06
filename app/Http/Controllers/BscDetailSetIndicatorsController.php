<?php

namespace App\Http\Controllers;

use App\Models\Bsc;
use App\Models\BscDetailSetIndicators;
use App\Models\BscSetIndicators;
use App\Models\BscTopicOrders;
use App\Models\BscTopics;
use App\Models\BscUnits;
use Carbon\Carbon;
use Cron\MonthField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BscDetailSetIndicatorsController extends Controller
{
    //
    public function index(Request $request)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $month = now()->firstOfMonth();
        $month->format('d-M-y');
        $year = $month;
        // $month = new Carbon('2022/06/01');
        // $year = new Carbon('2022/06/01');
        // $mt = $month->format('d-M-y');
        // $yt = $year->format('d-M-y');
        $username = $request->user()->username;
        if ($request->unit_id !== -1 && $request->unit_id !== null) {
            $unitId = $request->unit_id;
        } else {
            // $unitId = 1;
            $unitIdArr = BscUnitsController::getUnitArr($request);
            if($unitIdArr->count() == 0) {
                return response()->json(['statuscode' => 0]);
            }
            $unitId = $unitIdArr[0];
        }

        if($request->year_plan == 1) {
            return $this->index_year($request, $unitId);
        }
        $unit = BscUnits::find($unitId);
        $now = Carbon::now();
        $n = $now->format('d-M-y');
        $a = 1;
        // return [$month->toDateString(),  $year->toDateString(), $unitId , $now];
        $detailSetIndicators = BscSetIndicators::where('unit_id', $unitId)->whereDate('month_set', $month->toDateString())
            ->whereDate('year_set', $year->toDateString())->first()->detailSetIndicators()->pluck('id');
        $detailSetIndicators = BscDetailSetIndicators::whereIn('id', $detailSetIndicators)->whereDate('created_at', $now)->pluck('id');
        if ($detailSetIndicators->count() == 0) {
            $this->create($username, $unitId);
        }
        $arrSetIndicatorid = BscSetIndicators::where('unit_id', $unitId)->whereDate('month_set', $month->toDateString())->whereDate('year_set', $year->toDateString())->pluck('id');
        //get arr topic_id from arr set_indicator.
        $arrTopicId =  BscTopicOrders::select('topic_id')->whereIn('set_indicator_id', $arrSetIndicatorid)->distinct()->pluck('topic_id');
        //get topic from topic_id array -> with all chitieu.
        $topics = BscTopics::select('id', 'name')->whereIn('id', $arrTopicId)
            ->with(['targets.targets.targetUpdates' => function ($q) use ($request) {
                $q->where('username', $request->user()->username);
            }])
            ->with(['targets.targetUpdates' => function ($q) use ($request) {
                $q->where('username', $request->user()->username);
            }])
            ->with([
                'targets.setindicators' => function ($query) use ($request, $unitId, $year,  $month, $now) {
                    $query->select('id', 'set_indicator_id', 'target_id', 'active', 'total_plan', 'plan')->where('unit_id', $unitId)->whereDate('year_set', $year->toDateString())->whereDate('month_set', $month->toDateString());
                    $query->with(['detailSetIndicators' => function ($query) use ($now) {
                        $query->whereDate('created_at', $now);
                    }]);
                }
            ])->with([
                'targets.targets.setindicators' => function ($query) use ($request, $year, $month, $now, $unitId) {
                    $query->select('id', 'set_indicator_id', 'target_id', 'active', 'total_plan', 'plan')->where('unit_id', $unitId)->whereDate('year_set', $year->toDateString())->whereDate('month_set', $month->toDateString());
                    $query->with(['detailSetIndicators' => function ($query) use ($now) {
                        $query->whereDate('created_at', $now);
                    }]);
                }
            ])->get();
        return response()->json([
            'statuscode' => 1,
            'detailSetIndicators' => $detailSetIndicators,
            'topics' => $topics,
            'unit' => $unit,
        ]);
    }
    public function index_year(Request $request,  $uuId )
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $year = Carbon::now()->year;
        $year = new Carbon('01/01/'.$year);
        $year->format('d-M-y');

        $username = $request->user()->username;
        $unitId = $uuId;
        $unit = BscUnits::find($unitId);
        $now = Carbon::now();
        $n = $now->format('d-M-y');
        $a = 1;
        // return [$month->toDateString(),  $year->toDateString(), $unitId , $now];
        $detailSetIndicators = BscSetIndicators::where('unit_id', $unitId)
        ->whereNull('month_set')
        ->whereDate('year_set', $year->toDateString())
        ->first()->detailSetIndicators()->pluck('id');
        $detailSetIndicators = BscDetailSetIndicators::whereIn('id', $detailSetIndicators)
        ->whereDate('created_at', $now)
        ->pluck('id');
        if ($detailSetIndicators->count() == 0) {
            $this->createYear($username, $unitId, $year);
        }
        $arrSetIndicatorid = BscSetIndicators::where('unit_id', $unitId)
        ->whereNull('month_set')
        ->whereDate('year_set', $year->toDateString())
        ->pluck('id');
        //get arr topic_id from arr set_indicator.
        $arrTopicId =  BscTopicOrders::select('topic_id')
        ->whereIn('set_indicator_id', $arrSetIndicatorid)
        ->distinct()->pluck('topic_id');
        //get topic from topic_id array -> with all chitieu.
        $topics = BscTopics::select('id', 'name')->whereIn('id', $arrTopicId)
            ->with(['targets.targets.targetUpdates' => function ($q) use ($request) {
                $q->where('username', $request->user()->username);
            }])
            ->with(['targets.targetUpdates' => function ($q) use ($request) {
                $q->where('username', $request->user()->username);
            }])
            ->with([
                'targets.setindicators' => function ($query) use ($request, $unitId, $year, $now) {
                    $query
                    ->where('unit_id', $unitId)
                    ->whereDate('year_set', $year->toDateString())
                    ->whereNull('month_set');
                    $query->with(['detailSetIndicators' => function ($query) use ($now) {
                        $query->whereDate('created_at', $now);
                    }]);
                }
            ])->with([
                'targets.targets.setindicators' => function ($query) use ($request, $year, $now, $unitId) {
                    $query
                    ->where('unit_id', $unitId)
                    ->whereDate('year_set', $year->toDateString())
                    ->whereNull('month_set');
                    $query->with(['detailSetIndicators' => function ($query) use ($now) {
                        $query->whereDate('created_at', $now);
                    }]);
                }
            ])->get();
        return response()->json([
            'statuscode' => 1,
            'detailSetIndicators' => $detailSetIndicators,
            'topics' => $topics,
            'unit' => $unit,
        ]);
    }
    public function create($username, $unitId = 1, $month = null)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        if ($month ==  null) {
            $month = Carbon::now()->firstOfMonth();
            $month->format('y-M-y');
        }
        $year = $month;
        // $mt = $month->format('d-M-y');
        // $year = new Carbon('2022/06/01');
        // $yt = $year->format('d-M-y');
        $now = now();
        // $username = $request->user()->username;
        DB::transaction(function () use ($month, $year, $now, $username, $unitId) {
            $setIndicators = BscSetIndicators::where('unit_id', $unitId)
                ->whereDate('month_set', $month->toDateString())
                ->whereDate('year_set', $year->toDateString())->get();
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
    public function createYear($username, $unitId = 1, $year = null )
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        // $mt = $month->format('d-M-y');
        // $year = new Carbon('2022/06/01');
        // $yt = $year->format('d-M-y');
        $now = now();
        // $username = $request->user()->username;
        DB::transaction(function () use ( $year, $now, $username, $unitId) {
            $setIndicators = BscSetIndicators::where('unit_id', $unitId)
                ->whereNull('month_set')
                ->whereDate('year_set', $year->toDateString())->get();
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

    public function update1(Request $request)
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
        $setIndicators = BscSetIndicators::where('unit_id', $unitIdSI)
            ->whereDate('year_set', $year->toDateString())
            ->whereDate('month_set', $month->toDateString())
            ->get();
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
            // 'detailSetIndicators' => $detailSetIndicators
        ]);
    }
    public function update(Request $request)
    {
        $id = $request->id;
        $username = $request->user()->username;
        $totalPlan = $request->total_plan;
        //update row detail detailSetIndicator
        $data = $this->updateDetail($id, $username, $totalPlan);

        return response()->json([
            'statuscode' => 1,
            // 'detailSetIndicators' => $detailSetIndicators
            'data' => $data,
        ]);
    }

    public function updateDetail($id, $username,  $totalPlan)
    {
        $detailSetIndicator = BscDetailSetIndicators::find($id);
        $oldTotalPlan = $detailSetIndicator->total_plan;
        $detailSetIndicator->total_plan = $totalPlan;
        $detailSetIndicator->username_updated = $username;
        //update day
        $detailSetIndicator->save();
        //update value for
        $setIndicator = $detailSetIndicator->setIndicator;
        if($setIndicator->month_set ==  null) {
            return 1;
        }
        $yearSetSI = date('d-M-y', $setIndicator->year_set);
        $monthSetSI = date('d-M-y', $setIndicator->month_set);
        $targetId = $setIndicator->target_id;
        $month = new Carbon($monthSetSI);
        $year = new Carbon($yearSetSI);
        $mt = $month->format('d-M-y');
        $yr = $year->format('d-M-y');
        $now = Carbon::now();
        $now->format('d-M-y');
        //
        $unitIdSI = $setIndicator->unit_id;
        $setIndicator = BscSetIndicators::where('unit_id', $unitIdSI)
            ->whereDate('year_set', $year->toDateString())
            ->whereDate('month_set', $month->toDateString())
            ->where('target_id', $targetId)
            ->first();
        //update month;
        $detailSetIndicators = DB::transaction(function () use ($setIndicator, $totalPlan) {
            $setIndicator->total_plan = $totalPlan;
            $setIndicator->save();
        });

        $isUpdate = $setIndicator->is_update;
        $isChildUpdate = $setIndicator->is_child_update;
        if ($isUpdate == 1) {
            $allYear = $year->year;
            $years = new Carbon("$allYear" . '/01/01');
            $years->format('d-M-y');
            $yearSI = BscSetIndicators::where('unit_id', $unitIdSI)
                ->whereDate('year_set', $years->toDateString())
                ->whereNull('month_set')
                ->where('target_id', $targetId)
                ->first();
            //update month;
            $yearSI->total_plan +=  $totalPlan - $oldTotalPlan;
            $yearSI->save();
        }

        // update unit parent =
        if ($isChildUpdate == 1) {
            $unitParent = BscUnits::find($unitIdSI)->unit; // 21
            $targetId = $setIndicator->target_id; // 32
            if ($unitParent <> null) {
                $parentUnit_id = $unitParent->id;
                $mt = $month->format('d-M-y');
                $yr = $year->format('d-M-y');
                $parentSetIndicator = BscSetIndicators::where('unit_id', $parentUnit_id)
                    ->where('target_id', $targetId)
                    ->whereDate('month_set', $month->toDateString())
                    ->whereDate('year_set', $year->toDateString())
                    ->first();
                $parentDetailSetIndicator = $parentSetIndicator
                    ->detailSetIndicators()
                    ->whereDate('created_at', $now)->first();
                if ($parentDetailSetIndicator == null) {
                    $this->create($username, $parentUnit_id);
                }
                $parentSetIndicator = BscSetIndicators::where('unit_id', $parentUnit_id)
                    ->where('target_id', $targetId)
                    ->whereDate('month_set', $month->toDateString())
                    ->whereDate('year_set', $year->toDateString())
                    ->first();
                $parentDetailSetIndicator = $parentSetIndicator
                    ->detailSetIndicators()
                    ->whereDate('created_at', $now->toDateString())
                    ->first();
                $parentDetailSetIndicatorId = $parentDetailSetIndicator->id;
                $parentTotalPlan = $parentDetailSetIndicator->total_plan + $totalPlan - $oldTotalPlan;
                // return [$parentTotalPlan , $totalPlan, $oldTotalPlan, $parentDetailSetIndicatorId ];
                $this->updateDetail($parentDetailSetIndicatorId, $username, $parentTotalPlan);
            }
        }
    }
}
