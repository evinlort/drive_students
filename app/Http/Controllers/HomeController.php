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

        $choose_start = 'now';
        $today = new Carbon($choose_start);
        $today2 = new Carbon($choose_start);
        $today3 = new Carbon($choose_start);
        $today4 = new Carbon($choose_start);
        $today5 = new Carbon($choose_start);

        $holidays = [5,6];

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

        $data['start'] = $today->startOfWeek();

        $days = array();
        $days_to_add = 7 * ($settings->weeks - 2);
        $end = $today2->startOfWeek()->setDate($today->year,$today->month,$today->format('d') > 15?$today3->endOfMonth()->format('d'):15)->addDays($days_to_add);
        
        $end_of_period = $end->format('Y-m-d');
        $data['end'] = $end->endOfWeek();
        while($data['start']->format('Y-m-d') <= $data['end']->format('Y-m-d')) {
            if(in_array($data['start']->dayOfWeek, $holidays)) {
                $days[] = [$data['start']->format('d'),2];
                $data['start']->addDay();
                continue;
            }
            if($data['start']->format('Y-m-d') >= $today5->format('Y-m-d') && $data['start']->format('Y-m-d') <= $end_of_period) {
                $days[] = [$data['start']->format('d'),0];
            }
            else {
                $days[] = [$data['start']->format('d'),1];
            }
            
            
            $data['start']->addDay();
        }
        $data['days_a'] = $days;

        // From 1 of 15 plus settings weeks - 2(weeks)
        /* $data['now'] = $today->format('d');
        $data['month_first_day'] = $today->format('d');
        $data['this_month_weeks_starts'] = $data['now'] > 15?16:1;
        $data['this_month_weeks_ends'] = $data['now'] > 15?$today->endOfMonth()->format('d'):15;
        $week_starts = (new Carbon($choose_start))->startOfWeek();
        $data['this_month_week_starts'] = $week_starts->format('d');
        $data['this_month_week_start_number'] = $week_starts->weekOfYear;
        $days_to_add = 7 * ($settings->weeks - 2);
        $data['days_to_add'] = $days_to_add;
        $data['passed_days'] = $data['this_month_weeks_ends'] - $data['this_month_weeks_starts'] + $days_to_add;
        if($data['now'] <= 15) {
            $end = $today2->startOfMonth()->addDays(14)->format('d');
        }
        else {
            $end = $today2->endOfMonth()->format('d');
        }
        $data['last_month_last_day'] = $today->subDay()->subMonth()->endOfMonth()->format('d');
        $data['from_last_month_week_to_end_of_last_month'] = $data['last_month_last_day'] - $data['this_month_week_starts'];
        $full_registration_time = $end + $days_to_add + $data['from_last_month_week_to_end_of_last_month'];
        $data['full_registration_time'] = $full_registration_time;
        $data['date_after_add_weeks'] = $today2->addDays($days_to_add)->format('d');

        $end_of_week = $today2->endOfWeek();
        $data['end_of_week_with_add'] = $end_of_week->format('d');
        $data['end_of_week_with_add_number'] = $end_of_week->weekOfYear;
        $data['end_of_this_month'] = $today->endOfMonth()->format('d');
        
        $data['days_in_month'] = $today->daysInMonth;

        $data['total_runs'] = $data['end_of_week_with_add_number'] - $data['this_month_week_start_number'];

        // $data['end_of_week_with_add_numeric'] = $data['end_of_this_month'] + $data['end_of_week_with_add'];
        $data['end_of_week_with_add_numeric'] = $data['this_month_weeks_ends'] + $days_to_add;
        $data['start'] = 1;
        if($data['now'] > 15)
            $data['start'] = 16;
        
        $data['end'] = 15;
        if($data['start'] == 16)
            $data['end'] = $data['end_of_this_month'];

        $days = array();
        // if($data['this_month_week_starts'] != 1) {
            $holiday_checker = (new Carbon($choose_start))->startOfWeek();
            for($i = 0,$t = $data['this_month_week_starts'];$i <= $full_registration_time+1; $i++) {
                if($t > $data['end_of_this_month'])
                    $t = 1;
                if(in_array($holiday_checker->dayOfWeek, $holidays))
                    $days[] = [$t++,2];
                else if($i >= $data['now'] && $i <= $full_registration_time)
                    $days[] = [$t++,0];
                else
                    $days[] = [$t++,1];
                $holiday_checker->addDay();
            }
        // }
        // for($i = 1; $i <= $data['end_of_this_month']; $i++) {
        //     if($i < $data['now'])
        //         $days[] = [$i,1];
        //     else
        //         $days[] = [$i,0];
        // }
        
        // if($filled_days = count($days) % 7) {
        //     for($i = 1;$i <= (7 - $filled_days); $i++) {
        //         $days[] = [$i,1];
        //     }
        // }

        $data['days_a'] = $days; */

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
