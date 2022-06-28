<?php

namespace App\Http\Controllers;

use App\Models\BscUnits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class BscUnitsController extends Controller
{
    //
    public function index(Request $request) {
        $units = BscUnits::with('units')->get();

        return response()->json([
            'statuscode' => 1,
            'units' => $units
        ]);
    }
    public function create(Request $request) {
        $now = now();
        $data = [
            'username_created' => $request->user()->username,
            'unit_id' => $request->unit_id == -1 ? null : $request->unit_id,
            'name' => $request->name,
            'created_at' => $now
        ];

        $unit = BscUnits::create($data);

        return response()->json([
            'statuscode' => 1,
            'unit' => $unit,
        ], 200);

    }
    public function update(Request $request) {
        $now = now();
        $data = [
            'username_update' => $request->user()->username,
            'unit_id' => $request->unit_id,
            'name' => $request->name,
            'updated_at' => $now
        ];

        $unit = BscUnits::create($data);

        return response()->json($unit, 200);

    }
}
