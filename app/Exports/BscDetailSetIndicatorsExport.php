<?php

namespace App\Exports;

use App\Models\BscSetIndicators;
use App\Models\BscTargets;
use App\Models\BscTopicOrders;
use App\Models\BscTopics;
use App\Models\BscUnits;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class BscDetailSetIndicatorsExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    private $monthSet;
    private $yearSet;
    private $unitId;
    private $asYear = 0;

    function __construct($month, $year, $unitId)
    {
        if ($month == -1) {
            $vMonth = '01';
            $this->asYear = 1;
        } else {
            $vMonth = $month == null ? Carbon::now()->month : $month;
        }
        $vYear = $year == null ?  Carbon::now()->year : $year;
        $this->monthSet = new Carbon('01-' . $vMonth . '-' . $vYear);
        $this->monthSet->format('d-M-y');
        $this->yearSet = new Carbon('01-01-' . $vYear);
        $this->yearSet->format('d-M-y');
        $this->unitId  = $unitId;
    }

    public function view(): View
    {
        $topic = $this->getTopic();
        return view('DetailSetIndicators', [
            'topics' => $topic,
            'monthset' => $this->monthSet,
            'unit' => BscUnits::find($this->unitId),
            'nowdate' => now()->format('d-m-Y')
        ]);
    }

    private function getTopic()
    {
        $targetArrayId =  $this->getTargetArrayId();
        $arrTopicId = $this->getTopicArrayId($targetArrayId);
        $sis = $this->getSetIndicators($targetArrayId , $arrTopicId);
        return  $sis;
    }

    //get set indicators object from target array id , arr topic id
    private function getSetIndicators($targetArrayId, $arrTopicId) :array
    {
        $unitId = $this->unitId;
        $monthSet = $this->monthSet;
        $yearSet = $this->yearSet;
        $asYear = $this->asYear;
        $topic = BscTopics::whereIn('id', $arrTopicId)->orderBy('id')
            ->with(['targets' => function ($q) use ($targetArrayId, $unitId, $monthSet, $yearSet, $asYear) {
                $q->whereIn('id', $targetArrayId)
                    ->with(['setindicators' => function ($q) use ( $unitId, $monthSet, $yearSet, $asYear) {
                        $q->where('unit_id', $unitId)
                            ->whereDate('year_set', $yearSet->toDateString());
                        $asYear == 1 ? $q->whereNull('month_set') : $q->whereDate('month_set', $monthSet->toDateString());
                    }])
                    ->with(['targets' => function ($q) use ($targetArrayId, $unitId, $monthSet, $yearSet, $asYear) {
                        $q->whereIn('id', $targetArrayId)->with(['setindicators' => function ($q) use ( $unitId, $monthSet, $yearSet, $asYear) {
                            $q->where('unit_id', $unitId)
                                ->whereDate('year_set', $yearSet->toDateString());
                            $asYear == 1 ? $q->whereNull('month_set') : $q->whereDate('month_set', $monthSet->toDateString());
                        }]);
                    }]);
            }])->get();
        return $topic->toArray();
    }

    //get topic array id
    private function getTopicArrayId($targetArrayId): Collection
    {
        $arrTopicId = BscTargets::whereIn('id', $targetArrayId)
            ->select('topic_id')->distinct()->pluck('topic_id');
        return $arrTopicId;
    }

    //get target array id
    private function getTargetArrayId(): Collection
    {
        $targetArray = BscSetIndicators::select('target_id')
            ->whereDate('year_set', $this->yearSet->toDateString())
            ->where('unit_id', $this->unitId)
            ->where('active', 1);
        $this->asYear == 1 ? $targetArray->whereNull('month_set') : $targetArray->whereDate('month_set', $this->monthSet->toDateString());
        $targetArrayId = $targetArray->orderBy('target_id')->pluck('target_id');
        return $targetArrayId;
    }
}
