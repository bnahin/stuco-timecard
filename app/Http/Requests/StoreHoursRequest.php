<?php

namespace App\Http\Requests;

use App\Hour;
use App\StudentInfo;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

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

            $user = User::where('student_id', $id);
            $hasUser = $user->count();
            $student = StudentInfo::where('student_id', $id);
            $hasStudent = $student->count();

            if ($hasUser || $hasStudent) {
                //Step 2: User not currently clocked out
                if (Hour::isClockedOut($id)) {
                    $v->errors()->add('id', 'The student is already clocked out.');
                }
            } else {
                $v->errors()->add('id', 'The student does not exist.');
            }
        });
    }
}
