<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Carbon::setWeekStartsAt(0);
        $data['now'] = (new Carbon('today'))->format('d');
        $data['month_first_day'] = (new Carbon('first day of this month'))->format('d');
        $data['this_month_week_starts'] = (new Carbon('first day of this month'))->startOfWeek()->format('d');
        $data['month_last_day'] = (new Carbon('last day of this month'))->format('d');
        $data['last_month_last_day'] = (new Carbon('last day of last month'))->format('d');
        $data['first_day_of_week'] = Carbon::now()->startOfWeek()->format('d');
        $data['days_in_month'] = Carbon::now()->daysInMonth;

        $data['diff_last_this_in_week'] = $data['last_month_last_day'] - $data['this_month_week_starts'] + 1;

        $data['start'] = 1;
        if($data['now'] > 15)
            $data['start'] = 16;
        
        $data['end'] = 15;
        if($data['start'] == 16)
            $data['end'] = $data['month_last_day'];

        $data['permitted_weeks'] = Auth::user()->getPermittedWeeks() + 1;
        $displayed_rows = ceil($data['days_in_month']/7);

        $days = array();
        if($data['this_month_week_starts'] != 1) {
            for($i = $data['this_month_week_starts'];$i <= $data['last_month_last_day']; $i++) {
                $days[] = [$i,1];
            }
        }
        for($i = 1; $i <= $data['month_last_day']; $i++) {
            if($i < $data['now'])
                $days[] = [$i,1];
            else
                $days[] = [$i,0];
        }
        
        if($filled_days = count($days) % 7) {
            for($i = 1;$i <= (7 - $filled_days); $i++) {
                $days[] = [$i,1];
            }
        }
        $data['days_a'] = $days;

        $data['days'] = ['su','mo','tu','we','th','fr','sa'];
        $user = auth()->user();
        if(isset($user))
            if($user->is_admin)
                return view('admin.home');
            else
                return view('home', $data);
    }
}
