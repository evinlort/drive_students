<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Lesson;
use App\Http\Requests\LessonRequest;

class LessonsController extends Controller
{
    private $date_range_start;
    private $date_range_end;
    private $choose_start;
    private $holidays;
    private $user;

    public function __construct() {
        $this->middleware('auth');
        $this->choose_start = 'now';
        $this->holidays = array(5,6);
    }

    public function isLessonFree(LessonRequest $request) {
        if(!Lesson::where('date', $request->date_n_times[0])->where('time', $request->date_n_times[1])->exists())
            return ['status' => 'yes'];
        return ['status' => 'no'];
    }

    public function getLessons(Request $request) {
        // TODO: get from config, also check if admin
        $start_time = '07:00';

        $lessons = Lesson::where("user_id",Auth::user()->id)->where("date", $request->day)->pluck('time')->toArray();
        $taken_lessons = Lesson::where("date", $request->day)->pluck('time')->toArray();
        $taken_lessons_by_users = Lesson::where("date", $request->day)->get();
        $time_line = [];
        $time = new Carbon($start_time);
        while($time->format('H:i') <= '19:00') {
            $free_lesson = true;
            foreach ($taken_lessons_by_users as $taken_lesson) {
                // Lesson already taken
                if($time->format('H:i:s') == $taken_lesson->time) {
                    // Check if by current student
                    if($taken_lesson->user_id == Auth::user()->id) {
                        $time_line[] = [ $time->format('H:i'), 1, __('Already taken by you') ];
                    }
                    else {
                        $time_line[] = [ $time->format('H:i'), 1, __('Already taken') ];
                    }
                    $free_lesson = false;
                    break;
                }
            }
            if($free_lesson) {
                $time_line[] = [ $time->format('H:i'), 0, ' ' ];
            }
            $time->addMinutes(40);
        }
        return response()->json([ 'data' => $time_line ]);
    }

    public function get_dates_range() {
        $settings = app('auth')->user()->settings;
        Carbon::setWeekStartsAt(0);
        Carbon::setWeekEndsAt(6);

        //TODO get those from config
        $this->choose_start = 'now';
        $this->holidays = [5,6];

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
