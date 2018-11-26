<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

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

        $data['days'] = ['su','mo','th','te','we','st','sb'];
        $user = auth()->user();
        if(isset($user))
            if($user->is_admin)
                return view('admin.home');
            else
                return view('home', $data);
    }
}
