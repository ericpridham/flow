<?php

namespace EricPridham\Flow\Http\Controllers;

use Carbon\Carbon;
use EricPridham\Flow\Flow;
use Illuminate\Routing\Controller;

class FlowController extends Controller
{
    public function index()
    {
        return view('flow::flow');
    }

    public function events(Flow $flow)
    {
        return response()->json(
            $flow->retrieve()
                ->where('created_at', '>', Carbon::now()->subMinutes(30))
                ->get()
        );
    }
}
