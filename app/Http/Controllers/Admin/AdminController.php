<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Lesson;

class AdminController extends Controller
{
    public function __construct() {
        $this->middleware('admin');
    }
    public function showDate($date){
        $this->get_dates_range();
        $lessons = Lesson::where('date', $date)->orderby('time')->get();
        $data['lessons'] = $lessons;
        return view('admin/show_date', $data);
    }
    public function weekReport() {
        $this->get_dates_range();
        $lessons = Lesson::whereBetween('date', [$this->date_range_start->format('Y-m-d'), $this->date_range_end->format('Y-m-d')])->orderby('date')->distinct()->pluck('date');
        $lessons_count = Lesson::whereBetween('date', [$this->date_range_start->format('Y-m-d'), $this->date_range_end->format('Y-m-d')])->groupBy('date')->selectRaw('count(`date`) as lessons')->get();
        $data['dates'] = $lessons;
        $data['lessons_count'] = $lessons_count;
        return view('admin/week_report', $data);
    }

    public function get_dates_range() {
        Carbon::setWeekStartsAt(0);
        Carbon::setWeekEndsAt(6);

        //TODO get those from config
        $this->choose_start = 'now';
        $this->holidays = [5,6];

        $today = new Carbon($this->choose_start);
        $today2 = new Carbon($this->choose_start);
        $today3 = new Carbon($this->choose_start);
        $this->date_range_start = $today->setDate($today->year,$today->month,$today->format('d') > 15?16:1)->startOfDay();
        $this->date_range_end = $today2->startOfWeek()->setDate($today->year,$today->month,$today->format('d') > 15?$today3->endOfMonth()->format('d'):15);
    }

}
