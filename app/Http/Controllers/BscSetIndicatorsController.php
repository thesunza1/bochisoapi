<?php

namespace App\Http\Controllers;

use App\Models\BscSetIndicators;
use App\Models\BscTargets;
use App\Models\BscTopicOrders;
use App\Models\BscTopics;
use App\Models\BscUnits;
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
    //id , plan , type : 1: plan for set, 2:red warring, 3: organ planing
    public function update(Request $request)
    {
        $type = $request->type;
        $value = $request->plan;
        $plan = BscSetIndicators::find($request->id);
        if ($type == 1 || $type == null) {
            $plan->plan = $value;
        } else
        if ($type == 2) {
            $plan->min_warning = $value;
        } else if ($type == 3) {
            $plan->plan_warning = $value;
        } else if ($type == 4) {
            $plan->active = $value;
        }
        $plan->save();
        if ($type == 1 || $type == null) {
            if ($plan->month_set == null) {
                $year = new Carbon($plan->year_set);
                $year->format('d-M-y');
                $year->year;
                $month = now()->month;
                $unit_id = $plan->unit_id;
                for ($i = 1; $i <= $month; $i++) {
                    $yearSet = new Carbon('01-' . $i . '-' . $year);
                    $sis = BscSetIndicators::whereDate('year_set', $yearSet->toDateString())->where('unit_id', $unit_id)->get();
                    if ($sis->count() > 0) {
                        foreach ($sis as $si) {
                            $si->year_plan = $request->plan;
                            $si->save();
                        }
                    }
                }
            }
        }
        return response()->json(['statuscode' => 1]);
    }
    public function createWithTopicArr(Request $request)
    {
        $check = BscSetIndicatorsController::createWithTopicArrA($request, null);
        if ($check !== 1000) {
            return response()->json([
                'statuscode' => $check,
            ]);
        }
        for ($i = 1; $i <= 12; $i++) {
            BscSetIndicatorsController::createWithTopicArrA($request, $i);
        }
        if ($check == 1000) {
            return response()->json(['statuscode' => 1]);
        }
        return response()->json([
            'statuscode' => $check,
        ]);
    }
    //create with topic id arr, month , year,  unitId,

    // public function createWithTopicArr(Request $request)
    public static function createWithTopicArrA($request, $month1)
    {
        $topicIdArr = explode(',', $request->topic_id_arr);
        $username = $request->user()->username;
        $month = $month1;
        $year = $request->year;
        $unit_id = $request->unitId;
        $check = BscSetIndicatorsController::checkCreate($month, $year, $unit_id, $username);
        $order = [];

        if ($check <> 1) {
            return $check;
        }

        for ($i = 1; $i <=  count($topicIdArr); $i++) {
            array_push($order, $i);
        }
        if ($month == null) {
            $monthSet = null;
            $yearSet = new Carbon('01-01-' . $year);
            $yearSet->format('d-M-y');
            for ($i = 0; $i < count($topicIdArr); $i++) {
                $topic_id = $topicIdArr[$i];
                $orderNum = $order[$i];
                BscSetIndicatorsController::createWithTopicArrMonth($topic_id, $username, $unit_id,  $monthSet, $yearSet, $orderNum);
            };
        } else {
            $monthSet = new Carbon('01-' . $month . '-' . $year);
            $yearSet = new Carbon('01-01-' . $year);
            $monthSet->format('d-M-y');
            $yearSet->format('d-M-y');
            for ($i = 0; $i < count($topicIdArr); $i++) {
                $topic_id = $topicIdArr[$i];
                $orderNum = $order[$i];
                BscSetIndicatorsController::createWithTopicArrMonth($topic_id, $username, $unit_id,  $monthSet, $yearSet, $orderNum);
            };
        }
        return 1000;
    }

    public static function createWithTopicArrMonth(
        $topic_id,
        $username,
        $unit_id,
        $monthSet,
        $yearSet,
        $orderNum
    ) {
        DB::transaction(function () use ($topic_id, $username, $unit_id, $monthSet, $yearSet, $orderNum) {
            //topic width tagert
            $targets = BscTargets::where('topic_id', $topic_id)->get();
            if ($targets->count() == 0) return 'oooo';
            foreach ($targets as $target) {
                $dtSetIndicator = [
                    "username_created" => $username,
                    "month_set" => $monthSet,
                    "year_set" => $yearSet,
                    'unit_id' => $unit_id,
                    'plan' => 0,
                    'target_id' => $target->id,
                ];
                $setIndicator = BscSetIndicators::create($dtSetIndicator);

                BscTopicOrders::create([
                    'name' => $orderNum,
                    'set_indicator_id' => $setIndicator->id,
                    'topic_id' => $topic_id
                ]);

                foreach ($target->targets as $childTarget) {
                    $dtSetIndicator = [
                        "username_created" => $username,
                        "month_set" => $monthSet,
                        "year_set" => $yearSet,
                        'unit_id' => $unit_id,
                        'plan' => 0,
                        'target_id' => $childTarget->id,
                    ];
                    $clild = $setIndicator->setIndicators()->create($dtSetIndicator);
                    BscTopicOrders::create([
                        'name' => $orderNum,
                        'set_indicator_id' => $clild->id,
                        'topic_id' => $topic_id,
                    ]);
                }
            }
        });
    }

    public static function checkCreate($month, $year, $unitId, $username = '')
    {
        if ($month == null) {
            $yearSet = new Carbon('01/01/' . $year);
            $yearSet->format('d-M-y');
            $yearSetNum = BscSetIndicators::whereNull('month_set')
                ->whereDate('year_set', $yearSet->toDateString())
                ->where('unit_id', $unitId)
                ->get()->count();
            if ($yearSetNum > 0) {
                return 0; // bộ chỉ số năm đã tồn tại
            } else {
                $parentUnitId = BscUnits::find($unitId)->unit?->id;
                if ($parentUnitId == null) {
                    return 1;
                } else {
                    $yearSetNum = BscSetIndicators::whereNull('month_set')
                        ->whereDate('year_set', $yearSet->toDateString())
                        ->where('unit_id', $parentUnitId)
                        ->get()->count();
                    if ($yearSetNum == 0) return 2; //bộ chỉ số năm cha chưa tạo
                    return 1;
                }
            }
        } else {
            $monthSet = new Carbon($month . '/01' . '/' . $year);
            $yearSet = new Carbon($month . '/01' . '/' . $year);
            $monthSet->format('d-M-y');
            $yearSet->format('d-M-y');
            $checkYearSet = BscSetIndicatorsController::checkCreate(null, $year, $unitId, $username);
            if ($checkYearSet == 0) {
                $monthSetNum = BscSetIndicators::whereDate('month_set', $monthSet->toDateString())
                    ->whereDate('year_set', $yearSet->toDateString())
                    ->where('unit_id', $unitId)
                    ->get()->count();
                if ($monthSetNum == 0) {
                    $parentUnitId = BscUnits::find($unitId)->unit?->id;
                    if ($parentUnitId == null) {
                        return 1;
                    } else {
                        $parentSetNum = BscSetIndicators::whereDate('month_set',  $monthSet->toDateString())
                            ->whereDate('year_set', $yearSet->toDateString())
                            ->where('unit_id', $parentUnitId)
                            ->get()->count();
                        if ($parentSetNum == 0) return 4; //bộ chỉ số thang cha chưa tạo
                        return 1;
                    }
                } else {
                    return 3; //bo chỉ số tháng đã có
                }
            } else {
                return $checkYearSet == 1 ? 5 : $checkYearSet;
            }
        }

        return 1000;
    }

    //crete with copy from old month.
    public function createWithCopy(Request $request)
    {
    }
    //thang, năm , unit_id
    public function index(Request $request)
    {
        $arrUnitId = $request->user()->userUnits->pluck('unit_id');
        $units = BscUnits::whereIn('id', $arrUnitId)?->get();
        if ($arrUnitId == null || $arrUnitId->count() == 0) {
            return response()->json([
                'statuscode' => 0,
            ]);
        }
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $unit_id = $request->unit_id == null ? $units[0]->id : $request->unit_id;
        $unit = BscUnits::find($unit_id);
        if ($request->month <> 13) {
            $month = $request->month == null ? Carbon::now()->month : $request->month;
            $year = $request->year == null ? Carbon::now()->year : $request->year;
            $monthset = "$year" . '-' . "$month" . '-01';
            $yearset = "$year" . '-01-01';
            $month = new Carbon($monthset);
            $month->format('d-M-y');
            $year = new Carbon($yearset);
            $year->format('d-M-y');
            $targetArrId = BscSetIndicators::select('target_id')
                ->distinct()
                ->whereDate('year_set', $yearset)
                ->whereDate('month_set', $monthset)
                ->where('unit_id', $unit_id)
                ->where('active', 1)
                ->orderBy('target_id')
                ->pluck('target_id');
            $arrSetIndicatorid = BscSetIndicators::where('unit_id', $unit_id)->whereDate('month_set', $month->toDateString())->whereDate('year_set', $year->toDateString())->pluck('id');
            // $arrSetIndicatorid = BscSetIndicators::where('unit_id', 1)->where('year_set', $month)->where('month_set', $month)->pluck('id');
            //get arr topic_id from arr set_indicator.
            $arrTopicId = BscTargets::select('topic_id')->whereIn('id', $targetArrId)->whereNotNull('topic_id')->distinct()->pluck('topic_id');
            // $arrTopicId =  BscTopicOrders::select('topic_id')->whereIn('set_indicator_id', $arrSetIndicatorid)->distinct()->pluck('topic_id');
            // return [$arrTopicId , $arrSetIndicatorid];
            //get topic from topic_id array -> with all chitieu.
            // $topics = BscTopics::select('id', 'name')->whereIn('id', $arrTopicId)->orderBy('id')
            //     ->with(['targets.targetUpdates' => function ($q) use ($request) {
            //         $q->where('username', $request->user()->username);
            //     }])
            //     ->with(['targets.targets.targetUpdates' => function ($q) use ($request) {
            //         $q->where('username', $request->user()->username);
            //     }])
            //     ->with([
            //         'targets.setindicators' => function ($query) use ($request, $year,  $month, $unit_id) {
            //             $query->select('id', 'set_indicator_id', 'target_id', 'active', 'total_plan', 'plan', 'year_plan', 'plan_warning', 'updated_at', 'year_set', 'month_set', 'min_warning')
            //                 ->where('unit_id', $unit_id)
            //                 ->whereDate('year_set', $year->toDateString())
            //                 ->whereDate('month_set', $month->toDateString())
            //                 ->with(['detailSetIndicator' => function ($q) {
            //                     $q->select('users.name', 'bsc_detail_set_indicators.*')->join('users', 'bsc_detail_set_indicators.username_updated', 'users.username', 'updated_at');
            //                 }]);
            //         }
            //     ])->with([
            //         'targets.targets.setindicators' => function ($query) use ($request, $year, $month, $unit_id) {
            //             $query->select('id', 'set_indicator_id', 'target_id', 'active', 'total_plan', 'plan', 'year_plan', 'plan_warning', 'updated_at', 'year_set', 'month_set', 'min_warning')
            //                 ->where('unit_id', $unit_id)
            //                 ->whereDate('year_set', $year->toDateString())
            //                 ->whereDate('month_set', $month->toDateString())
            //                 ->with(['detailSetIndicator' => function ($q) {
            //                     $q->select('users.name', 'bsc_detail_set_indicators.*')->join('users', 'bsc_detail_set_indicators.username_updated', 'users.username');
            //                 }]);
            //         }
            //     ])->get();
            // return response()->json($arrTopicId);

            $topics = BscTopics::select('id', 'name')->whereIn('id', $arrTopicId)->orderBy('id')
                ->with(['targets' => function ($q) use ($request, $year, $month, $unit_id, $targetArrId) {
                    $q->whereIn('id', $targetArrId)
                        ->with(['targetUpdates' => function ($q) use ($request) {
                            $q->where('username', $request->user()->username);
                        }])
                        ->with(['targets' => function ($q) use ($request, $year, $month, $unit_id, $targetArrId) {
                            $q->whereIn('id', $targetArrId)
                                ->with(['targetUpdates' => function ($q) use ($request) {
                                    $q->where('username', $request->user()->username);
                                }])
                                ->with(['setindicators' => function ($q) use ($year, $month, $unit_id) {
                                    $q->where('unit_id', $unit_id)
                                        ->whereDate('year_set', $year->toDateString())
                                        ->whereDate('month_set', $month->toDateString())
                                        ->with(['detailSetIndicator' => function ($q) {
                                            $q->select('users.name', 'bsc_detail_set_indicators.*')->join('users', 'bsc_detail_set_indicators.username_updated', 'users.username');
                                        }]);
                                }])->orderBy('order');
                        }])
                        ->with(['setindicators' => function ($q) use ($year, $month, $unit_id) {
                            $q->where('unit_id', $unit_id)
                                ->whereDate('year_set', $year->toDateString())
                                ->whereDate('month_set', $month->toDateString())
                                ->with(['detailSetIndicator' => function ($q) {
                                    $q->select('users.name', 'bsc_detail_set_indicators.*')->join('users', 'bsc_detail_set_indicators.username_updated', 'users.username');
                                }]);
                        }])->orderBy('order');
                }])->get();

        } else {
            $month =  '01';
            $year = $request->year == null ? Carbon::now()->year : $request->year;
            $monthset = null;
            $yearset = "$year" . '-01-01';
            $year = new Carbon($yearset);
            $year->format('d-M-y');
            $month = null;

            $arrSetIndicatorid = BscSetIndicators::where('unit_id', $unit_id)->whereNull('month_set')->whereDate('year_set', $year->toDateString())->pluck('id');
            // $arrSetIndicatorid = BscSetIndicators::where('unit_id', 1)->where('year_set', $month)->where('month_set', $month)->pluck('id');
            //get arr topic_id from arr set_indicator.
            $arrTopicId =  BscTopicOrders::select('topic_id')->whereIn('set_indicator_id', $arrSetIndicatorid)->distinct()->pluck('topic_id');
            //get topic from topic_id array -> with all chitieu.
            $topics = BscTopics::select('id', 'name')->whereIn('id', $arrTopicId)->orderBy('id')
                ->with(['targets.targets.targetUpdates' => function ($q) use ($request) {
                    $q->where('username', $request->user()->username);
                }])
                ->with(['targets.targetUpdates' => function ($q) use ($request) {
                    $q->where('username', $request->user()->username);
                }])
                ->with([
                    'targets.setindicators' => function ($query) use ($request, $year,  $month, $unit_id) {
                        $query->select('id', 'set_indicator_id', 'target_id', 'active', 'total_plan', 'plan', 'year_plan', 'plan_warning', 'updated_at', 'year_set', 'month_set', 'min_warning')
                            ->where('unit_id', $unit_id)
                            ->whereDate('year_set', $year->toDateString())
                            ->whereNull('month_set')
                            ->with(['detailSetIndicator' => function ($q) {
                                $q->join('users', 'bsc_detail_set_indicators.username_updated', 'users.username');
                            }]);
                    }
                ])->with([
                    'targets.targets.setindicators' => function ($query) use ($request, $year, $month, $unit_id) {
                        $query->select('id', 'set_indicator_id', 'target_id', 'active', 'total_plan', 'plan', 'year_plan', 'plan_warning', 'updated_at', 'year_set', 'month_set', 'min_warning')
                            ->where('unit_id', $unit_id)
                            ->whereDate('year_set', $year->toDateString())
                            ->whereNull('month_set')
                            ->with(['detailSetIndicator' => function ($q) {
                                $q->join('users', 'bsc_detail_set_indicators.username_updated', 'users.username');
                            }]);
                    }
                ])->get();
        }



        return response()->json([
            'statuscode' => 1,
            'topics' => $topics,
            'unit' => $unit,
        ]);
    }
}
