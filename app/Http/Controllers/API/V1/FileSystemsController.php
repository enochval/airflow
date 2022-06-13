<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\BreezeResponse;
use App\Helpers\FileUtil;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetFileRequest;
use App\Http\Requests\UploadFileRequest;

class FileSystemsController extends Controller
{
    public function upload(UploadFileRequest $request)
    {
        $fileUtil = new FileUtil;
        $file = $fileUtil->uploadBlob($request->file('file'), $request->file_type);

        $data = $fileUtil->fileSchema($file);

        return (new BreezeResponse(
            data: $data, message: __('general.action_successful')
        ))->asSuccessful();
    }

    public function getFileUrl(GetFileRequest $request)
    {
        $data = (new FileUtil)->getFile($request->file_id);

        return (new BreezeResponse(
            data: $data, message: __('general.action_successful')
        ))->asSuccessful();
    }
}
