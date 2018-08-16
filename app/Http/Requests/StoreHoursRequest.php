<?php

namespace App\Http\Requests;

use App\Hour;
use App\StudentInfo;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;

class StoreHoursRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (Auth::check()) {
            if (Auth::guard('admin')->check()) {
                // Admins can clock out anybody
                return true;
            }
            if ($this->input('id') != Auth::user()->student->student_id) {
                //Somehow not their own ID
                return false;
            }

            //All good
            return true;

        }

        //Not logged in
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id'    => 'required|integer',
            'event' => 'integer',
        ];
    }

    /**
     * Validate Student ID
     * Check not already clocked out
     *
     * @param \Illuminate\Validation\Validator $validator
     */
    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            //Step 1: User exists
            $id = $this->input('id');

            $student = StudentInfo::where('student_id', $id);
            $hasStudent = $student->exists() &&
                $student->first()->user()->exists() &&
                $student->first()->user->clubs()->exists()
                && $student->first()->user->clubs()
                    ->where('club_id',
                        (app()->isLocal()) ? 1 : Session::get('club-id'))->exists();

            if ($hasStudent) {
                //Step 2: User not currently clocked out
                if (Hour::isClockedOut($id)) {
                    $v->errors()->add('id', 'The student is already clocked out.');
                }
            } else {
                $v->errors()->add('id', 'The student does not exist in the current club.');
            }
        });
    }
}
