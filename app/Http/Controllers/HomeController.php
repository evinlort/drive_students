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
        $settings = Auth::user()->settings;
        Carbon::setWeekStartsAt(0);
        Carbon::setWeekEndsAt(6);

        $today = new Carbon('2018-05-10');
        $today2 = new Carbon('2018-05-10');

/* 
        // From today to plus settings weeks.
        $data['now'] = $today->format('d');
        $data['month_first_day'] = $today->format('d');
        $data['this_month_week_starts'] = $today2->startOfWeek()->format('d');
        $data['next_week_after_this'] = $today2->addDays(7)->format('d');
        $data['date_after_add_weeks'] = $today2->addDays(7 * ($settings->weeks - 1))->format('d');
        $data['end_of_week_with_add'] = $today2->endOfWeek()->format('d');
        $data['end_of_this_month'] = $today->endOfMonth()->format('d');
        $data['last_month_last_day'] = $today->subDay()->subMonth()->endOfMonth()->format('d');
        $data['days_in_month'] = $today->daysInMonth;
 */

        // From 1 of 15 plus settings weeks - 2(weeks)
        $data['now'] = $today->format('d');
        $data['month_first_day'] = $today->format('d');
        $data['this_month_weeks_starts'] = $data['now'] > 15?16:1;
        $data['this_month_weeks_ends'] = $data['now'] > 15?$today->endOfMonth()->format('d'):15;
        $data['this_month_week_starts'] = $today->startOfMonth()->startOfWeek()->format('d');
        if($data['now'] <= 15) {
            $today2->startOfMonth()->addDays(14)->format('d');
        }
        else {
            $today2->endOfMonth()->format('d');
        }
        $data['date_after_add_weeks'] = $today2->addDays((7 * ($settings->weeks - 2)))->format('d');
        $data['end_of_week_with_add'] = $today2->endOfWeek()->format('d');

        $data['end_of_this_month'] = $today->endOfMonth()->format('d');
        $data['last_month_last_day'] = $today->subDay()->subMonth()->endOfMonth()->format('d');
        $data['days_in_month'] = $today->daysInMonth;

        $data['start'] = 1;
        if($data['now'] > 15)
            $data['start'] = 16;
        
        $data['end'] = 15;
        if($data['start'] == 16)
            $data['end'] = $data['end_of_this_month'];

        $days = array();
        if($data['this_month_week_starts'] != 1) {
            for($i = $data['this_month_week_starts'];$i <= $data['end_of_this_month']; $i++) {
                $days[] = [$i,1];
            }
        }
        for($i = 1; $i <= $data['end_of_this_month']; $i++) {
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

        $data['days'] = [__('Su'),__('Mo'),__('Tu'),__('We'),__('Th'),__('Fr'),__('Sa')];
        // dd($data);
        $user = auth()->user();
        if(isset($user))
            if($user->is_admin)
                return view('admin.home');
            else
                return view('home', $data);
    }

    public function getLessons(Request $request) {
        $day = $request->day;
        $date = date('Y-m-').$day;
        $times = array();
        $step_time = Carbon::createFromTime(7)->toTimeString();
        $counter = 1;
        while(Carbon::createFromTime(19)->toTimeString() >= $step_time) {
            $times[] = [$counter++ => $step_time];
            list($h,$m,$s) = explode(':',$step_time);
            $step_time = Carbon::createFromTime($h,$m,$s)->addMinutes(40)->toTimeString();
        }
        return response()->json([ 'data' => $times ]);
    }
}
