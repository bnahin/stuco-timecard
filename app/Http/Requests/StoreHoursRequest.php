<?php

namespace App\Http\Requests;

use App\User;
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
        //Anyone can submit hours/clock out
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
            if (User::where('student_id', $id)->count()) {
                //Step 2: User not currently clocked out
                if (User::isClockedOut(User::where('student_id', $id)->pluck('id'))) {
                    $v->errors()->add('student_id', 'Student is already clocked out');
                }
            } else {
                $v->errors()->add('student_id', 'The student does not exist.');
            }
        });
    }
}
