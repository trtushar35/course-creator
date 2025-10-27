<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index()
    {
        $courses = $this->courseService->getAllCourses();
        
        return view('backend.pages.course.index', compact('courses'));
    }

    public function create()
    {
        return view('backend.pages.course.form');
    }

    public function store(CourseRequest $request)
    {
        try {
            $course = $this->courseService->createCourse($request);
            
            return response()->json([
                'success' => true,
                'message' => 'Course created successfully!',
                'data' => [
                    'course_id' => $course->id,
                    'title' => $course->title
                ],
                'redirect' => route('backend.courses.index')
            ], 201);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create course. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function show(int $id)
    {
        $course = $this->courseService->findCourse($id);
        
        return view('backend.pages.course.show', compact('course'));
    }

    public function edit(int $id)
    {
        $course = $this->courseService->findCourse($id);
        
        return view('backend.pages.course.form', compact('course'));
    }

    public function update(CourseRequest $request, int $id)
    {
        try {
            $course = $this->courseService->updateCourse($id, $request);
            
            return response()->json([
                'success' => true,
                'message' => 'Course updated successfully!',
                'data' => [
                    'course_id' => $course->id,
                    'title' => $course->title
                ],
                'redirect' => route('backend.courses.index')
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update course. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->courseService->deleteCourse($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Course deleted successfully!'
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete course. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}