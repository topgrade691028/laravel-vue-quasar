<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Fuel;
use App\User;
use Illuminate\Support\Facades\Auth;

class FuelController extends Controller
{
  public function __construct()
  {
  }
  
  public function getFuels (Request $request) {
    if (Auth::user()) {
      $user = Auth::user();
      $start = $request['start'] ? $request['start'] : 0;
      $numPerPage = $request['numPerPage'] ? $request['numPerPage'] : 10;
      $sortBy = $request['sortBy'] ? $request['sortBy'] : 'card_no';
      $desc = $request['descending'] ? 'DESC' : 'ASC';

      $fuels = Fuel::with(['user'])->with(['driver']);

      if ($user->user_type != '0') {
        $fuels = $fuels->whereUserId($user->id);
      }
      if ($request['conditions'] && $request['conditions']['filter']) {
        $search = $request['conditions']['filter'];
        $fuels = $fuels ->where('company', 'like', '%' . $search . '%')->orWhere('card_no', 'like', '%' . $search . '%');
      }
      // $fleets = $fleets->where('deleted_date', NULL);
      $totalCount = count($fuels->get());
      $fuels = $fuels->orderBy($sortBy, $desc)->skip($start)->take($numPerPage)->get();

      if ($totalCount == 0) {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => []], 200, [], JSON_NUMERIC_CHECK);
      } else {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => $fuels], 200, [], JSON_NUMERIC_CHECK);
      }
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
  public function createFuel (Request $request) {
    if (Auth::user()) {
      $user_id = Auth::user()->id;
      $fuelInfo = $request->all();
      
      try {
        $existing_fuel = Fuel::whereUserId($user_id)->where('card_no', $fuelInfo['card_no'])->where('company', $fuelInfo['company'])->get();
        if (count($existing_fuel)) {
          return response()->json(['message' => 'Fuel already exists'], 409);
        } else {
          $fuelInfo['user_id'] = $user_id;
          $fuel = Fuel::create($fuelInfo);
          return response()->json(['success' => 'success', 'data' => $fuel], 200, [], JSON_NUMERIC_CHECK);
        }
      } catch (\Exception $e) {
        return response()->json(['message' => 'Cant add Fuel', 'error' => $e], 500);
      }
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
  public function updateFuel (Request $request) {
    if (Auth::user()) {
      $fuelInfo = $request['data'];
      // $user_id = Auth::user()->id;
      $fuelID = $request['conditions']['id'];
      try {
        $fuel = Fuel::find($fuelID);
        $fuel->update($fuelInfo);
        return response()->json(['success' => 'success', 'data' => $fuel], 200, [], JSON_NUMERIC_CHECK);
      } catch (\Exception $e) {
        return response()->json(['message' => 'Fuel updating is failed', 'error' => $e], 500);
      }
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
  public function removeFuel (Request $request) {
    if (Auth::user()) {
      $fuelID = $request['conditions']['id'];
      try {
        $fuel = Fuel::find($fuelID);
        // $driver->deleted_date = date("Y-m-d h:i:s");
        // $driver->save();
        $fuel->delete();
        return response()->json(['success' => 'Fuel Successfully Removed']);
      } catch (\Exception $e) {
        return response()->json(['error' => 'Fuel Remove Failed']);
      }
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
  public function getFuelDetails(Request $request) {
    $fuel_id = $request->query()['id'];
    try {
      $fuel = Fuel::find($fuel_id);
      return response()->json(['success'=>'success', 'data' => $fuel], 200, [], JSON_NUMERIC_CHECK);
    } catch (\Exception $e) {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
}
