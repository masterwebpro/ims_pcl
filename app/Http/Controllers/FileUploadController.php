<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pod;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attachment' => 'required',
        ]
        , [
            'attachment' => 'The attachment is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        if ($request->hasFile('attachment')) {
            $uploadedFiles = [];

            foreach ($request->file('attachment') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                 $file->move('uploads', $fileName);
                $uploadedFiles[] = $fileName;
            }
            if(!empty($uploadedFiles))
            {
                $pod = Pod::find(_decode($request->id));
                $pod->update(['attachment' => json_encode($uploadedFiles)]);
                return response()->json(['success' => 'Files uploaded successfully', 'uploaded_files' => $uploadedFiles]);
            }
        }
        else{
            return response()->json(['error' => 'File upload failed']);
        }
    }
}
