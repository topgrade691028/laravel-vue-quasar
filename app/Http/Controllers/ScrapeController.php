<?php

namespace App\Http\Controllers;

use App\DpdReport;
use Illuminate\Http\Request;
use App\Period;
use App\User;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ScrapeController extends Controller
{
  public function __construct()
  {
  }

  public function getDpdPeriods(Request $request)
  {
    // try {
    $currentDate = $request['currentDate'];
    $currentYear = substr($currentDate, 0, 4);

    $loginResponse = Http::post('https://drivers.dpd.co.uk/esg/user/?action=login', [
      'username' => $request['email'],
      'password' => $request['password'],
    ]);

    $sid = $loginResponse->cookies()->getCookieByName('sid')->getValue();

    $sidCookie = CookieJar::fromArray([
      'sid' => $sid
    ], 'drivers.dpd.co.uk');

    $transactionsResponse = Http::send('GET', 'https://drivers.dpd.co.uk/esg/odf/billing/cutoff/?view=Transaction&year=' . $currentYear, ['cookies' => $sidCookie]);

    $transactions = $transactionsResponse->json();
    $periods = [];
    foreach ($transactions['data'] as $transaction) {
      $period = array(
        'billingYear' => $transaction['billingYear'],
        'firstTransactionDate' => $transaction['firstTransactionDate'],
        'lastTransactionDate' => $transaction['lastTransactionDate'],
        'previousCutOffDate' => $transaction['previousCutOffDate'],
        'cutOffDate' => $transaction['cutOffDate'],
        'duration' => $transaction['firstTransactionDate'] . ' ~ ' . $transaction['lastTransactionDate']
      );
      array_push($periods, $period);
    }
    Period::where('billingYear', $currentYear)->delete();
    Period::insert($periods);
    $currentPeriods = Period::where('billingYear', $currentYear)
                            ->where('firstTransactionDate', '<=', $currentDate)
                            ->orderBy('firstTransactionDate', 'DESC')->get();

    return response()->json(['success' => 'success', 'sid' => $sid, 'data' => $currentPeriods], 200, [], JSON_NUMERIC_CHECK);
    // } catch (\Exception $e) {
    //   return response()->json(['message' => 'DPD Login is failed, Please try again', 'error' => $e], 401);
    // }
  }

  public function getPeriods(Request $request)
  {
    $currentDate = $request->query('date');
    $currentYear = substr($currentDate, 0, 4);
    $periods = Period::where('billingYear', $currentYear)->where('firstTransactionDate', '<=', $currentDate)->orderBy('firstTransactionDate', 'DESC')->get();
    return response()->json(['success' => 'success', 'data' => $periods], 200, [], JSON_NUMERIC_CHECK);
  }

  public function dpdLogin(Request $request)
  {
    // try {
    $loginResponse = Http::post('https://drivers.dpd.co.uk/esg/user/?action=login', [
      'username' => $request['email'],
      'password' => $request['password'],
    ]);
    $sid = $loginResponse->cookies()->getCookieByName('sid')->getValue();
    $startDate = $request['period']['startDate'];
    $endDate = $request['period']['endDate'];
    $previousCutOffDate = $request['period']['previousCutOffDate'];
    $cutOffDate = $request['period']['cutOffDate'];
    $this->updateDpd($sid, $startDate, $endDate, $previousCutOffDate, $cutOffDate);

    return response()->json(['success' => 'success', 'sid' => $sid], 200, [], JSON_NUMERIC_CHECK);
    // } catch (\Exception $e) {
    //   return response()->json(['message' => 'DPD Login is failed, Please try again', 'error' => $e], 401);
    // }
  }
  public function updateDpdData(Request $request)
  {
    // try {
      $sid = $request['sid'];
      $startDate = $request['period']['startDate'];
      $endDate = $request['period']['endDate'];
      $previousCutOffDate = $request['period']['previousCutOffDate'];
      $cutOffDate = $request['period']['cutOffDate'];
      $this->updateDpd($sid, $startDate, $endDate, $previousCutOffDate, $cutOffDate);
      return response()->json(['success' => 'success'], 200, [], JSON_NUMERIC_CHECK);
    // } catch (\Exception $e) {
    //   return response()->json(['message' => 'Failed to update DPD data'], 500, [], JSON_NUMERIC_CHECK);
    // }
  }

  private function updateDpd($sid, $startDate, $endDate, $previousCutOffDate, $cutOffDate)
  {
    if (Auth::guard('api')->user()) {
      $user_id = Auth::guard('api')->user()->id;
      $sidCookie = CookieJar::fromArray([
        'sid' => $sid
      ], 'drivers.dpd.co.uk');

      $applicantResponse = Http::send('GET', 'https://drivers.dpd.co.uk/esg/odf/applicant', ['cookies' => $sidCookie]);
      $applicantData = $applicantResponse->json()['data'];
      $routes = [];
      $currentRoute = array(
        'id' => $applicantData['applicantId'],
        'routeNumber' => $applicantData['franchiseeNumber']
      );
      array_push($routes, $currentRoute);

      foreach ($applicantData['otherFranchises'] as $data) {
        $route = array(
          'id' => $data['id'],
          'routeNumber' => $data['franchiseeNumber']
        );
        array_push($routes, $route);
      }

      $transactions = [];
      foreach ($routes as $route) {
        $paymentUrl = 'https://drivers.dpd.co.uk/esg/odf/review/?action=payments&applicantId=' . $route['id'] . '&startDate=' . $startDate . '&endDate=' . $endDate . '&cutOffDate=' . $cutOffDate . '&previousCutOffDate=' . $previousCutOffDate;
        $paymentResponse = Http::send('GET', $paymentUrl, ['cookies' => $sidCookie]);
        $stopTransactions = $paymentResponse->json()['data']['stopTransactions'];
        foreach ($stopTransactions as $stopTransaction) {
          $transaction = [
            'user_id' => $user_id,
            'dpdRoute' => $route['routeNumber'],
            'dpdStops' => $stopTransaction['numStops'],
            'dpdDate' => substr($stopTransaction['transactionDate'], 0, 10)
          ];
          DpdReport::firstOrCreate($transaction);
        }
      }
      // DpdReport::insert($transactions);
    }
  }

  // private function getDpd($fromDate, $endDate, $start, $numPerPage) {
  //   $user = Auth::guard('api')->user();
  //   $dpdReports = DpdReport::where('dpdDate', '>=', $fromDate)->where('dpdDate', '<=', $endDate)->where('dpd_reports.user_id', $user->id)->select(['dpdDate', DB::raw('count(dpdRoute) as route_count'), 'user_id'])->groupBy('dpdDate')->orderBy('dpdDate', 'DESC');
  //   return $dpdReports;
  // }

  public function getDpdDatas(Request $request) {
    if (Auth::guard('api')->user()) {
      $user = Auth::guard('api')->user();
      $start = $request['start'] ? $request['start'] : 0;
      $numPerPage = $request['numPerPage'] ? $request['numPerPage'] : 10;
      $fromDate = $request['fromDate'];
      $endDate = $request['endDate'];
      $dpdReports = DpdReport::where('dpdDate', '>=', $fromDate)->where('dpdDate', '<=', $endDate)->where('dpd_reports.user_id', $user->id)->select(['dpdDate', DB::raw('count(dpdRoute) as route_count'), 'user_id'])->groupBy('dpdDate')->orderBy('dpdDate', 'DESC');
      $totalCount = count($dpdReports->get());
      $dpdReports = $dpdReports->skip($start)->take($numPerPage)->get();

      if ($totalCount == 0) {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => []], 200, [], JSON_NUMERIC_CHECK);
      } else {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => $dpdReports], 200, [], JSON_NUMERIC_CHECK);
      }
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }

  public function getDPDInfo(Request $request) {
    $dpdDate = $request->query()['dpdDate'];
    try {
      $user = Auth::guard('api')->user();
      $dpdReports = DpdReport::with(['driver'])->where('user_id', $user->id)->where('dpdDate', $dpdDate)->get();
      return response()->json(['success' => 'success', 'data' => $dpdReports], 200, [], JSON_NUMERIC_CHECK);
    } catch (\Exception $e) {
      return response()->json(['error' => 'DPD Data does not exist'], 404);
    }
  }

  public function updateDPDInfo(Request $request) {
    if (Auth::guard('api')->user()) {
      $user_id = Auth::guard('api')->user()->id;
      $dpdInfo = $request['data'];
      $dpdDate = $request['conditions']['dpdDate'];
      try {
        DpdReport::whereUserId($user_id)->where('dpdDate', $dpdDate)->delete();
        foreach ($dpdInfo as $data) {
          $dpdReport = new DpdReport;
          $dpdReport->user_id = $user_id;
          $dpdReport->driver_id = $data['driver_id'];
          $dpdReport->dpdRoute = $data['dpdRoute'];
          $dpdReport->dpdStops = $data['dpdStops'];
          $dpdReport->dpdPayment = $data['dpdPayment'];
          $dpdReport->dpdPayType = $data['dpdPayType'];
          $dpdReport->dpdDate = $data['dpdDate'];
          $dpdReport->save();
        }
        return response()->json(['success' => 'success'], 200, [], JSON_NUMERIC_CHECK);
      } catch (\Exception $e) {
        return response()->json(['message' => 'Dpd Report updating is failed', 'error' => $e], 500);
      }
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
  public function removeDpdInfo (Request $request) {
    if (Auth::guard('api')->user()) {
      $user_id = Auth::guard('api')->user()->id;
        DpdReport::whereUserId($user_id)->where('dpdDate', $request['conditions']['dpdDate'])->delete();
        return response()->json(['success' => 'DPD Report Successfully Removed'], 200, [], JSON_NUMERIC_CHECK);
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }

  public function getDpdRouteAll (Request $request) {
    // $driver_id = $request['conditions']['driver_id'];
    $dpdRoutes = DpdReport::leftJoin('drivers', 'drivers.id', '=', 'dpd_reports.driver_id');
    $dpdRoutes = $dpdRoutes->where('driver_name', '!=', 'RNC');
    // $dpdRoutes = $dpdRoutes->where('dpd_reports.driver_id', $driver_id);
    $dpdRoutes = $dpdRoutes->select('dpd_reports.dpdRoute')->groupBy('dpdRoute')->orderBy('dpdRoute', 'ASC')->get();
    return response()->json(['success'=>'success', 'data' => $dpdRoutes], 200, [], JSON_NUMERIC_CHECK);
  }
  public function getDpdDriverPerformanceList (Request $request) {
    // if (Auth::guard('api')->user()) {
      $start = $request['start'] ? $request['start'] : 0;
      $numPerPage = $request['numPerPage'] ? $request['numPerPage'] : 10;
      $sortBy = $request['sortBy'] ? $request['sortBy'] : 'dpdDate';
      $desc = $request['descending'] ? 'DESC' : 'ASC';
      $fromDate = $request['fromDate'];
      $endDate = $request['endDate'];
      $isDateFilter = $request['conditions']['is_date_filter'];
      $driver_id = $request['conditions']['driver_id'];

      $dpdReports = DpdReport::with(['user'])
                ->leftJoin('drivers', 'drivers.id', '=', 'dpd_reports.driver_id');
      if ($isDateFilter) {
        $dpdReports = $dpdReports->where('dpdDate', '>=', $fromDate)->where('dpdDate', '<=', $endDate);
      }
      
      if ($request['conditions'] && isset($request['conditions']['dpdRoute'])) {
        $dpdRoute = $request['conditions']['dpdRoute'];
        $dpdReports = $dpdReports->where('dpdRoute', $dpdRoute);
      }

      $dpdReports = $dpdReports->where('driver_name', '!=', 'RNC');
      $dpdReports = $dpdReports->where('dpd_reports.driver_id', $driver_id);
      $dpdReports = $dpdReports->select('dpd_reports.id', 'dpd_reports.driver_id', 'dpdDate', 'drivers.driver_name', 'dpdRoute', 'dpdStops', 'dpdPayment', 'dpdPayType', 'dpd_reports.created_at', 'dpd_reports.user_id')->orderBy($sortBy, $desc);
      $totalCount = count($dpdReports->get());
      $dpdReports = $dpdReports->skip($start)->take($numPerPage)->get();

      if ($totalCount == 0) {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => []], 200, [], JSON_NUMERIC_CHECK);
      } else {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => $dpdReports], 200, [], JSON_NUMERIC_CHECK);
      }
    // } else {
    //   return response()->json(['failed'=>'failed'], 401);
    // }
  }
  public function getDpdDriverPerformanceAll (Request $request) {
    // if (Auth::guard('api')->user()) {
      $sortBy = $request['sortBy'] ? $request['sortBy'] : 'dpdDate';
      $desc = $request['descending'] ? 'DESC' : 'ASC';
      $fromDate = $request['fromDate'];
      $endDate = $request['endDate'];
      $isDateFilter = $request['conditions']['is_date_filter'];
      $driver_id = $request['conditions']['driver_id'];

      $dpdReports = DpdReport::with(['user'])
                ->leftJoin('drivers', 'drivers.id', '=', 'dpd_reports.driver_id');
      if ($isDateFilter) {
        $dpdReports = $dpdReports->where('dpdDate', '>=', $fromDate)->where('dpdDate', '<=', $endDate);
      }
      
      if ($request['conditions'] && isset($request['conditions']['dpdRoute'])) {
        $dpdRoute = $request['conditions']['dpdRoute'];
        $dpdReports = $dpdReports->where('dpdRoute', $dpdRoute);
      }

      $dpdReports = $dpdReports->where('driver_name', '!=', 'RNC');
      $dpdReports = $dpdReports->where('dpd_reports.driver_id', $driver_id);
      $dpdReports = $dpdReports->select('dpd_reports.id', 'dpd_reports.driver_id', 'dpdDate', 'drivers.driver_name', 'dpdRoute', 'dpdStops', 'dpdPayment', 'dpdPayType', 'dpd_reports.created_at', 'dpd_reports.user_id')->orderBy($sortBy, $desc)->get();
      $totalCount = count($dpdReports);

      if ($totalCount == 0) {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => []], 200, [], JSON_NUMERIC_CHECK);
      } else {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => $dpdReports], 200, [], JSON_NUMERIC_CHECK);
      }
    // } else {
    //   return response()->json(['failed'=>'failed'], 401);
    // }
  }
  
  public function getDpdPerformanceList (Request $request) {
    if (Auth::guard('api')->user()) {
      $user = Auth::guard('api')->user();
      $start = $request['start'] ? $request['start'] : 0;
      $numPerPage = $request['numPerPage'] ? $request['numPerPage'] : 10;
      $sortBy = $request['sortBy'] ? $request['sortBy'] : 'dpdDate';
      $desc = $request['descending'] ? 'DESC' : 'ASC';
      $fromDate = $request['fromDate'];
      $endDate = $request['endDate'];
      $isDateFilter = $request['conditions']['is_date_filter'];

      $dpdReports = DpdReport::with(['user'])
                ->leftJoin('drivers', 'drivers.id', '=', 'dpd_reports.driver_id');
      if ($isDateFilter) {
        $dpdReports = $dpdReports->where('dpdDate', '>=', $fromDate)->where('dpdDate', '<=', $endDate);
      }
      
      if ($request['conditions'] && isset($request['conditions']['driver_name'])) {
        $driver_name = $request['conditions']['driver_name'];
        $dpdReports = $dpdReports->where('driver_name', $driver_name);
      }
      if ($request['conditions'] && isset($request['conditions']['dpdRoute'])) {
        $dpdRoute = $request['conditions']['dpdRoute'];
        $dpdReports = $dpdReports->where('dpdRoute', $dpdRoute);
      }
      // if ($request['conditions'] && isset($request['conditions']['filter'])) {
      //   $search = $request['conditions']['filter'];
      //   $dpdReports = $dpdReports->where(function($q) use ($search) {
      //     $q->orWhere('driver_name', 'like', '%' . $search . '%')
      //       ->orWhere('dpdRoute', 'like', '%' . $search . '%');
      //   });
      // }
      $dpdReports = $dpdReports->where('driver_name', '!=', 'RNC');
      $dpdReports = $dpdReports->where('dpd_reports.user_id', $user->id);
      $dpdReports = $dpdReports->select('dpd_reports.id', 'dpd_reports.driver_id', 'dpdDate', 'drivers.pay_type', 'drivers.driver_name', 'dpdRoute', 'dpdStops', 'dpdPayment', 'dpdPayType', 'dpd_reports.created_at', 'dpd_reports.user_id')->orderBy($sortBy, $desc);
      $totalCount = count($dpdReports->get());
      $dpdReports = $dpdReports->skip($start)->take($numPerPage)->get();

      if ($totalCount == 0) {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => []], 200, [], JSON_NUMERIC_CHECK);
      } else {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => $dpdReports], 200, [], JSON_NUMERIC_CHECK);
      }
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }

  public function getDpdPerformanceAll (Request $request) {
    if (Auth::guard('api')->user()) {
      $user = Auth::guard('api')->user();
      $sortBy = $request['sortBy'] ? $request['sortBy'] : 'dpdDate';
      $desc = $request['descending'] ? 'DESC' : 'ASC';
      $fromDate = $request['fromDate'];
      $endDate = $request['endDate'];
      $isDateFilter = $request['conditions']['is_date_filter'];

      $dpdReports = DpdReport::with(['user'])
                ->leftJoin('drivers', 'drivers.id', '=', 'dpd_reports.driver_id');
      if ($isDateFilter) {
        $dpdReports = $dpdReports->where('dpdDate', '>=', $fromDate)->where('dpdDate', '<=', $endDate);
      }
      if ($request['conditions'] && isset($request['conditions']['filter'])) {
        $search = $request['conditions']['filter'];
        $dpdReports = $dpdReports->where(function($q) use ($search) {
          $q->orWhere('driver_name', 'like', '%' . $search . '%')
            ->orWhere('dpdRoute', 'like', '%' . $search . '%');
        });
      }
      $dpdReports = $dpdReports->where('driver_name', '!=', 'RNC');
      $dpdReports = $dpdReports->where('dpd_reports.user_id', $user->id);
      $dpdReports = $dpdReports->select('dpd_reports.id', 'dpd_reports.driver_id', 'dpdDate', 'drivers.driver_name', 'dpdRoute', 'dpdStops', 'dpdPayment', 'dpdPayType', 'dpd_reports.created_at', 'dpd_reports.user_id')->orderBy($sortBy, $desc)->get();
      $totalCount = count($dpdReports);

      if ($totalCount == 0) {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => []], 200, [], JSON_NUMERIC_CHECK);
      } else {
        return response()->json(['success'=>'success', 'totalCount' => $totalCount, 'data' => $dpdReports], 200, [], JSON_NUMERIC_CHECK);
      }
    } else {
      return response()->json(['failed'=>'failed'], 401);
    }
  }
}
