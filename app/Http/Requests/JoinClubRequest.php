<?php

namespace App\Http\Requests;

use App\Admin;
use App\Club;
use App\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;

class JoinClubRequest extends FormRequest
{
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
            'code' => 'required|string|size:6|exists:clubs,join_code'
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            if (!Session::has('temp-auth')) {
                return $validator->errors()->add('code', 'Session expired. Log out and log back in.');
            }

            //Not a part of the club or is admin
            $auth = Session::get('temp-auth');
            $club = Club::where('join_code', $this->code)->first();
            $user = User::where('email', $auth->email);
            $admin = Admin::where('email', $auth->email);

            if ($club && (($admin->exists() && $admin->first()->clubs()->where('clubs.id', $club->id)->exists())
                ||
                ($user->exists() && $user->first()->clubs()->where('clubs.id', $club->id)->exists()))) {
                $validator->errors()->add('code', 'You are already a member of the club.');
            }
        });
    }
}
