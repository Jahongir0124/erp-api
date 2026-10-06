<?php

namespace App\Http\Controllers;

use App\Http\Resources\DashboardResource;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    

    public function __construct(protected readonly DashboardService $dashboardService)
    {}



    public function index()
    {
        return new DashboardResource(
            $this->dashboardService->index()
        );
    }
}
