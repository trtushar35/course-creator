<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\CourseService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index()
    {
        $totalCourses = $this->courseService->getAllCourses()->count();
        $totalPrice = $this->courseService->getAllCourses()->sum('price');

        return view('backend.pages.dashboard', compact('totalCourses', 'totalPrice'));
    }
}
