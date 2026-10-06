<?php

namespace App\Http\Controllers;

use App\Models\InterventionDay;
use Illuminate\Http\Request;

class RoadmapController extends Controller
{
    public function index()
    {
        $days = InterventionDay::orderBy('day_number')->get()->groupBy('week_number');
        $weeks = InterventionDay::getWeeks();

        return view('roadmap.index', compact('days', 'weeks'));
    }
}
