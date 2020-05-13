<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Lesson;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\LessonRequest;

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
        $user = Auth::user();
        $data['user'] = $user;
        $settings = $user->settings;
        Carbon::setWeekStartsAt(0);
        Carbon::setWeekEndsAt(6);

        // TODO: get this from config
        $choose_start = 'now';
        $half_day_holidays = 5;
        $holidays = [6];
        $start_time = '05:00';
        $end_time = '21:00';

        $today = new Carbon($choose_start);
        $today2 = new Carbon($choose_start);
        $today3 = new Carbon($choose_start);
        $today4 = new Carbon($choose_start);
        $today5 = new Carbon($choose_start);
        $today6 = new Carbon($choose_start);

        $data['start'] = $today6->startOfWeek();

        $days = array();
        $days_to_add = 7 * ($settings->weeks - 2);
        $end = $today2->
            startOfWeek()->
            setDate($today->year,$today->month,$today->format('d') > 15?
                $today3->endOfMonth()->format('d'):
                15
            )->
            addDays($days_to_add);
        
        $end_of_period = $end->format('Y-m-d');
        $data['end'] = $end->endOfWeek();
        
        while($data['start']->format('Y-m-d') <= $data['end']->format('Y-m-d')) {
            $has_user_lessons = Lesson::where('user_id', $user->id)->where('date', $data['start']->format('Y-m-d'))->count('user_id');//false;
            // if(Lesson::where('user_id', $user->id)->where('date', $data['start']->format('Y-m-d'))->count('user_id')) {
            //     $has_user_lessons = true;
            // }

            if(in_array($data['start']->dayOfWeek, $holidays)) {
                $days[] = [$data['start']->format('d'),2, "full" => $data['start']->format('Y-m-d'), $has_user_lessons];
                $data['start']->addDay();
                continue;
            }
            if($data['start']->dayOfWeek == $half_day_holidays) {
                if($data['start']->format('Y-m-d') >= $today5->format('Y-m-d') && $data['start']->format('Y-m-d') <= $end_of_period) {
                    $days[] = [$data['start']->format('d'),3, "full" => $data['start']->format('Y-m-d'), $has_user_lessons];
                    $data['start']->addDay();
                }
                else {
                    $days[] = [$data['start']->format('d'),1, "full" => $data['start']->format('Y-m-d'), $has_user_lessons];
                    $data['start']->addDay();
                }
                continue;
            }
            if($data['start']->format('Y-m-d') >= $today5->format('Y-m-d') && $data['start']->format('Y-m-d') <= $end_of_period) {
                $days[] = [$data['start']->format('d'),0, "full" => $data['start']->format('Y-m-d'), $has_user_lessons];
            }
            else {
                $days[] = [$data['start']->format('d'),1, "full" => $data['start']->format('Y-m-d'), $has_user_lessons];
            }
            
            $data['start']->addDay();
        }
        $data['days_a'] = $days;

        $data['days'] = [__('Su'),__('Mo'),__('Tu'),__('We'),__('Th'),__('Fr'),__('Sa')];
        $time_line = [];
        $time = new Carbon($start_time);
        while($time->format('H:i') <= $end_time) {
            $time_line[] = $time->format('H:i');
            $time->addMinutes(40);
        }
        $data['time_line'] = $time_line;

        $user = auth()->user();
        if(isset($user) && isset($user->settings))
            if($user->settings->is_admin)
                return view('admin.home');
            else
                return view('home', $data);
    }
}
