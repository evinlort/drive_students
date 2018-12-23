<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class LessonsController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function checkDateInBorders(Request $request) {
        $settings = \Auth::user()->settings;
        Carbon::setWeekStartsAt(0);
        Carbon::setWeekEndsAt(6);

        //TODO get those from config
        $choose_start = 'now';
        $holidays = [5,6];

        $today = new Carbon($choose_start);
        $today2 = new Carbon($choose_start);
        $today3 = new Carbon($choose_start);
        $start = $today->setDate($today->year,$today->month,$today->format('d') > 15?16:1)->startOfDay();
        $days_to_add = 7 * ($settings->weeks - 2);
        $end = $today2->startOfWeek()->setDate($today->year,$today->month,$today->format('d') > 15?$today3->endOfMonth()->format('d'):15)->addDays($days_to_add);
        if(Carbon::parse($request->date)->between($start, $end) && !in_array(Carbon::parse($request->date)->dayOfWeek, $holidays)) {
            return ['status' => true];
        }
        return ['status' => false];
    }
}
