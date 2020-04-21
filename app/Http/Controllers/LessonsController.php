<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\LessonRequest;
use Illuminate\Support\Facades\Auth;

class LessonsController extends Controller
{
    private $date_range_start;
    private $date_range_end;
    private $choose_start;
    private $holidays;
    private $user;

    public function __construct() {
        $this->middleware(['auth']);
        $this->choose_start = 'now';
        $this->holidays = array(5,6);
    }

    public function setLessons(Request $request) {
        $user = User::where('id', $request->user_id)->first();
        // TODO: get from config
        $half_day_end_time = '17:00';
        $half_day_holiday = 5;

        $date_n_times = $request->date_n_times;
        $date = array_shift($date_n_times);
        $times = $date_n_times;
        $some_is_not_saved = '';
        $busy_times = array();

        foreach($times as $time) {
            if((new Carbon($date))->dayOfWeek == $half_day_holiday) {
                if($time > $half_day_end_time) {
                    $some_is_not_saved .= __('Choosen time not in range', ['time' => $time]);
                    Log::channel('single')->warning(
                        'User with ID:'.$user->id.' (name: '.$user->name.') with IP: '.request()->ip().', tried to order lesson that not in range. Params - date: '.
                        $date.' , time: '.$time
                    );
                    continue;
                }
            }

            if(!Lesson::where('date',$date)->where('time', $time)->exists()) {
                Lesson::create([
                    'user_id' => $user->id,
                    'date' => $date,
                    'time' => $time
                ]);
            }
            else {
                $some_is_not_saved .= __('Someone took lessons on just before you', ['time' => $time]);
                $busy_times[] = $time;
            }
        }
        return ['success'=>true, 'message' => $some_is_not_saved, 'busy' => $busy_times];
    }

    public function isHasFreeLessons(Request $request) {
        $checked = $request->checked_lessons;
        $date = array_shift($checked);
        $choosen_lessons = count($checked);

        foreach ($checked as $lesson_time) {
            if(Lesson::where('date', $date)->where('time', $lesson_time)->exists()) {
                return ['status' => false, 'error' => __('One or more of choosen lessons are already taken')];
            }
        }
        $this->get_dates_range();
        $taken_by_student_for_date_range = Lesson::where('user_id', Auth::user()->id)
            ->whereBetween('date', array($this->date_range_start->format('Y-m-d'), $this->date_range_end->format('Y-m-d')))
            ->count();
        $available_lessons = Auth::user()->settings->lessons - $taken_by_student_for_date_range;
        if($available_lessons - $choosen_lessons < 0) {
            Log::channel('single')->warning(
                'User with ID:'.Auth::user()->id.' (name: '.Auth::user()->name.') with IP: '.request()->ip().', tried to order too much lessons (max is '.Auth::user()->settings->lessons.', free lessons left: '.$available_lessons.')'
            );
            return ['status' => false, 'error' => __('You are already used too much lessons, available lessons: ',['lessons' =>$available_lessons])];
        }

        return ['status' => true];
    }

    public function isLessonFree(LessonRequest $request) {
        if(!Lesson::where('date', $request->date_n_times[0])->where('time', $request->date_n_times[1])->exists()) {

            return ['status' => 'yes'];
        }
        return ['status' => 'no'];
    }

    public function getLessons(Request $request) {
        // TODO: get from config, also check if admin
        $start_time = '05:00';
        $end_time = '21:00';
        $half_day_number = 5;
        $half_day_end_time = '17:00';

        $is_half_day = (new Carbon($request->day))->dayOfWeek == $half_day_number;
        $lessons = Lesson::where("user_id",Auth::user()->id)->where("date", $request->day)->pluck('time')->toArray();
        $admin_added_lessons = Lesson::where("user_id",Auth::user()->id)->where("date", $request->day)->where(function($q) use($start_time,$end_time) {
            $q->where('time', '<', $start_time);
            $q->orWhere('time', '>', $end_time);
        })->pluck('time')->toArray();
        // $start_time = $admin_added_lessons[0];
        // Get from array above min and max value to set as start and end time
        $taken_lessons = Lesson::where("date", $request->day)->pluck('time')->toArray();
        $taken_lessons_by_users = Lesson::where("date", $request->day)->get();
        $has_lessons = false;
        if(count($lessons)) $has_lessons = true;
        $time_line = [];
        $time = new Carbon($start_time);

        while($time->format('H:i') <= $end_time) {
            $free_lesson = true;
            foreach ($taken_lessons_by_users as $taken_lesson) {
                // Lesson already taken
                if($time->format('H:i:s') == $taken_lesson->time) {
                    // Check if by current student
                    if($taken_lesson->user_id == Auth::user()->id) {
                        $time_line[] = [ $time->format('H:i'), 1, __('Already taken by you'), $is_half_day ];
                    }
                    else {
                        $time_line[] = [ $time->format('H:i'), 2, __('Already taken'), $is_half_day ];
                    }
                    $free_lesson = false;
                    break;
                }
            }
            if($free_lesson) {
                $time_line[] = [ $time->format('H:i'), 0, ' ', $is_half_day ];
            }
            $time->addMinutes(40);
        }
        return response()->json(
            [ 
                'data' => $time_line, 
                'has_user_lessons' => $has_lessons, 
                'is_half_day' => $is_half_day, 
                'half_day_end_time' => $half_day_end_time
            ]
        );
    }

    public function get_dates_range() {
        $settings = app('auth')->user()->settings;
        Carbon::setWeekStartsAt(0);
        Carbon::setWeekEndsAt(6);

        //TODO get those from config
        $this->choose_start = 'now';
        $this->holidays = [6];

        $today = new Carbon($this->choose_start);
        $today2 = new Carbon($this->choose_start);
        $today3 = new Carbon($this->choose_start);
        $this->date_range_start = $today->setDate($today->year,$today->month,$today->format('d') > 15?16:1)->startOfDay();
        $days_to_add = 7 * ($settings->weeks - 2);
        $this->date_range_end = $today2->startOfWeek()->setDate($today->year,$today->month,$today->format('d') > 15?$today3->endOfMonth()->format('d'):15)->addDays($days_to_add);
    }

    public function checkDateInBorders(Request $request) {
        $this->get_dates_range();
        
        if(Carbon::parse($request->date)->between($this->date_range_start, $this->date_range_end) && !in_array(Carbon::parse($request->date)->dayOfWeek, $this->holidays)) {
            return ['status' => true];
        }
        return ['status' => false];
    }
}
