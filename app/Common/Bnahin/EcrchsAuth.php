<?php
/**
 * ECRCHS Google SSO Wrapper
 * @author Blake Nahin <bnahin@live.com>
 */

namespace App\Common\Bnahin;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

use Maatwebsite\Excel\Excel;

class EcrchsAuth
{
    private $guzzle;
    public $user;
    public $excel;
    public $studentExcelPath = 'storage/app/public/uploads/';
    public $studentExcelFileName = 'students.xlsx';

    public function __construct(Excel $excel)
    {
        $this->guzzle = new Client(['base_uri' => 'https://www.googleapis.com/oauth2/v2/']);
        $this->excel = $excel;
    }

    public function getUser()
    {
        if (App::isLocal()) {
            $headers = [
                'Authorization' => 'Bearer ' . config('services.google.client_secret')
            ];

            $response = $this->guzzle->get('userinfo', ['headers' => $headers])->getBody()
                ->getContents();

            $user = json_decode($response, true);
            $this->user = $user;

        } else {
            $user = Socialite::driver('google')->user();
            $this->user = $user;
        }
        if (!in_array($user['hd'], ["ecrchs.org", "ecrchs.net"])) {
            return abort(403, "Not a member of the ECRCHS organization"); //Not member of ECRCHS organization
        }

        return (object)$this->user;
    }

    /**
     * @param bool $truncate Truncate the table first.
     *
     * @return bool|int
     */
    public function importEnrolled($truncate = true)
    {
        ini_set('max_execution_time', 120);
        if (Storage::disk('local')->has('public/uploads/' . $this->studentExcelFileName)) {
            $students = $this->excel->load($this->studentExcelPath . $this->studentExcelFileName)->get();
        } else {
            return false;
        }

        $found = array();
        $errors = array();
        $count = 0;

        if ($truncate) {
            \App\StudentInfo::truncate();
        }
        foreach ($students as $student) {
            if (!$student->grade || !$student->student_id || !$student->first_name || !$student->last_name) {
                //Invalid data, not complete
                $errors[] = $student;
                continue;
            }

            $info = new \App\StudentInfo;
            $info->grade = $student->grade;
            $info->student_id = $student->student_id;
            $info->first_name = $student->first_name;
            $info->last_name = $student->last_name;
            if (!$student->stuemail) {
                // Fallback in case email does not exist somehow
                $info->email = $student->student_id . "@ecrchs.org";
            } else {
                $info->email = $student->stuemail;
            }

            $user = \App\User::where('email', $student->stuemail)->with('student');
            if ($user->exists()) {
                //Associate user
                $user = $user->first();
                $user->student()->save($info);
                $found[] = $user;
            } else {
                //No user association
                $info->save();
            }
            $count++;
        }

        return $count;
    }
}