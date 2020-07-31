<?php

namespace EricPridham\Flow\Http\Controllers;

use Carbon\Carbon;
use EricPridham\Flow\Recorder\DatabaseRecorder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FlowController extends Controller
{
    public function index()
    {
        return view('flow::flow');
    }

    public function events(Request $request, DatabaseRecorder $databaseRecorder)
    {
        $from = $request->query('from')
            ? Carbon::createFromTimestamp($request->query('from'))
            : Carbon::now()->subMinutes(30);

        $to = $request->query('to')
            ? Carbon::createFromTimestamp($request->query('to'))
            : Carbon::now();

        return response()->json(
            $databaseRecorder->retrieve($from, $to)
                ->limit(5000)
                ->get()
        );
    }
}
