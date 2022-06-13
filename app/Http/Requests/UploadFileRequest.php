<?php

namespace App\Http\Requests;

use App\Enums\FileTypeEnum;
use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => 'required|file',
            'file_type' => 'required|string|in:' . FileTypeEnum::valueList(',')
        ];
    }
}
