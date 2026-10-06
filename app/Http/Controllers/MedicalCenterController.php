<?php

namespace App\Http\Controllers;

use App\Models\MedicalCenter;
use Illuminate\Http\Request;

class MedicalCenterController extends Controller
{
    public function index(Request $request)
    {
        $query = MedicalCenter::where('is_verified', true);

        if ($city = $request->get('city')) {
            $query->where('city', $city);
        }

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('specialty', 'like', "%{$search}%");
            });
        }

        $centers = $query->orderBy('order_index')->get();
        $cities = MedicalCenter::getCities();
        $selectedCity = $request->get('city');

        return view('medical-centers.index', compact('centers', 'cities', 'selectedCity'));
    }
}
