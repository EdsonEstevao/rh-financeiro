<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;



class DashboardControllerMyhub extends Controller
{
    
    // public function index()    
    // {
    //     return view('dashboard');
    // }

    public function index(DashboardService $service)
    {
        $data = $service->getData();

        return view('dashboard', $data);
    }
    
}