<?php

namespace App\Http\Controllers;

use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function index(Request $request)
    {
        $query = FileUpload::orderBy('created_at', 'desc');

        if (Auth::user()->role_id == 5) { // Student
            $userCourseId = Auth::user()->course_id;
            
            $query->where(function($q) use ($userCourseId) {
                $q->whereHas('uploader', function($q) use ($userCourseId) {
                    $q->where('role_id', 3) // Faculty role
                      ->whereHas('profile', function($q) use ($userCourseId) {
                          $q->where('course_id', $userCourseId); // Faculty from the same course
                      });
                })->orWhereHas('uploader', function($q) {
                    $q->whereIn('role_id', [1, 2]); // Admin and Super Admin roles
                });
            });
        } else {
            // For non-students, apply additional filters if requested
            if ($request->filter == 'my_uploads') {
                $query->where('uploaded_by', Auth::id());
            } elseif ($request->filter == 'other_uploads') {
                $query->where('uploaded_by', '!=', Auth::id());
            }
        }

        $files = $query->get();

        return view('file_uploads.index', compact('files'));
    }

    public function create()
    {
        return view('file_uploads.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:2048',
            'description' => 'required|string|max:255',
        ]);

        $file = $request->file('file');
        $filePath = $file->store('uploads');

        FileUpload::create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'description' => $request->description,
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->route('file_uploads.index')->with('success', 'File uploaded successfully.');
    }

    public function download($id)
    {
        $file = FileUpload::findOrFail($id);
        return Storage::download($file->file_path, $file->file_name);
    }

    public function destroy($id)
    {
        $file = FileUpload::findOrFail($id);
        Storage::delete($file->file_path);
        $file->delete();

        return redirect()->route('file_uploads.index')->with('success', 'File deleted successfully.');
    }

    public function preview($id)
    {
        $file = FileUpload::findOrFail($id);
    
        $filePath = storage_path('app/' . $file->file_path);
        $fileMimeType = mime_content_type($filePath);
    
        return response()->file($filePath, [
            'Content-Type' => $fileMimeType,
        ]);
    }

    public function edit($id)
    {
        $file = FileUpload::findOrFail($id);
        return view('file_uploads.edit', compact('file'));
    }

    public function update(Request $request, $id)
    {
        $file = FileUpload::findOrFail($id);

        $request->validate([
            'description' => 'required|string|max:255',
            'file' => 'nullable|file|max:2048', // File is optional in update
        ]);

        // Update description
        $file->description = $request->description;

        // If a new file is uploaded, update the file
        if ($request->hasFile('file')) {
            // Delete old file
            Storage::delete($file->file_path);

            // Store new file
            $uploadedFile = $request->file('file');
            $filePath = $uploadedFile->store('uploads');
            
            $file->file_name = $uploadedFile->getClientOriginalName();
            $file->file_path = $filePath;
        }

        $file->save();

        return redirect()->route('file_uploads.index')->with('success', 'File updated successfully.');
    }

    
}
