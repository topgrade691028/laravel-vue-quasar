<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Auth;
use Response;
use Illuminate\Support\Facades\Input;
use App\Report;
use App\Driver;
use App\Route;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
  public function __construct()
  {
    // $this->middleware('driverAuth');
  }
  public function login()
  {
    $credentials = [
      'email' => request('email'),
      'password' => request('password')
    ];

    if (Auth::guard('driver')->validate($credentials)) {
      $driver = Auth::guard('driver')->getLastAttempted();
      // if ($driver->has_access) {
        Auth::guard('driver')->login($driver);
        $driver = Driver::with(['user'])->find(Auth::guard('driver')->id());
        return response()->json($this->successResponse($driver), 200, [], JSON_NUMERIC_CHECK);
      // } else {
      //   return response()->json([
      //     'error' => 'Unauthorised',
      //     'message' => 'You dont have permission to login'
      //   ], 401);
      // }
    } else {
      return response()->json([
        'error' => 'Unauthorised',
        'message' => 'Wrong E-Mail or Password.'
      ], 404);
    }
  }
  private function successResponse(Driver $driver)
  {
    $freshToken = $driver->createToken('MyApp');
    $success['driver'] = $driver;
    $success['token'] = $freshToken->accessToken;
    $success['expiresAt'] = $freshToken->token->expires_at;

    return $success;
  }
  //
  public function getRNCID(Request $request) {
    if (Auth::user()) {
      $user_id = Auth::user()->id;
      $rnc = Driver::whereUserId($user_id)->where('driver_name', 'RNC')->where('deleted_date', NULL)->get();
      if (count($rnc)) {
        return response()->json(['success' => 'success', 'data'=>$rnc[0]->id], 200, [], JSON_NUMERIC_CHECK);
      } else {
        $rnc = new Driver;
        $rnc->driver_name = 'RNC';
        $rnc->user_id = $user_id;
        $rnc->save();
        return response()->json(['success' => 'success', 'data'=>$rnc->id], 200, [], JSON_NUMERIC_CHECK); 
      }
    }
  }

  public function getDriversByUserID(Request $request) {
    if (Auth::user()) {
      // $report_datetime = $request->query()['report_datetime'];
      // if (!isset($report_datetime)) $report_datetime = date('Y-m-d h:i:s');
      $user = Auth::user();

      if ($user->user_type == '0') {
        $drivers = Driver::where('deleted_date', NULL)->get();
      } else {
        $drivers = Driver::whereUserId($user->id);
        $drivers = $drivers->where(function($q) {
          $q->orWhere('deleted_date', NULL)->orWhere('deleted_date', '');
        })->get();
      }
      return response()->json(['success'=>'success', 'data' => $drivers], 200, [], JSON_NUMERIC_CHECK);
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }

  public function getDriverAll(Request $request) {
    if (Auth::user()) {
      $updated_at = $request->query()['updated_at'];
      if (!isset($updated_at)) $updated_at = date('Y-m-d h:i:s');
      $user = Auth::user();

      if ($user->user_type == '0') {
        $drivers = Driver::where(function($q) use ($updated_at) {
          $q->orWhere('deleted_date', NULL)->orWhere('deleted_date', '')
            ->orWhere('deleted_date', '>', $updated_at);
        })->get();
      } else {
        $drivers = Driver::whereUserId($user->id);
        $drivers = $drivers->where(function($q) use ($updated_at) {
          $q->orWhere('deleted_date', NULL)->orWhere('deleted_date', '')
            ->orWhere('deleted_date', '>', $updated_at);
        })->get();
      }
      return response()->json(['success'=>'success', 'data' => $drivers], 200, [], JSON_NUMERIC_CHECK);
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
  
  public function getDrivers (Request $request) {
    if (Auth::user()) {
      $user = Auth::user();
      $start = $request['start'] ? $request['start'] : 0;
      $numPerPage = $request['numPerPage'] ? $request['numPerPage'] : 10;
      $sortBy = $request['sortBy'] ? $request['sortBy'] : 'driver_name';
      $desc = $request['descending'] ? 'DESC' : 'ASC';

      $drivers = Driver::with(['user']);

      if ($user->user_type != '0') {
        $drivers = $drivers->whereUserId($user->id);
      }
      if ($request['conditions'] && isset($request['conditions']['driver_name'])) {
        $driver_name = $request['conditions']['driver_name'];
        $drivers = $drivers->where('driver_name', $driver_name);
      }
      $drivers = $drivers->where('deleted_date', NULL)->where('driver_name', '<>', 'RNC');
      $totalCount = count($drivers->get());
      $drivers = $drivers->orderBy($sortBy, $desc)->skip($start)->take($numPerPage)->get();

      if ($totalCount == 0) {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => []], 200, [], JSON_NUMERIC_CHECK);
      } else {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => $drivers], 200, [], JSON_NUMERIC_CHECK);
      }
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
  public function createDriver (Request $request) {
    if (Auth::user()) {
      $user_id = Auth::user()->id;
      $driverInfo = $request->all();
      $validator = Validator::make($request->all(), [
        'driver_name' => 'required',
        'email' => 'required|email',
        'password' => 'required',
      ]);

      if ($validator->fails()) {
        return response()->json(['message'=>'Driver Info is not correct, Please check again','error' => $validator->errors()], 500);
      }
      try {
        $existing_driver = Driver::where('email', $driverInfo['email'])->where('deleted_date', NULL)->get();
        if (count($existing_driver)) {
          return response()->json(['message' => 'Driver already exists'], 409);
        } else {
          $driverInfo['password'] = bcrypt($driverInfo['password']);
          $driverInfo['user_id'] = $user_id;
          $driver = Driver::create($driverInfo);
          return response()->json(['success' => 'success', 'data' => $driver], 200, [], JSON_NUMERIC_CHECK);
        }
      } catch (\Exception $e) {
        return response()->json(['message' => 'Cant add Driver', 'error' => $e], 500);
      }
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
  public function updateDriver (Request $request) {
    if (Auth::user()) {
      $driverInfo = $request['data'];
      $user_id = Auth::user()->id;
      $driverID = $request['conditions']['id'];
      try {
        $driver = Driver::find($driverID);
        if (isset($driverInfo['password'])) {
          $driverInfo['password'] = bcrypt($driverInfo['password']);
        }
        $driver->update($driverInfo);
        return response()->json(['success' => 'success', 'data' => $driver], 200, [], JSON_NUMERIC_CHECK);
      } catch (\Exception $e) {
        return response()->json(['message' => 'Driver updating is failed', 'error' => $e], 500);
      }
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
  public function removeDriver (Request $request) {
    if (Auth::user()) {
      $driverID = $request['conditions']['id'];
      try {
        $driver = Driver::find($driverID);
        $driver->deleted_date = date("Y-m-d h:i:s");
        $driver->save();
        return response()->json(['success' => 'Driver Successfully Removed']);
      } catch (\Exception $e) {
        return response()->json(['error' => 'Driver Remove Failed']);
      }
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
  public function getDriverDetails(Request $request) {
    $driver_id = $request->query()['id'];
    try {
      $driver = Driver::find($driver_id);
      return response()->json(['success'=>'success', 'driver' => $driver], 200, [], JSON_NUMERIC_CHECK);
    } catch (\Exception $e) {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
  public function getMonthlyReportsByDriver (Request $request) {
    // if (Auth::guard('driver')->user()) {
      $start = $request['start'] ? $request['start'] : 0;
      $numPerPage = $request['numPerPage'] ? $request['numPerPage'] : 10;
      $sortBy = $request['sortBy'] ? 'reports.'.$request['sortBy'] : 'reports.created_at';
      $desc = $request['descending'] ? 'DESC' : 'ASC';
      $fromDate = $request['fromDate'];
      $endDate = $request['endDate'];
      $isDateFilter = $request['conditions']['is_date_filter'];
      $driver_id = $request['conditions']['driver_id'];

      $reports = Report::with(['user'])->leftJoin('drivers', 'drivers.id', '=', 'reports.driver_id')->leftJoin('routes', 'routes.id', '=', 'reports.route_id');
      if ($isDateFilter) {
        $reports = $reports->where('reports.report_date', '>=', $fromDate)->where('reports.report_date', '<=', $endDate);
      }
      if ($request['conditions'] && isset($request['conditions']['route_number'])) {
        $route_number = $request['conditions']['route_number'];
        $reports = $reports->where('route_number', $route_number);
      }
      $reports = $reports->where('driver_name', '!=', 'RNC');
      $reports = $reports->where('reports.driver_id', $driver_id);
      $reports = $reports->select('reports.*', 'drivers.driver_name', 'drivers.pay_type', 'drivers.pay_amount', 'routes.route_number', 'is_group', 'reports.created_at', 'reports.user_id')->orderBy($sortBy, $desc);
      $totalCount = count($reports->get());
      $reports = $reports->skip($start)->take($numPerPage)->get();

      if ($totalCount == 0) {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => []], 200, [], JSON_NUMERIC_CHECK);
      } else {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => $reports], 200, [], JSON_NUMERIC_CHECK);
      }
    // } else {
    //   return response()->json(['failed'=>'failed'], 401);
    // }
  }
  public function getMonthlyAllByDriver (Request $request) {
    $sortBy = $request['sortBy'] ? 'reports.'.$request['sortBy'] : 'reports.created_at';
    $desc = $request['descending'] ? 'DESC' : 'ASC';
    $fromDate = $request['fromDate'];
    $endDate = $request['endDate'];
    $isDateFilter = $request['conditions']['is_date_filter'];
    $driver_id = $request['conditions']['driver_id'];

    $reports = Report::with(['user'])->leftJoin('drivers', 'drivers.id', '=', 'reports.driver_id')->leftJoin('routes', 'routes.id', '=', 'reports.route_id');
    if ($isDateFilter) {
      $reports = $reports->where('reports.report_date', '>=', $fromDate)->where('reports.report_date', '<=', $endDate);
    }
    // if ($request['conditions'] && isset($request['conditions']['route_number'])) {
    //   $route_number = $request['conditions']['route_number'];
    //   $reports = $reports->where('route_number', $route_number);
    // }
    $reports = $reports->where('driver_name', '!=', 'RNC');
    $reports = $reports->where('reports.driver_id', $driver_id);
    $reports = $reports->select('reports.id', 'reports.driver_id', 'reports.route_id', 'reports.report_title', 'reports.report_date', 'reports.report_no', 'drivers.driver_name', 'drivers.pay_amount', 'routes.route_number', 'is_group', 'reports.created_at', 'reports.user_id')->orderBy($sortBy, $desc)->get();
    $totalCount = count($reports);

    if ($totalCount == 0) {
      return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => []], 200, [], JSON_NUMERIC_CHECK);
    } else {
      return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => $reports], 200, [], JSON_NUMERIC_CHECK);
    }
  }
  public function getRouteAllByDriver (Request $request) {
    $driver_id = $request['conditions']['driver_id'];

    $routes = Report::leftJoin('routes', 'routes.id', '=', 'reports.route_id');
    $routes = $routes->where('reports.driver_id', $driver_id);
    $routes = $routes->select('routes.route_number')->groupBy('route_number')->orderBy('route_number', 'ASC')->get();

    return response()->json(['success'=>'success', 'data' => $routes], 200, [], JSON_NUMERIC_CHECK);
  }
}
