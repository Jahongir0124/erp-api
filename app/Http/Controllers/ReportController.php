<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(protected readonly ReportService $reportService)
    {}

    public function sales_report(Request $request)
    {
        $data = $this->reportService->sales($request);
        return response()->json($data);
    }

    public function orders(Request $request)
    {
        return response()->json(
            $this->reportService->orders($request)
        );
    }

    public function inventory(Request $request)
    {
        return response()->json(
            $this->reportService->inventory($request)
        );
    }

    public function products(Request $request)
    {
        return response()->json(
            $this->reportService->products($request)
        );
    }
}
