<?php

namespace App\Http\Controllers;

use App\Driver;
use App\DriverService;
use Illuminate\Http\Request;
use App\Fleet;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery\Undefined;

class FleetController extends Controller
{
  public function __construct()
  {
    // $this->middleware('driverAuth');
  }
  
  public function getFleetAll(Request $request) {
    if (Auth::guard('api')->user()) {
      $user = Auth::guard('api')->user();
      $fleets = [];
      if ($user->user_type != '0') {
        $fleets = Fleet::whereUserId($user->id)->get();
      } else {
        $fleets = Fleet::all();
      }
      return response()->json(['success'=>'success', 'data'=>$fleets], 200, [], JSON_NUMERIC_CHECK);
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
  public function getFleets (Request $request) {
    if (Auth::guard('api')->user()) {
      $user = Auth::guard('api')->user();
      $start = $request['start'] ? $request['start'] : 0;
      $numPerPage = $request['numPerPage'] ? $request['numPerPage'] : 10;
      $sortBy = $request['sortBy'] ? $request['sortBy'] : 'van_number';
      $desc = $request['descending'] ? 'DESC' : 'ASC';

      // $fleets = Fleet::with(['user'])->with(['driver']);
      $fleets = Fleet::with(['user'])->leftJoin('drivers', 'drivers.id', '=', 'fleets.driver_id');

      if ($user->user_type != '0') {
        $fleets = $fleets->where('fleets.user_id', $user->id);
      }
      if ($request['conditions'] && isset($request['conditions']['driver_name'])) {
        $driver_name = $request['conditions']['driver_name'];
        $fleets = $fleets->where('driver_name', $driver_name);
      }
      if ($request['conditions'] && isset($request['conditions']['van_number'])) {
        $van_number = $request['conditions']['van_number'];
        $fleets = $fleets->where('van_number', $van_number);
      }
      $fleets = $fleets->select('fleets.*', 'drivers.driver_name');
      // $fleets = $fleets->where('deleted_date', NULL);
      $totalCount = count($fleets->get());
      $fleets = $fleets->orderBy($sortBy, $desc)->skip($start)->take($numPerPage)->get();

      if ($totalCount == 0) {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => []], 200, [], JSON_NUMERIC_CHECK);
      } else {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => $fleets], 200, [], JSON_NUMERIC_CHECK);
      }
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
  public function createFleet (Request $request) {
    if (Auth::guard('api')->user()) {
      $user_id = Auth::guard('api')->user()->id;
      $fleetInfo = $request->all();
      
      try {
        // $existing_fleet = Fleet::whereUserId($user_id)->where('van_number', $fleetInfo['van_number'])->get();
        $existing_fleet = Fleet::where('driver_id', $fleetInfo['driver_id'])->get();
        if (count($existing_fleet)) {
          return response()->json(['message' => 'This drivr already assigned the van number'], 409);
        } else {
          $fleetInfo['user_id'] = $user_id;
          $fleet = Fleet::create($fleetInfo);
          return response()->json(['success' => 'success', 'data' => $fleet], 200, [], JSON_NUMERIC_CHECK);
        }
      } catch (\Exception $e) {
        return response()->json(['message' => 'Cant add Fleet', 'error' => $e], 500);
      }
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
  public function updateFleet (Request $request) {
    if (Auth::guard('api')->user()) {
      $fleetInfo = $request['data'];
      // $user_id = Auth::guard('api')->user()->id;
      $fleetID = $request['conditions']['id'];
      try {
        $fleet = Fleet::find($fleetID);
        $fleet->update($fleetInfo);
        return response()->json(['success' => 'success', 'data' => $fleet], 200, [], JSON_NUMERIC_CHECK);
      } catch (\Exception $e) {
        return response()->json(['message' => 'Fleet updating is failed', 'error' => $e], 500);
      }
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
  public function removeFleet (Request $request) {
    if (Auth::guard('api')->user()) {
      $fleetID = $request['conditions']['id'];
      try {
        $fleet = Fleet::find($fleetID);
        // $driver->deleted_date = date("Y-m-d h:i:s");
        // $driver->save();
        $fleet->delete();
        return response()->json(['success' => 'Fleet Successfully Removed']);
      } catch (\Exception $e) {
        return response()->json(['error' => 'Fleet Remove Failed']);
      }
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
  public function getFleetDetails(Request $request) {
    $fleetID = $request->query()['id'];
    try {
      $fleet = Fleet::find($fleetID);
      return response()->json(['success'=>'success', 'data' => $fleet], 200, [], JSON_NUMERIC_CHECK);
    } catch (\Exception $e) {
      return response()->json(['failed'=>'failed'], 401);
    }
  }



  public function getServices (Request $request) {
    // if (Auth::guard('api')->user()) {
      $driver_id = isset($request['driver_id']) ? $request['driver_id'] : '';
      $show_all = isset($request['showAll']) ? $request['showAll'] : false;
      // $serviceInfo = $request->all();
      if ($driver_id) {
        $fleet = Fleet::where('driver_id', $driver_id)->get()[0];
        $user_id = $fleet['user_id'];
      } else {
        // print_r(Auth::guard('api')->user()); exit;
        $user_id = Auth::guard('api')->user()->id;
      }
      // $user = Auth::guard('api')->user();
      $start = $request['start'] ? $request['start'] : 0;
      $numPerPage = $request['numPerPage'] ? $request['numPerPage'] : 10;
      $sortBy = $request['sortBy'] ? $request['sortBy'] : 'driver_name';
      $desc = $request['descending'] ? 'DESC' : 'ASC';

      $services = DriverService::with(['user'])->leftJoin('fleets', 'fleets.id', '=', 'driver_services.fleet_id')->leftJoin('drivers', 'drivers.id', '=', 'fleets.driver_id');
      if (!$driver_id) {
        if (Auth::guard('api')->user()->user_type != '0') {
          $services = $services->where('driver_services.user_id', $user_id);
        }
      }
      if ($request['conditions'] && isset($request['conditions']['van_number'])) {
        $van_number = $request['conditions']['van_number'];
        $services = $services->where('van_number', $van_number);
      }
      if (!$driver_id) {
        if (!$show_all) {
          $services = $services->where('driver_services.is_first', 1);
        }
      } else {
        $services = $services->where('fleets.driver_id', $driver_id);
      }
      $services = $services->select(['driver_services.*',DB::raw('(driver_services.next_service_mileage - driver_services.service_mileage) as left_mileage'), 'fleets.van_number', 'drivers.driver_name']);
      $totalCount = count($services->get());
      $services = $services->orderBy('driver_services.created_at', 'desc')->orderBy($sortBy, $desc)->skip($start)->take($numPerPage)->get();

      if ($totalCount == 0) {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => []], 200, [], JSON_NUMERIC_CHECK);
      } else {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => $services], 200, [], JSON_NUMERIC_CHECK);
      }
    // } else {
    //   return response()->json(['failed'=>'failed'], 401);
    // }
  }
  public function createService (Request $request) {
    // if (Auth::guard('api')->user()) {
      $driver_id = isset($request['driver_id']) ? $request['driver_id'] : '';
      $serviceInfo = $request->all();
      if ($driver_id) {
        $fleet = Fleet::where('driver_id', $driver_id)->get()[0];
        $user_id = $fleet['user_id'];
      } else {
        $user_id = Auth::guard('api')->user()->id;
      }
      try {
        // if ($serviceInfo['is_first']) {
        //   $existing_service = DriverService::whereUserId($user_id)->where('fleet_id', $serviceInfo['fleet_id'])->where('is_first', 1)->get();
        //   if (count($existing_service)) {
        //     return response()->json(['message' => 'This driver is already assigned the van number'], 409);
        //   } else {
        //     $serviceInfo['user_id'] = $user_id;
        //     $service = DriverService::create($serviceInfo);
        //     return response()->json(['success' => 'success', 'data' => $service], 200, [], JSON_NUMERIC_CHECK);
        //   }
        // } else {
        //   $serviceInfo['user_id'] = $user_id;
        //   $service = DriverService::create($serviceInfo);
        //   return response()->json(['success' => 'success', 'data' => $service], 200, [], JSON_NUMERIC_CHECK);
        // }
        $serviceInfo['user_id'] = $user_id;
        $service = DriverService::create($serviceInfo);
        return response()->json(['success' => 'success', 'data' => $service], 200, [], JSON_NUMERIC_CHECK);
      } catch (\Exception $e) {
        return response()->json(['message' => 'Cant add service data', 'error' => $e], 500);
      }
    // } else {
    //   return response()->json(['failed'=>'failed'], 401);
    // }
  }
  public function updateService (Request $request) {
    // if (Auth::guard('api')->user()) {
      $serviceInfo = $request['data'];
      // $user_id = Auth::guard('api')->user()->id;
      $serviceID = $request['conditions']['id'];
      try {
        $service = DriverService::find($serviceID);
        $service->update($serviceInfo);
        return response()->json(['success' => 'success', 'data' => $service], 200, [], JSON_NUMERIC_CHECK);
      } catch (\Exception $e) {
        return response()->json(['message' => 'Driver Service data updating is failed', 'error' => $e], 500);
      }
    // } else {
    //   return response()->json(['failed'=>'failed'], 401);
    // }
  }
  public function removeService (Request $request) {
    if (Auth::guard('api')->user()) {
      $serviceID = $request['conditions']['id'];
      try {
        $service = DriverService::find($serviceID);
        // $driver->deleted_date = date("Y-m-d h:i:s");
        // $driver->save();
        $service->delete();
        return response()->json(['success' => 'Service Data Successfully Removed']);
      } catch (\Exception $e) {
        return response()->json(['error' => 'Service Data Remove Failed']);
      }
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
  public function getServiceDetails(Request $request) {
    $serviceID = $request->query()['id'];
    try {
      $service = DriverService::find($serviceID);
      // $total_mileage = DriverService::where('fleet_id', $service['fleet_id'])->where('created_at', '<', $service['created_at'])->select([DB::raw('sum(service_mileage) as total_mileage')])->get();
      // $total_mileage = $total_mileage[0]['total_mileage'] ? $total_mileage[0]['total_mileage'] : 0;
      // $service['total_mileage'] = $total_mileage;
      return response()->json(['success'=>'success', 'data' => $service], 200, [], JSON_NUMERIC_CHECK);
    } catch (\Exception $e) {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
  public function getMileageDrivers(Request $request) {
    $user_id = Auth::guard('api')->user()->id;
    $drivers = DriverService::leftJoin('fleets', 'fleets.id', '=', 'driver_services.fleet_id')->leftJoin('drivers', 'drivers.id', '=', 'fleets.driver_id')->leftJoin(DB::raw('(SELECT * FROM driver_services where is_first = "1" ORDER BY created_at DESC LIMIT 1) as d2'), 'd2.fleet_id', '=', 'driver_services.fleet_id');
    if (Auth::guard('api')->user()->user_type != '0') {
      $drivers = $drivers->where('driver_services.user_id', $user_id);
    }
    $drivers = $drivers->where('driver_services.is_first', 0);
    $drivers = $drivers->select('drivers.driver_name')->groupBy('driver_name')->orderBy('driver_name', 'ASC')->get();
    return response()->json(['success'=>'success', 'data' => $drivers], 200, [], JSON_NUMERIC_CHECK);
  }
  public function getMileageAll (Request $request) {
    // if (Auth::guard('api')->user()) {
      $driver_id = isset($request['driver_id']) ? $request['driver_id'] : '';
      if ($driver_id) {
        $fleet = Fleet::where('driver_id', $driver_id)->get()[0];
        $user_id = $fleet['user_id'];
      } else {
        $user_id = Auth::guard('api')->user()->id;
      }
      $services = DriverService::with(['user'])->leftJoin('fleets', 'fleets.id', '=', 'driver_services.fleet_id')->leftJoin('drivers', 'drivers.id', '=', 'fleets.driver_id')->leftJoin(DB::raw('(SELECT * FROM driver_services where is_first = "1" ORDER BY created_at DESC LIMIT 1) as d2'), 'd2.fleet_id', '=', 'driver_services.fleet_id');
      if (!$driver_id) {
        if (Auth::guard('api')->user()->user_type != '0') {
          $services = $services->where('driver_services.user_id', $user_id);
        }
      }
      if ($request['conditions'] && $request['conditions']['van_number']) {
        $van_number = $request['conditions']['van_number'];
        $services = $services->where('van_number', $van_number);
      }
      if ($request['conditions'] && $request['conditions']['driver_name']) {
        $driver_name = $request['conditions']['driver_name'];
        $services = $services->where('driver_name', $driver_name);
      }
      // if ($request['conditions'] && $request['conditions']['filter']) {
      //   $search = $request['conditions']['filter'];
      //   $services = $services->where(function($q) use ($search) {
      //     $q->orWhere('driver_name', 'like', '%' . $search . '%')
      //       ->orWhere('van_number', 'like', '%' . $search . '%');
      //   });
      // }
      if ($driver_id) {
        $services = $services->where('fleets.driver_id', $driver_id);
      }
      $services = $services->where('driver_services.is_first', 0);
      $services = $services->select('driver_services.*', 'd2.next_service_mileage as next_service', DB::raw('(d2.next_service_mileage - driver_services.service_mileage) as left_mileage'), 'fleets.van_number', 'drivers.driver_name');
      $totalCount = count($services->get());
      $services = $services->orderBy('driver_services.created_at', 'desc')->orderBy('driver_name', 'ASC')->get();

      if ($totalCount == 0) {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => []], 200, [], JSON_NUMERIC_CHECK);
      } else {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => $services], 200, [], JSON_NUMERIC_CHECK);
      }
    // } else {
    //   return response()->json(['failed'=>'failed'], 401);
    // }
  }
  public function getMileageRecords (Request $request) {
    // if (Auth::guard('api')->user()) {
      $driver_id = isset($request['driver_id']) ? $request['driver_id'] : '';
      // $show_all = isset($request['showAll']) ? $request['showAll'] : false;
      // $serviceInfo = $request->all();
      if ($driver_id) {
        try {
          $fleet = Fleet::where('driver_id', $driver_id)->get()[0];
          $user_id = $fleet['user_id'];
        } catch (\Exception $e) {
          return response()->json(['failed'=>'failed', 'message' => 'This driver doesnt assign the van number yet'], 500, [], JSON_NUMERIC_CHECK);
        }
      } else {
        // print_r(Auth::guard('api')->user()); exit;
        $user_id = Auth::guard('api')->user()->id;
      }
      // $user = Auth::guard('api')->user();
      $start = $request['start'] ? $request['start'] : 0;
      $numPerPage = $request['numPerPage'] ? $request['numPerPage'] : 10;
      $sortBy = $request['sortBy'] ? $request['sortBy'] : 'driver_name';
      $desc = $request['descending'] ? 'DESC' : 'ASC';

      $services = DriverService::with(['user'])->leftJoin('fleets', 'fleets.id', '=', 'driver_services.fleet_id')->leftJoin('drivers', 'drivers.id', '=', 'fleets.driver_id')->leftJoin(DB::raw('(SELECT * FROM driver_services where is_first = "1" ORDER BY created_at DESC LIMIT 1) as d2'), 'd2.fleet_id', '=', 'driver_services.fleet_id');
      if (!$driver_id) {
        if (Auth::guard('api')->user()->user_type != '0') {
          $services = $services->where('driver_services.user_id', $user_id);
        }
      }
      if ($request['conditions'] && isset($request['conditions']['van_number'])) {
        $van_number = $request['conditions']['van_number'];
        $services = $services->where('van_number', $van_number);
      }
      if ($request['conditions'] && isset($request['conditions']['driver_name'])) {
        $driver_name = $request['conditions']['driver_name'];
        $services = $services->where('driver_name', $driver_name);
      }
      if ($driver_id) {
        $services = $services->where('fleets.driver_id', $driver_id);
      }
      $services = $services->where('driver_services.is_first', 0);
      $services = $services->select('driver_services.*', 'd2.next_service_mileage as next_service', DB::raw('(d2.next_service_mileage - driver_services.service_mileage) as left_mileage'), 'fleets.van_number', 'drivers.driver_name');
      $totalCount = count($services->get());
      $services = $services->orderBy('driver_services.created_at', 'desc')->orderBy($sortBy, $desc)->skip($start)->take($numPerPage)->get();

      if ($totalCount == 0) {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => []], 200, [], JSON_NUMERIC_CHECK);
      } else {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => $services], 200, [], JSON_NUMERIC_CHECK);
      }
    // } else {
    //   return response()->json(['failed'=>'failed'], 401);
    // }
  }
  public function createMileageRecord (Request $request) {
      $recordInfo = $request->all();      
      try {
        $record = DriverService::create($recordInfo);
        return response()->json(['success' => 'success', 'data' => $record], 200, [], JSON_NUMERIC_CHECK);
      } catch (\Exception $e) {
        return response()->json(['message' => 'Cant add odometer record', 'error' => $e], 500);
      }
  }
  public function updateMileageRecord (Request $request) {
    // if (Auth::guard('api')->user()) {
      $recordInfo = $request['data'];
      // $user_id = Auth::guard('api')->user()->id;
      $recordID = $request['conditions']['id'];
      try {
        $record = DriverService::find($recordID);
        $record->update($recordInfo);
        return response()->json(['success' => 'success', 'data' => $record], 200, [], JSON_NUMERIC_CHECK);
      } catch (\Exception $e) {
        return response()->json(['message' => 'Odometer record updating is failed', 'error' => $e], 500);
      }
    // } else {
    //   return response()->json(['failed'=>'failed'], 401);
    // }
  }
  public function removeMileageRecord (Request $request) {
    // if (Auth::guard('api')->user()) {
      $serviceID = $request['conditions']['id'];
      try {
        $service = DriverService::find($serviceID);
        $service->delete();
        return response()->json(['success' => 'Mileage Record is successfully removed']);
      } catch (\Exception $e) {
        return response()->json(['error' => 'Mileage Record Remove Failed']);
      }
    // } else {
    //   return response()->json(['failed'=>'failed'], 401);
    // }
  }
  public function getMileageDetails(Request $request) {
    $mileageID = isset($request->query()['id']) ? $request->query()['id'] : '';
    $driverID = isset($request->query()['driver_id']) ? $request->query()['driver_id'] : '';
    // try {
      if (!$mileageID) {
        try {
          $record = DriverService::leftJoin('fleets', 'fleets.id', '=', 'driver_services.fleet_id')->leftJoin('drivers', 'drivers.id', '=', 'fleets.driver_id');
          if ($driverID) {
            $record = $record->where('fleets.driver_id', $driverID);
          }
          $record = $record->select('driver_services.*', 'fleets.van_number', 'drivers.driver_name', 'fleets.driver_id')->orderBy('driver_services.created_at', 'desc')->get()[0];
          return response()->json(['success'=>'success', 'data' => $record], 200, [], JSON_NUMERIC_CHECK);
        } catch (\Exception $e) {
          return response()->json(['failed'=>'failed', 'message' => 'This driver doesnt assign the van number yet'], 500, [], JSON_NUMERIC_CHECK);
        }
      } else {
        $record =DriverService::leftJoin('fleets', 'fleets.id', '=', 'driver_services.fleet_id')->leftJoin('drivers', 'drivers.id', '=', 'fleets.driver_id')->where('driver_services.id', $mileageID);
        $record = $record->select('driver_services.*', 'fleets.van_number', 'drivers.driver_name', 'fleets.driver_id')->get()[0];

        // $total_mileage = DriverService::where('fleet_id', $record['fleet_id'])->where('created_at', '<', $record['created_at'])->select([DB::raw('sum(service_mileage) as total_mileage')])->get();
        // $total_mileage = $total_mileage[0]['total_mileage'] ? $total_mileage[0]['total_mileage'] : 0;
        // $record['total_mileage'] = $total_mileage;
        return response()->json(['success'=>'success', 'data' => $record], 200, [], JSON_NUMERIC_CHECK);
      }
    // } catch (\Exception $e) {
    //   return response()->json(['failed'=>'failed', 'message' => 'This driver doesnt assign the van number yet'], 500);
    // }
  }
}
