<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseContent;
use App\Http\Requests\CourseRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class CourseService
{
    public function getAllCourses()
    {
        return Course::with(['modules.contents' => function ($query) {
            $query->orderBy('order');
        }])
        ->latest()
        ->get();
    }

    public function findCourse(int $id)
    {
        return Course::with(['modules.contents' => function ($query) {
            $query->orderBy('order');
        }])->findOrFail($id);
    }

    public function createCourse(CourseRequest $request)
    {
        return DB::transaction(function () use ($request) {
            try {
                $courseData = $this->prepareCourseData($request);
                $course = Course::create($courseData);
                
                if ($request->has('modules')) {
                    $this->createModulesAndContents($course, $request->input('modules'), $request);
                }
                
                return $course->load(['modules.contents']);
                
            } catch (Exception $e) {
                Log::error('Course creation failed: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function updateCourse(int $id, CourseRequest $request)
    {
        return DB::transaction(function () use ($id, $request) {
            try {
                $course = $this->findCourse($id);
                $courseData = $this->prepareCourseData($request, $course);
                $course->update($courseData);
                
                // Delete existing modules and contents
                $this->deleteModulesAndContents($course);
                
                // Create new modules and contents
                if ($request->has('modules')) {
                    $this->createModulesAndContents($course, $request->input('modules'), $request);
                }
                
                return $course->fresh(['modules.contents']);
                
            } catch (Exception $e) {
                Log::error('Course update failed: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function deleteCourse(int $id)
    {
        return DB::transaction(function () use ($id) {
            try {
                $course = $this->findCourse($id);
                $this->deleteAllCourseFiles($course);
                return $course->delete();
                
            } catch (Exception $e) {
                Log::error('Course deletion failed: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    private function prepareCourseData(CourseRequest $request, ?Course $course = null)
    {
        $courseData = $request->only(['title', 'summary', 'level', 'category', 'price', 'status']);
        
        if ($request->hasFile('feature_image')) {
            if ($course && $course->feature_image) {
                Storage::disk('public')->delete($course->feature_image);
            }
            $courseData['feature_image'] = $this->uploadFile(
                $request->file('feature_image'), 
                'courses/images'
            );
        }
        
        if ($request->hasFile('feature_video')) {
            if ($course && $course->feature_video) {
                Storage::disk('public')->delete($course->feature_video);
            }
            $courseData['feature_video'] = $this->uploadFile(
                $request->file('feature_video'), 
                'courses/videos'
            );
        }
        
        return $courseData;
    }

    private function createModulesAndContents(Course $course, array $modules, CourseRequest $request)
    {
        foreach ($modules as $moduleIndex => $moduleData) {
            $module = $course->modules()->create([
                'title' => $moduleData['title'] ?? 'Untitled Module',
                'order' => $moduleIndex,
                'status' => 'Active'
            ]);
            
            if (isset($moduleData['contents']) && is_array($moduleData['contents'])) {
                $this->createContents($module, $moduleData['contents'], $moduleIndex, $request);
            }
        }
    }

    private function createContents(CourseModule $module, array $contents, int $moduleIndex, CourseRequest $request)
    {
        foreach ($contents as $contentIndex => $contentData) {
            $content = [
                'title' => $contentData['title'] ?? 'Untitled Content',
                'type' => $contentData['type'] ?? 'text',
                'order' => $contentIndex,
                'status' => 'Active'
            ];
            
            $content = $this->processContentByType($content, $contentData, $moduleIndex, $contentIndex, $request);
            
            $module->contents()->create($content);
        }
    }

    private function processContentByType(array $content, array $contentData, int $moduleIndex, int $contentIndex, CourseRequest $request)
    {
        switch ($content['type']) {
            case 'text':
                $content['content'] = $contentData['content'] ?? null;
                break;
                
            case 'video':
                $content = $this->processVideoContent($content, $contentData, $moduleIndex, $contentIndex, $request);
                break;
                
            case 'image':
            case 'document':
                $content = $this->processFileContent($content, $contentData, $moduleIndex, $contentIndex, $request);
                break;
                
            case 'link':
                $content['link_url'] = $contentData['link_url'] ?? null;
                break;
        }
        
        return $content;
    }

    private function processVideoContent(array $content, array $contentData, int $moduleIndex, int $contentIndex, CourseRequest $request)
    {
        $content['video_source_type'] = $contentData['video_source_type'] ?? null;
        $content['video_url'] = $contentData['video_url'] ?? null;
        $content['video_length'] = $contentData['video_length'] ?? null;
        
        $fileKey = "modules.{$moduleIndex}.contents.{$contentIndex}.video_file";
        
        if ($request->hasFile($fileKey)) {
            $content['video_file'] = $this->uploadFile(
                $request->file($fileKey), 
                'courses/content/videos'
            );
        } elseif (isset($contentData['video_file_old']) && !empty($contentData['video_file_old'])) {

            $content['video_file'] = $contentData['video_file_old'];
        } else {
            $content['video_file'] = null;
        }
        
        return $content;
    }

    private function processFileContent(array $content, array $contentData, int $moduleIndex, int $contentIndex, CourseRequest $request)
    {
        $fileKey = "modules.{$moduleIndex}.contents.{$contentIndex}.file";
        
        if ($request->hasFile($fileKey)) {
            $uploadedFile = $request->file($fileKey);
            
            $directory = $content['type'] === 'image' 
                ? 'courses/content/images' 
                : 'courses/content/documents';
            
            $content['file'] = $this->uploadFile($uploadedFile, $directory);
            
            Log::info("File uploaded successfully", [
                'type' => $content['type'],
                'path' => $content['file'],
                'file_key' => $fileKey
            ]);
        } elseif (isset($contentData['file_old']) && !empty($contentData['file_old'])) {

            $content['file'] = $contentData['file_old'];
            
            Log::info("Using old file", [
                'type' => $content['type'],
                'path' => $content['file']
            ]);
        } else {
            $content['file'] = null;
            
            Log::warning("No file found", [
                'type' => $content['type'],
                'file_key' => $fileKey,
                'has_file' => $request->hasFile($fileKey),
                'content_data' => array_keys($contentData)
            ]);
        }
        
        return $content;
    }

    private function deleteModulesAndContents(Course $course)
    {
        foreach ($course->modules as $module) {
            foreach ($module->contents as $content) {
                if ($content->video_file) {
                    Storage::disk('public')->delete($content->video_file);
                }
                if ($content->file) {
                    Storage::disk('public')->delete($content->file);
                }
            }
            $module->contents()->delete();
        }
        
        $course->modules()->delete();
    }

    private function deleteAllCourseFiles(Course $course)
    {
        if ($course->feature_image) {
            Storage::disk('public')->delete($course->feature_image);
        }
        if ($course->feature_video) {
            Storage::disk('public')->delete($course->feature_video);
        }
        
        foreach ($course->modules as $module) {
            foreach ($module->contents as $content) {
                if ($content->video_file) {
                    Storage::disk('public')->delete($content->video_file);
                }
                if ($content->file) {
                    Storage::disk('public')->delete($content->file);
                }
            }
        }
    }

    private function uploadFile($file, string $directory)
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($directory, $filename, 'public');
    }
}