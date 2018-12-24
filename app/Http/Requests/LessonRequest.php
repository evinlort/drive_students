<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Factory as ValidationFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Lesson;
use Illuminate\Support\Facades\Log;

class LessonRequest extends FormRequest
{

    public function __construct(ValidationFactory $validationFactory) {
    
            /* $validationFactory->extend(
            'check_used_lessons',
            function ($attribute, $value, $parameters) {
                // return true;

                $settings = \Auth::user()->settings;
                Carbon::setWeekStartsAt(0);
                Carbon::setWeekEndsAt(6);

                $choose_start = 'now';
                $today = new Carbon($choose_start);
                $today2 = new Carbon($choose_start);
                $today3 = new Carbon($choose_start);
                $start = $today->setDate($today->year,$today->month,$today->format('d') > 15?16:1)->startOfDay();
                $days_to_add = 7 * ($settings->weeks - 2);
                $end = $today2->startOfWeek()->setDate($today->year,$today->month,$today->format('d') > 15?$today3->endOfMonth()->format('d'):15)->addDays($days_to_add);
                $date = Carbon::parse($value[0]);
                if(Carbon::parse()->between($start, $end)) {
                    $lessons = Lesson::where('user_id', Auth::user()->id)->whereBetween('date', array($start->format('Y-m-d'), $end->format('Y-m-d')))->count();
                }
                if($lessons <= $settings->lessons)
                    return true;
            },
            'Used too much lessons'
        ); */

        $validationFactory->extend(
            'check_taken_lesson',
            function ($attribute, $value, $parameters) {
                if(!Lesson::where('date',$value[0])->where('time',$value[1])->first()) {
                    return true;
                }
                else {
                    Log::channel('single')->warning(
                        'User with ID:'.Auth::user()->id.' (name: '.Auth::user()->name.') with IP: '.request()->ip().', tried to set already taken lesson ('.implode(' ', $value).')'
                    );
               }
            },
            __('Lesson already taken')
        );
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date_n_times' => 'check_taken_lesson',
        ];
    }
}
