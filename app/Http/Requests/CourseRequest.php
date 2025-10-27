<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:10000',
            'level' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0|max:99999.99',
            'status' => 'nullable|in:Active,Inactive',
            
            'feature_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'feature_video' => 'nullable|mimes:mp4,mov,avi,wmv,webm|max:102400',
        ];

        if ($this->has('modules') && is_array($this->input('modules'))) {
            $rules = array_merge($rules, $this->getModulesRules());
        }

        return $rules;
    }

    private function getModulesRules(): array
    {
        $rules = [];

        foreach ($this->input('modules', []) as $moduleIndex => $module) {
            $rules["modules.{$moduleIndex}.title"] = 'required|string|max:255';

            if (isset($module['contents']) && is_array($module['contents'])) {
                foreach ($module['contents'] as $contentIndex => $content) {
                    $prefix = "modules.{$moduleIndex}.contents.{$contentIndex}";
                    
                    $rules["{$prefix}.title"] = 'required|string|max:255';
                    $rules["{$prefix}.type"] = 'required|in:video,text,image,link,document';

                    if (isset($content['type'])) {
                        $rules = array_merge($rules, $this->getContentTypeRules($prefix, $content));
                    }
                }
            }
        }

        return $rules;
    }

    private function getContentTypeRules(string $prefix, array $content): array
    {
        $rules = [];

        switch ($content['type']) {
            case 'video':
                $rules["{$prefix}.video_source_type"] = 'required|in:youtube,upload';
                $rules["{$prefix}.video_length"] = 'required|string|max:20|regex:/^(\d{1,2}:)?([0-5]?\d):([0-5]\d)$/';

                if (isset($content['video_source_type'])) {
                    if ($content['video_source_type'] === 'youtube') {
                        $rules["{$prefix}.video_url"] = 'required|url|max:500';
                    }

                    if ($content['video_source_type'] === 'upload') {
                        $rules["{$prefix}.video_file"] = 'nullable|mimes:mp4,webm,ogg,mov,avi|max:524288';
                    }
                }
                break;

            case 'text':
                $rules["{$prefix}.content"] = 'nullable|string|max:50000';
                break;

            case 'image':
                $rules["{$prefix}.file"] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120';
                break;

            case 'document':
                $rules["{$prefix}.file"] = 'nullable|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:10240';
                break;

            case 'link':
                $rules["{$prefix}.link_url"] = 'nullable|url|max:500';
                break;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Course title is required',
            'price.required' => 'Course price is required',
            
            'feature_image.image' => 'Feature image must be a valid image file',
            'feature_image.max' => 'Feature image size must not exceed 2MB',
            'feature_video.max' => 'Feature video size must not exceed 100MB',
            
            'modules.*.title.required' => 'Module title is required',
            'modules.*.contents.*.title.required' => 'Content title is required',
            'modules.*.contents.*.type.required' => 'Content type is required',
            
            'modules.*.contents.*.video_source_type.required' => 'Video source type is required',
            'modules.*.contents.*.video_url.required' => 'Video URL is required',
            'modules.*.contents.*.video_length.required' => 'Video duration is required',
            'modules.*.contents.*.video_length.regex' => 'Video duration must be in format MM:SS or HH:MM:SS',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('price')) {
            $this->merge([
                'price' => (float) str_replace(',', '', $this->input('price', 0))
            ]);
        }

        if (!$this->has('status')) {
            $this->merge(['status' => 'Active']);
        }
    }
}