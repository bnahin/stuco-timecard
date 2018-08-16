<?php

namespace App\Http\Controllers;

use App\Event;
use App\Hour;
use App\Http\Requests\StoreHoursRequest;
use App\StudentInfo;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HoursController extends Controller
{
    public function index(User $user = null)
    {
        if ($user) {
            if (!isAdmin()) {
                return redirect(route('home'))->with('forbidden', true);
            }
            $uid = $user->id;
            $fullName = $user->full_name;
        } else {
            $uid = Auth::id();
            $fullName = Auth::user()->full_name;
        }

        $hours = Hour::where('user_id', $uid)->orderByDesc('start_time')->get();

        $total = Hour::select(\DB::raw('TIME_TO_SEC(TIMEDIFF(end_time, start_time)) AS total'))->where('user_id',
            $uid)->get();
        $totalHours = round($total->sum('total') / 3600);
        $averageHours = round($total->avg('total') / 3600);

        $numEvents = Hour::where('user_id', Auth::id())->count();

        return view('pages.hours', compact('hours', 'totalHours', 'averageHours', 'numEvents', 'fullName', 'uid'));
    }

    /**
     * Store new hour submission
     * "Add New Activity"
     *
     * @param \App\Http\Requests\StoreHoursRequest $request
     *
     * @return array
     * @throws \Throwable
     */
    public function store(StoreHoursRequest $request)
    {
        $req = $request->validated();

        $stuid = $req['id'];
        $event = $req['event'];
        $comments = $request->comments ?? null; //Comments might be blank

        $hour = new Hour;
        $hour->student_id = $stuid;
        $hour->event_id = $event;
        $hour->start_time = Carbon::now();
        $hour->comments = $comments;
        $hour->club_id = (app()->isLocal()) ? 1 : Session::get('club-id');
        #$hour->saveOrFail();

        $student = StudentInfo::where('student_id', $stuid);
        if ($student->exists()) {
            //Associate user
            $hour->user_id = $student->first()->user->id;
            $hour->saveOrFail();
        }

        $name = $student->first()->full_name;
        $event = $hour->getEventName();
        if (Auth::guard('admin')->check()) {
            log_action("Clocked out $name for $event");
        } else {
            log_action("Clocked out for $event");
        }

        return ['success' => true, 'messsage' => $name . " has been clocked out."];


    }

    public function delete(Hour $hour, Request $request)
    {
        $stuid = $hour->student_id;
        if (Hour::isClockedOut($stuid) || isAdmin()) {
            //Delete hour
            try {
                $hour->delete();
                if (isAdmin()) {
                    log_action("Deleted time punch for " . $hour->getFullName() . " from " . $hour->start_time->toFormattedDateString());
                } else {
                    log_action("Deleted own time punch");
                }
            } catch (\Exception $e) {
                abort(500, $e->getMessage());
            }

            return response()->json(['success' => true]);
        } else {
            return abort(422, 'You are not clocked out.');
        }
    }

    public function clockin(Hour $hour, Request $request)
    {
        if ($hour->end_time) {
            //Already clocked in, possibly by admin? Whatever!
            return response()->json(['success' => true]);
        }
        //Clock in!
        $hour->end_time = Carbon::now();
        $hour->comments = $request->comments ?? null;
        $hour->saveOrFail();

        $name = $hour->getEventName();
        log_action("Clocked in from $name");

        return response()->json(['success' => true]);
    }

    public function charts(User $user)
    {
        //TODO: add club_ids
        $uid = $user->id;
        //Line Chart: Hours per Month
        $lineRes = Hour::select(\DB::raw(
            "MONTH(start_time) as `month`, TRUNCATE(AVG(ROUND(TIME_TO_SEC(TIMEDIFF(end_time, start_time)) / 3600)), 2) AS hours"))
            ->where('user_id', $uid)->groupBy("month")->get();

        //Pie Chart: Events by Name
        $pieRes = \DB::table('hours')
            ->join('events', 'hours.event_id', '=', 'events.id')
            ->select(\DB::raw('hours.event_id AS event_id, events.event_name AS event_name, COUNT(*) AS count'))
            ->where('hours.user_id', $uid)
            ->groupBy('event_id', 'event_name')->get();

        //Mixed Chart: Total Hours, Total Hours per Month by Event
        $mixedRes = [];
        $labels = [];
        for ($i = 0; $i < 8; $i++) {
            //Past 8 months
            $now = Carbon::now()->subMonths($i);

            $monthName = $now->format('F');
            $month = Carbon::now()->subMonths($i)->month;

            $labels[$month] = $monthName;

            /** Total Hours */
            $db = Hour::select(
                \DB::raw("CEIL(SUM(TIME_TO_SEC(TIMEDIFF(end_time, start_time)) / 3600)) AS hours"))
                ->where('user_id', $uid);
            $totalHours = $db->whereRaw("MONTH(start_time) = ?", [$month]);
            $mixedRes[$month]['total'] = $totalHours->first()->hours ?: 0;

            /** Hours per Event */
            $events = Event::all(); //Inlcuding inactive events
            foreach ($events as $event) {
                $db = Hour::select(
                    \DB::raw("CEIL(SUM(TIME_TO_SEC(TIMEDIFF(end_time, start_time)) / 3600)) AS hours"))
                    ->where('user_id', $uid);
                $totalHours = $db->whereRaw("MONTH(start_time) = ?", [$month])
                    ->where('event_id', $event->id)
                    ->first()->hours;

                $mixedRes[$month]['events'][$event->event_name] =
                    $totalHours ?: 0;
            }

        }
        $response = $this->parseChartsForJs(
            ['line' => $lineRes, 'pie' => $pieRes, 'mixed' => $mixedRes, 'labels' => $labels]
        );

        return response()->json($response);
    }

    private function parseChartsForJs(array $data)
    {
        $return = [];
        $labelData = $data['labels'];
        $pieData = $data['pie'];
        $mixedData = $data['mixed'];

        /** Line Chart */
        $lineData = $data['line'];
        foreach ($lineData as $line) {
            $month = $line->month;
            $total = $line->hours;

            if (!isset($labelData[$month])) {
                //Past 8 months ago, ignore
                continue;
            }
            $monthName = $labelData[$month];

            $return['line']['labels'][] = $monthName;
            $return['line']['data'][] = $total;
        }

        /** Pie Chart */
        foreach ($pieData as $pie) {
            $id = $pie->event_id;
            $name = $pie->event_name;
            $total = $pie->count;

            $return['pie']['labels'][] = $name;
            $return['pie']['data'][] = $total;
        }

        /** Mixed Chart - "The Big One!" */
        $dataset = [];
        foreach ($mixedData as $month => $mixed) {
            if (!$mixed['total']) {
                //No data to show
                continue;
            }
            $monthName = $labelData[$month];
            $return['mixed']['labels'][] = $monthName;
            $return['mixed']['totals'][] = $mixed['total'];

            //Each Event
            $c = 0;
            foreach ($mixed['events'] as $name => $total) {
                $dataset[$name][] = $total;
            }

        }
        $return['mixed']['datasets'] = $dataset;

        return $return;
    }
}
