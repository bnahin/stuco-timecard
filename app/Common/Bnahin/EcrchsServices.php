<?php
/**
 * ECRCHS Google SSO Wrapper & Services
 * @author Blake Nahin <bnahin@live.com>
 */

namespace App\Common\Bnahin;


use GuzzleHttp\Client;
use http\Exception\UnexpectedValueException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Files\ExcelFile;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

use ZanySoft\Zip\Zip;

class EcrchsServices
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
            $domain = $user['hd'];
        } else {
            $user = Socialite::driver('google')->user();
            $this->user = $user;
            $domain = explode("@", $user->email)[1];

            $this->user->hd = $domain;
            $name = explode(" ", $user->name);
            $this->user->given_name = $user['given_name'] ?? $name[0];
            $this->user->family_name = $user['family_name'] ?? explode(" ", $user->name)[count($name) - 1];
        }

        if (!in_array($domain, ["ecrchs.org", "ecrchs.net"])) {
            return abort(403, "Not a member of the ECRCHS organization");
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

    /**
     * Export all club hours to zip of Excel files
     *
     * @param \App\Club $club
     *
     * @return boolean
     * @throws \Exception
     */
    public function exportHours(\App\Club $club)
    {
        /**
         * This function will generate a zip file of the students' hour histories
         * for the club.
         */

        /** Step 1: Get all users in the club */
        $users = $club->users;

        if (!$users) {
            throw new \Exception("No users are assigned to the club");
        }

        //Temp folder name
        $folderName = preg_replace('/\s+/', '', $club->club_name);

        //Make Temp Directory
        if (Storage::exists("temp-archives/$folderName")) {
            $this->deleteTempArchivesFolder($folderName);
        }
        Storage::makeDirectory("temp-archives/$folderName");

        /** Step 2: For each user in club, save hours in Excel sheet located in temp folder. */
        foreach ($users as $user) {
            $this->excel->create($user->full_name,
                function (LaravelExcelWriter $excel) use ($club, $user) {
                    $excel->setTitle("Hours for {$user->full_name} in {$club->club_name}");

                    $excel->sheet("{$user->first_name} {$user->last_name} {$club->club_name}",
                        function (LaravelExcelWorksheet $sheet) use ($club, $user) {
                            //Get user's hours for current club
                            $hours = $user->hours()->where('hours.club_id', $club->id)
                                ->orderBy('start_time', 'asc')->get();

                            //Load view containing table of hours
                            $sheet->loadView('vendor.excel.hourexport')
                                ->with(compact('hours'));
                        });
                })->store('xlsx', Storage::path("temp-archives/$folderName"));
        }

        /** Step 3: Compress temp folder of Excel sheets to zip file */
        $date = date("m.d.Y"); //File suffix
        $storagePath = "public/archives/{$club->club_name} $date.zip"; //Zip file name
        $zipFileName = Storage::path($storagePath); //Real path

        $zip = Zip::create($zipFileName, true); //Create zip w/ overwrite
        $zip->add(Storage::path("temp-archives/$folderName"), true); //Add temp folder to zip
        $zip->close(); //Close and save zip

        $this->deleteTempArchivesFolder($folderName);


        /** Step 4: Set path in session to be sent to AJAX */
        Session::put('archive-download', $zipFileName);

        return true;
    }

    private function deleteTempArchivesFolder($name)
    {
        Storage::deleteDirectory("temp-archives/$name/"); //Delete temp directory
        if (!count(Storage::files("temp-archives"))) {
            //Temp folder empty, delete to clean up
            Storage::deleteDirectory("temp-archives/");
        }
    }
}