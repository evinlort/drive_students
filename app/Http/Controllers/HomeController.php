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

        $data['days'] = [__('Su'),__('Mo'),__('Tu'),__('We'),__('Th'),__('Fr'),__('Sa')];
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
