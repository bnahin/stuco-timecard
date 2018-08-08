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

class HoursController extends Controller
{
    public function index()
    {
        $hours = Hour::where('user_id', Auth::user()->id)->orderByDesc('start_time')->get();

        $total = Hour::select(\DB::raw('TIME_TO_SEC(TIMEDIFF(end_time, start_time)) AS total'))->where('user_id',
            Auth::user()->id)->get();
        $totalHours = round($total->sum('total') / 3600);
        $averageHours = round($total->avg('total') / 3600);

        $numEvents = Hour::where('user_id', Auth::id())->count();

        return view('pages.hours', compact('hours', 'totalHours', 'averageHours', 'numEvents'));
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
        #$hour->saveOrFail();

        $user = User::where('student_id', $stuid);
        if ($user->exists()) {
            //Associate user
            $hour->user_id = $user->first()->id;
            $hour->saveOrFail();
        }

        $name = StudentInfo::where('student_id', $hour->student_id)->first()->full_name;
        $event = $hour->getEventName();
        if (Auth::user()->isAdmin()) {
            log_action("Clocked out $name for $event");
        } else {
            log_action("Clocked out for $event");
        }

        return ['success' => true, 'messsage' => $user->first()->first_name . " has been clocked out."];


    }

    public function delete(Request $request)
    {
        $stuid = Auth::user()->student->student_id;
        if (Hour::isClockedOut($stuid)) {
            //Delete hour
            try {
                Hour::getClockData($stuid)->delete();
                log_action("Deleted time punch");
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
        if ($hour->user_id !== Auth::user()->id) {
            //Hmm... How is this possible?
            abort(403);
        }
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

    public function charts()
    {
        //Line Chart: Hours per Month
        $lineRes = Hour::select(
            \DB::raw(
                "MONTH(start_time) as `month`, TRUNCATE(AVG(ROUND(TIME_TO_SEC(TIMEDIFF(end_time, start_time)) / 3600)), 2) AS hours"))
            ->groupBy("month")->get();

        //Pie Chart: Events by Name
        $pieRes = \DB::table('hours')
            ->join('events', 'hours.event_id', '=', 'events.id')
            ->select(\DB::raw('hours.event_id AS event_id, events.event_name AS event_name, COUNT(*) AS count'))
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
                \DB::raw("CEIL(SUM(TIME_TO_SEC(TIMEDIFF(end_time, start_time)) / 3600)) AS hours"));
            $totalHours = $db->whereRaw("MONTH(start_time) = ?", [$month]);
            $mixedRes[$month]['total'] = $totalHours->first()->hours ?: 0;

            /** Hours per Event */
            $db = Hour::select(
                \DB::raw("CEIL(SUM(TIME_TO_SEC(TIMEDIFF(end_time, start_time)) / 3600)) AS hours, event_id"));
            $totalHours = $db->whereRaw("MONTH(start_time) = ?", [$month]);
            $events = Event::all(); //Inlcuding inactive events
            foreach ($events as $event) {
                $db = Hour::select(
                    \DB::raw("CEIL(SUM(TIME_TO_SEC(TIMEDIFF(end_time, start_time)) / 3600)) AS hours"));
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
            $monthName = $labelData[$month];
            $total = $line->hours;

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
