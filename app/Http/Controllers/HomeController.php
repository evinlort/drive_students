<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Lesson;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\LessonRequest;
use Exception;

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
        $this->half_day_holidays = 5;
        $this->holidays = [6];
        $this->choose_start = 'now';
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
        $start_time = '06:30';
        $end_time = '19:30';

        $today = new Carbon($this->choose_start);
        $today2 = new Carbon($this->choose_start);
        $today3 = new Carbon($this->choose_start);
        // $today4 = new Carbon($this->choose_start);
        // $today5 = new Carbon($this->choose_start);
        $today6 = new Carbon($this->choose_start);

        $data['start'] = $today6->startOfWeek();

        $days = array();
        $days_to_add = 7 * ($settings->weeks - 2);
        $end = $today2->startOfWeek()->setDate(
            $today->year,
            $today->month,
            $today->format('d') > 15 ?
                $today3->endOfMonth()->format('d') :
                15
        )->addDays($days_to_add);

        $data['end_of_period'] = $end->copy()->endOfWeek()->format('Y-m-d');
        $data['end'] = $end;

        $added_range = false;
        $next_range = array();
        if ((new Carbon)->day >= 13 && (new Carbon)->day <= 15) {
            $added_range = true;
            $next_range['start'] = (new Carbon)->setDate((new Carbon)->year, (new Carbon)->month, 16)->startOfWeek();
            $next_range['start_of_period'] = (new Carbon)->setDate((new Carbon)->year, (new Carbon)->month, 16);
            $next_range['end'] = (new Carbon)->startOfWeek()->setDate((new Carbon)->year, (new Carbon)->month, $today3->endOfMonth()->format('d'));
            $next_range['end_of_period'] = (new Carbon)->startOfWeek()->setDate((new Carbon)->year, (new Carbon)->month, $today3->endOfMonth()->format('d'))->endOfWeek()->format('Y-m-d');
        } elseif ((new Carbon)->day >= 28 && (new Carbon)->day <= 31) {
            $added_range = true;
            $year = (new Carbon)->year + ((new Carbon)->month == 12?1:0); // Set year as next year if this month is December (12)
            $next_range['start'] = (new Carbon)->setDate($year, (new Carbon)->addMonthNoOverflow(1)->format("m"), 1)->startOfWeek();
            $next_range['start_of_period'] = (new Carbon)->setDate($year, (new Carbon)->addMonthNoOverflow(1)->format("m"), 1);
            $next_range['end'] = (new Carbon)->startOfWeek()->setDate($year, (new Carbon)->addMonthNoOverflow(1)->format("m"), 15);
            $next_range['end_of_period'] = (new Carbon)->startOfWeek()->setDate($year, (new Carbon)->addMonthNoOverflow(1)->format("m"), 15)->endOfWeek()->format('Y-m-d');
        }

        $data['days_a'] = $this->get_days($data);
        $data['days_b'] = $this->get_days($next_range);

        $data['days'] = [__('Su'), __('Mo'), __('Tu'), __('We'), __('Th'), __('Fr'), __('Sa')];
        $time_line = [];
        $time = new Carbon($start_time);
        while ($time->format('H:i') <= $end_time) {
            $time_line[] = $time->format('H:i');
            $time->addMinutes(60);
        }
        $data['time_line'] = $time_line;

        $user = auth()->user();
        if (isset($user) && isset($user->settings))
            if ($user->settings->is_admin)
                return view('admin.home');
            else
                return view('home', $data);
    }

    public function get_days($data)
    {
        try {
            if (isset($data['start_of_period'])) {
                $start_of_period = $data['start_of_period'];
            } else {
                $start_of_period = new Carbon($this->choose_start);
            }
            while ($data['start']->format('Y-m-d') <= $data['end_of_period']) {
                $has_user_lessons = Lesson::where('user_id', Auth::user()->id)->where('date', $data['start']->format('Y-m-d'))->count('user_id');

                if (in_array($data['start']->dayOfWeek, $this->holidays)) {
                    $days[] = [$data['start']->format('d'), 2, "full" => $data['start']->format('Y-m-d'), $has_user_lessons];
                    if ($data['start']->format('Y-m-d') == $start_of_period->format('Y-m-d')) {
                        $start_of_period->addDay();
                    }
                    $data['start']->addDay();
                    continue;
                }
                if ($data['start']->dayOfWeek == $this->half_day_holidays) {
                    if (
                        $data['start']->format('Y-m-d') >= (new Carbon($this->choose_start))->format('Y-m-d') &&
                        $data['start']->format('Y-m-d') <= $data['end_of_period'] &&
                        $data['start']->format('Y-m-d') == $start_of_period->format('Y-m-d') &&
                        $data['start']->format('Y-m-d') <= $data['end']->format('Y-m-d')
                    ) {
                        $days[] = [$data['start']->format('d'), 3, "full" => $data['start']->format('Y-m-d'), $has_user_lessons];
                        $start_of_period->addDay();
                        $data['start']->addDay();
                    } else {
                        $days[] = [$data['start']->format('d'), 1, "full" => $data['start']->format('Y-m-d'), $has_user_lessons];
                        if ($data['start']->format('Y-m-d') == $start_of_period->format('Y-m-d')) {
                            $start_of_period->addDay();
                        }
                        $data['start']->addDay();
                    }
                    continue;
                }
                if (
                    $data['start']->format('Y-m-d') >= (new Carbon($this->choose_start))->format('Y-m-d') &&
                    $data['start']->format('Y-m-d') <= $data['end_of_period'] &&
                    $data['start']->format('Y-m-d') == $start_of_period->format('Y-m-d') &&
                    $data['start']->format('Y-m-d') <= $data['end']->format('Y-m-d')
                ) {
                    $days[] = [$data['start']->format('d'), 0, "full" => $data['start']->format('Y-m-d'), $has_user_lessons];
                    $start_of_period->addDay();
                } else {
                    $days[] = [$data['start']->format('d'), 1, "full" => $data['start']->format('Y-m-d'), $has_user_lessons];
                }
                $data['start']->addDay();
            }
            return $days;
        } catch (Exception $e) {
            return null;
        }
    }
}
