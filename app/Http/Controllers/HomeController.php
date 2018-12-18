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
                $days[] = [$data['start']->format('d'),2, "full" => $data['start']->format('Y-m-d')];
                $data['start']->addDay();
                continue;
            }
            if($data['start']->format('Y-m-d') >= $today5->format('Y-m-d') && $data['start']->format('Y-m-d') <= $end_of_period) {
                $days[] = [$data['start']->format('d'),0, "full" => $data['start']->format('Y-m-d')];
            }
            else {
                $days[] = [$data['start']->format('d'),1, "full" => $data['start']->format('Y-m-d')];
            }
            
            
            $data['start']->addDay();
        }
        $data['days_a'] = $days;

        $data['days'] = [__('Su'),__('Mo'),__('Tu'),__('We'),__('Th'),__('Fr'),__('Sa')];
        $time_line = [];
        $time = new Carbon('07:00');
        while($time->format('H:i') <= '19:00') {
            $time_line[] = $time->format('H:i');
            $time->addMinutes(40);
        }
        $data['time_line'] = $time_line;

        $user = auth()->user();
        if(isset($user))
            if($user->is_admin)
                return view('admin.home');
            else
                return view('home', $data);
    }

    public function getLessons(Request $request) {
        $lessons = Lesson::where("user_id",Auth::user()->id)->where("date", $request->day)->pluck('time')->toArray();
        $taken_lessons = Lesson::where("date", $request->day)->pluck('time')->toArray();
        $taken_lessons_by_user = Lesson::where("date", $request->day)->get();
        $time_line = [];
        $time = new Carbon('07:00');
        while($time->format('H:i') <= '19:00') {
            if(in_array($time->format('H:i:s'), $taken_lessons)) {
                $by_user = false;
                foreach ($taken_lessons_by_user as $lesson) {
                    if($lesson->user_id == Auth::user()->id && $time->format('H:i:s') == $lesson->time) {
                        $by_user = true;
                        break;
                    }
                }
                $time_line[] = [ $time->format('H:i'), 2, $by_user?1:0 ];
            }
            else if(in_array($time->format('H:i:s'), $lessons)) {
                $time_line[] = [ $time->format('H:i'), 1 ];
            }
            else { 
                $time_line[] = [ $time->format('H:i'), 0 ];
            }
            $time->addMinutes(40);
        }
        return response()->json([ 'data' => $time_line ]);
    }

    public function setLessons(LessonRequest $request) {
        $date_n_times = $request->date_n_times;
        $date = array_shift($date_n_times);
        $times = $date_n_times;

        foreach($times as $time) {
            Lesson::create([
                'user_id' => Auth::user()->id,
                'date' => $date,
                'time' => $time
            ]);
            
        }
        return Response::json(array('success'=>true));
    }

    public function changeStatus()
    {
        return 1;
        $company->settings->use_global_fee == 1 ? $company->settings->use_global_fee = 0 : $company->settings->use_global_fee = 1;
        $company->settings->save();

        return $company->settings->use_global_fee;
    }

    public function isLessonFree(LessonRequest $request) {
        if(!Lesson::where('date', $request->date_n_times[0])->where('time', $request->date_n_times[1])->exists())
            return ['status' => 'yes'];
        return ['status' => 'no'];
    }
}
