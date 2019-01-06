<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Lesson;
use App\Http\Requests\AdminStudentRequest;
use App\Models\User;
use App\Models\UsersSettings;

class AdminController extends Controller
{
    public function __construct() {
        $this->middleware('admin');
    }

    public function siteSettings() {
        return 'Settings will be here!';
    }

    public function addStudent(Request $request) {
        $user = User::where('identity',$request->identity)->first();
        // Get how much lessons left and show if 0 or less
        // Check if lesson is not captured by somebody else - and send message
        Lesson::create( [ 'user_id' => $user->id, 'date' => $request->date, 'time' => $request->time ] );
        $a='';
    }

    public function removeStudent(Request $request) {
        $user = User::where('identity',$request->identity)->first();
        $lesson = Lesson::where('user_id', $user->id)->where('time', $request->time)->where('date', $request->date)->first();
        $query = 'delete from `lessons` where `date` = ? and `time` = ? and `user_id` = ?';
        if(\DB::delete($query, [$lesson->date,$lesson->time,$lesson->user_id])) {
            return ['status'=>'success'];
        }
        else {
            return ['status'=>'fail'];
        }
    }

    public function registerStudent(AdminStudentRequest $request) {
        $user = User::create( [ 'identity' => $request->identity, 'name' => $request->full_name ] );
        $settings = UsersSettings::create( ['user_id' => $user->id] );
        $settings->weeks = $request->weeks;
        $settings->lessons = $request->lessons;
        $settings->save();
        return redirect()->back()->withErrors(['test' => __('All right!') ]);
    }

    public function studentRegistration() {
        // TODO: get from config
        $data['weeks'] = 2;
        $data['lessons'] = 6;
        return view('admin/register', $data);
    }

    public function showDate($date){
        $this->get_dates_range();
        $time_line = [];
        // TODO: get times from config
        // $admin_day_start = config('admin.lessons_starts_from');
        // $admin_day_end = config('admin.lessons_ends_at');
        $admin_day_start = '07:00';
        $admin_day_end = '19:00';
        $lessons = array();
        $time = new Carbon($admin_day_start);
        while($time->format('H:i') <= $admin_day_end) {
            $time_line[] = $time->format('H:i');
            $lessons[] = Lesson::where('date', $date)->where('time', $time->format('H:i'))->first();
            $time->addMinutes(40);
        }
        $data['time_line'] = $time_line;
        // $lessons = Lesson::where('date', $date)->orderby('time')->get();
        $data['lessons'] = $lessons;
        $data['users'] = User::all();
        $data['date'] = $date;
        return view('admin/show_date', $data);
    }
    public function weekReport() {
        $this->get_dates_range();
        $lessons = array();
        $lessons_count = array();
        $this->date_range_end->addDay();
        while($this->date_range_start->format('Y-m-d') != $this->date_range_end->format('Y-m-d')) {
            $lessons[] = $this->date_range_start->format('Y-m-d');
            $lessons_count[] = Lesson::where('date', $this->date_range_start->format('Y-m-d'))->groupBy('date')->selectRaw('count(`date`) as lessons')->first();
            $this->date_range_start->addDay();
        }
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
