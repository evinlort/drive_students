<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
