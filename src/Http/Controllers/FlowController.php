<?php

namespace EricPridham\Flow\Http\Controllers;

use Illuminate\Routing\Controller;

class FlowController extends Controller
{
    public function index()
    {
        return view('flow::flow');
    }
}
