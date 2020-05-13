<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Models\UsersSettings;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\AdminStudentRequest;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
        $this->half_day_holidays = 5;
        $this->holidays = [6];
        $this->choose_start = 'now';
    }

    public function siteSettings()
    {
        return view('admin/settings');
    }

    public function chooseStudent()
    {
        return view('admin/choose_student', ['users' => User::all()]);
    }

    public function studentHome(Request $request)
    {
        if (empty($request->choosen_student)) {
            return Redirect::back()->withErrors(['status' => __('No identity')]);
        }
        $identity = explode(' ', $request->choosen_student)[0];
        $user = User::where('identity', $identity)->first();
        $data['user'] = $user;
        $settings = $user->settings;
        Carbon::setWeekStartsAt(0);
        Carbon::setWeekEndsAt(6);

        // TODO: get this from config
        $start_time = '05:00';
        $end_time = '21:00';

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
        $next_range['user'] = $data['user'];
        if ((new Carbon)->day >= 13 && (new Carbon)->day <= 15) {
            $added_range = true;
            $next_range['start'] = (new Carbon)->setDate((new Carbon)->year, (new Carbon)->month, 16)->startOfWeek();
            $next_range['start_of_period'] = (new Carbon)->setDate((new Carbon)->year, (new Carbon)->month, 16);
            $next_range['end'] = (new Carbon)->startOfWeek()->setDate((new Carbon)->year, (new Carbon)->month, $today3->endOfMonth()->format('d'));
            $next_range['end_of_period'] = (new Carbon)->startOfWeek()->setDate((new Carbon)->year, (new Carbon)->month, $today3->endOfMonth()->format('d'))->endOfWeek()->format('Y-m-d');
        } elseif ((new Carbon)->day >= 28 && (new Carbon)->day <= 31) {
            $added_range = true;
            $next_range['start'] = (new Carbon)->setDate((new Carbon)->year, (new Carbon)->addMonthNoOverflow(1)->format("m"), 1)->startOfWeek();
            $next_range['start_of_period'] = (new Carbon)->setDate((new Carbon)->year, (new Carbon)->addMonthNoOverflow(1)->format("m"), 1);
            $next_range['end'] = (new Carbon)->startOfWeek()->setDate((new Carbon)->year, (new Carbon)->addMonthNoOverflow(1)->format("m"), 15);
            $next_range['end_of_period'] = (new Carbon)->startOfWeek()->setDate((new Carbon)->year, (new Carbon)->addMonthNoOverflow(1)->format("m"), 15)->endOfWeek()->format('Y-m-d');
        }

        $data['days_a'] = $this->get_days($data);
        $data['days_b'] = $this->get_days($next_range);

        $data['days'] = [__('Su'), __('Mo'), __('Tu'), __('We'), __('Th'), __('Fr'), __('Sa')];
        $time_line = [];
        $time = new Carbon($start_time);
        while ($time->format('H:i') <= $end_time) {
            $time_line[] = $time->format('H:i');
            $time->addMinutes(40);
        }
        $data['time_line'] = $time_line;

        return view('admin/student_home', $data);
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
                $has_user_lessons = Lesson::where('user_id', $data['user']->id)->where('date', $data['start']->format('Y-m-d'))->count('user_id');

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

    public function addStudent(Request $request)
    {
        $user = User::where('identity', $request->identity)->first();
        // Get how much lessons left and show if 0 or less
        // Check if lesson is not captured by somebody else - and send message
        if (!Lesson::where('date', $request->date)->where('time', $request->time)->exists()) {
            Lesson::create(['user_id' => $user->id, 'date' => $request->date, 'time' => $request->time]);
            return ['status' => 'success'];
        } else {
            $student = Lesson::where('date', $request->date)->where('time', $request->time)->first()->user;
            return ['status' => 'failed', 'message' => __('Already taken') . ' : ' . __('Name') . ' ' . $student->name . ', ' . __('Identity') . ' ' . $student->identity];
        }
        $a = '';
    }

    public function removeStudent(Request $request)
    {
        $user = User::where('identity', $request->identity)->first();
        $lesson = Lesson::where('user_id', $user->id)->where('time', $request->time)->where('date', $request->date)->first();
        $query = 'delete from `lessons` where `date` = ? and `time` = ? and `user_id` = ?';
        if (\DB::delete($query, [$lesson->date, $lesson->time, $lesson->user_id])) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'fail'];
        }
    }

    public function registerStudent(AdminStudentRequest $request)
    {
        $user = User::create(['identity' => $request->identity, 'name' => $request->full_name]);
        $settings = UsersSettings::create(['user_id' => $user->id]);
        $settings->weeks = $request->weeks;
        $settings->lessons = $request->lessons;
        $settings->save();
        return redirect()->back()->withErrors(['test' => __('All right!')]);
    }

    public function deleteStudentView()
    {
        return view('admin/delete_student', ['users' => User::all()]);
    }

    public function deleteStudent(Request $request)
    {
        $identity = explode(' ', $request->choosen_student)[0];
        User::where('identity', $identity)->delete();
        return view('admin.home');
    }

    public function studentRegistration()
    {
        // TODO: get from config
        $data['weeks'] = 2;
        $data['lessons'] = 6;
        return view('admin/register', $data);
    }

    public function showDate($date)
    {
        $this->get_dates_range();
        $time_line = [];
        // TODO: get times from config
        // $admin_day_start = config('admin.lessons_starts_from');
        // $admin_day_end = config('admin.lessons_ends_at');
        $admin_day_start = '05:00';
        $admin_day_end = '21:00';
        $lessons = array();
        $time = new Carbon($admin_day_start);
        while ($time->format('H:i') <= $admin_day_end) {
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
    public function weekReport()
    {
        $this->get_dates_range();
        $lessons = array();
        $lessons_count = array();
        $this->date_range_end->addDay();
        while ($this->date_range_start->format('Y-m-d') != $this->date_range_end->format('Y-m-d')) {
            $lessons[] = $this->date_range_start->format('Y-m-d');
            $lessons_count[] = Lesson::where('date', $this->date_range_start->format('Y-m-d'))->groupBy('date')->selectRaw('count(`date`) as lessons')->first();
            $this->date_range_start->addDay();
        }
        if ((new Carbon)->day >= 13 && (new Carbon)->day <= 15) {
            $start = (new Carbon((new Carbon)->year.'-'.(new Carbon)->month.'-16'));
            while ($start->format('Y-m-d') <= (new Carbon((new Carbon)->year.'-'.(new Carbon)->month.'-16'))->endOfMonth()->format('Y-m-d')) {
                $next_lessons[] = $start->format('Y-m-d');
                $next_lessons_count[] = Lesson::where('date', $start->format('Y-m-d'))->groupBy('date')->selectRaw('count(`date`) as lessons')->first();
                $start->addDay();
            }
            $data['next_dates'] = $next_lessons;
            $data['next_lessons_count'] = $next_lessons_count;
        } elseif ((new Carbon)->day >= 28 && (new Carbon)->day <= 31) {
            $start = (new Carbon((new Carbon)->year.'-'.(new Carbon)->addMonth()->month.'-1'));
            while ($start->format('Y-m-d') <= (new Carbon((new Carbon)->year.'-'.(new Carbon)->addMonth()->month.'-1'))->addWeeks(2)->format('Y-m-d')) {
                $next_lessons[] = $start->format('Y-m-d');
                $next_lessons_count[] = Lesson::where('date', $start->format('Y-m-d'))->groupBy('date')->selectRaw('count(`date`) as lessons')->first();
                $start->addDay();
            }
            $data['next_dates'] = $next_lessons;
            $data['next_lessons_count'] = $next_lessons_count;
        }
        $data['dates'] = $lessons;
        $data['lessons_count'] = $lessons_count;
        return view('admin/week_report', $data);
    }

    public function get_dates_range()
    {
        Carbon::setWeekStartsAt(0);
        Carbon::setWeekEndsAt(6);

        //TODO get those from config
        $this->choose_start = 'now';
        $this->holidays = [5, 6];

        $today = new Carbon($this->choose_start);
        $today2 = new Carbon($this->choose_start);
        $today3 = new Carbon($this->choose_start);
        $this->date_range_start = $today->setDate($today->year, $today->month, $today->format('d') > 15 ? 16 : 1)->startOfDay();
        $this->date_range_end = $today2->startOfWeek()->setDate($today->year, $today->month, $today->format('d') > 15 ? $today3->endOfMonth()->format('d') : 15);
    }

    public function showUpdateView()
    {
        return view('admin.update_choose_student', ['users' => User::all()]);
    }

    public function studentUpdate(Request $request)
    {
        $data = array();
        $identity = explode(' ', $request->choosen_student)[0];
        $data['user'] = User::where('identity', $identity)->first();
        $data['settings'] = $data['user']->settings;
        return view('admin.student_update', $data);
    }

    public function updateStudentSettings(Request $request)
    {
        $settings = UsersSettings::where('user_id', $request->id)->first();
        $settings->weeks = $request->weeks;
        $settings->lessons = $request->lessons;
        $settings->save();
        return redirect('show_update');
    }

    public function showReportView()
    {
        return view('admin.report_choose_student', ['users' => User::all()]);
    }

    public function studentReport(Request $request)
    {
        $this->get_dates_range();
        $data = array();
        $identity = explode(' ', $request->choosen_student)[0];
        $data['user'] = User::where('identity', $identity)->first();
        if (!$data['user']) {
            return back()->withErrors(['error' => __('No identity given')]);
        }
        $now = new Carbon();
        $data['lessons'] = Lesson::where('user_id', $data['user']->id)
            ->where('date', '>=', $now->format('Y-m-d'))
            ->orderBy('date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();
        $data['start'] = $this->date_range_start;
        $data['end'] = $this->date_range_end;
        return view('admin.student_report', $data);
    }

    public function downloadPDF(Request $request)
    {
        $this->get_dates_range();
        $data = array();
        $data['user'] = User::where('identity', $request->id)->first();
        $now = new Carbon();
        $data['lessons'] = Lesson::where('user_id', $data['user']->id)
            ->where('date', '>=', $now->format('Y-m-d'))
            ->orderBy('date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();
        $data['start'] = $this->date_range_start;
        $data['end'] = $this->date_range_end;
        // return view('admin.student_report_tiny', $data);
        $pdf = PDF::loadView('admin.student_report_tiny', $data);
        return $pdf->download('report.pdf');
    }

    public function downloadCSV(Request $request)
    {
        $this->get_dates_range();
        $data['user'] = User::where('identity', $request->id)->first();
        $now = new Carbon();
        $lessons = Lesson::where('user_id', $data['user']->id)
            ->where('date', '>=', $now->format('Y-m-d'))
            ->orderBy('date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();

        $columns = array(__('Date'), __('Time'));
        $fname = $request->id . '.csv';
        $file = fopen($fname, 'w');
        fputcsv($file, $columns);

        foreach ($lessons as $lesson) {
            fputcsv($file, array(Carbon::parse($lesson->date)->format('d-m-Y'), $lesson->time));
        }
        fclose($file);

        return Response::download($fname);
    }
}
