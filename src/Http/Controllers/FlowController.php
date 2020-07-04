<?php

namespace EricPridham\Flow\Http\Controllers;

use Carbon\Carbon;
use EricPridham\Flow\Recorder\DatabaseRecorder;
use Illuminate\Routing\Controller;

class FlowController extends Controller
{
    public function index()
    {
        return view('flow::flow');
    }

    public function events(DatabaseRecorder $databaseRecorder)
    {
        return response()->json(
            $databaseRecorder->retrieve()
                ->where('created_at', '>', Carbon::now()->subMinutes(30))
                ->get()
        );
    }
}
