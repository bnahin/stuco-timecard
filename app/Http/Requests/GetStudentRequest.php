<?php

namespace App\Http\Requests;

use App\Helpers\UserHelper;
use App\StudentInfo;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class GetStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|int'
        ];
    }

    /**
     * Validate user is part of club
     *
     * @param \Illuminate\Validation\Validator $validator
     */
    public function withValidator($validator)
    {
        $validator->after(function (Validator $v) {
            $id = $this->input('id');

            //Step 1: User is part of club
            if (!UserHelper::belongsToClub(StudentInfo::where('student_id', $id), $blocked)) {
                //Does not exist or not assigned to club
                $v->errors()->add('id', 'The student does not exist.');

                //Step 2: User is not blocked
                if ($blocked) {
                    $v->errors()->add('id', 'The student is blocked or has been removed from the club.');
                }
            }
        });
    }
}
