<?php

namespace App\Http\Controllers;

use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function index(Request $request)
    {
        $query = FileUpload::orderBy('created_at', 'desc');

        if (Auth::user()->role_id == 5) { // Student
            // Original student query logic
        } else {
            // For Admins or Super Admins, include trashed files
            if (in_array(Auth::user()->role_id, [1, 2])) {
                $query->withTrashed(); // Include soft deleted files
            }
    
            // Non-students can filter files
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

        $fileUpload = FileUpload::create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'description' => $request->description,
            'uploaded_by' => Auth::id(),
        ]);


        // Log the file upload
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Uploaded File',
            'target' => $fileUpload->file_name,
            'changes' => json_encode([
                'file_name' => $fileUpload->file_name,
                'description' => $fileUpload->description,
            ]),
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

        // Authorization check
        if (Auth::id() != $file->uploaded_by && !in_array(Auth::user()->role_id, [1, 2])) {
            abort(403, 'Unauthorized action.');
        }

        // Soft delete the file record 
        $file->delete();

            // Log the file deletion
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Deleted File',
            'target' => $file->file_name,
            'changes' => json_encode(['file_name' => $file->file_name]),
        ]);
        
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

        // Authorization check
        if (Auth::id() != $file->uploaded_by && !in_array(Auth::user()->role_id, [1, 2])) {
            abort(403, 'Unauthorized action.');
        }

        return view('file_uploads.edit', compact('file'));
    }

    public function update(Request $request, $id)
    {
        $file = FileUpload::findOrFail($id);

        // Authorization check
        if (Auth::id() != $file->uploaded_by && !in_array(Auth::user()->role_id, [1, 2])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'description' => 'required|string|max:255',
            'file' => 'nullable|file|max:2048', // File is optional in update
        ]);

        $updatedFields = [];
    
        // Update description
        if ($file->description != $request->description) {
            $updatedFields['description'] = ['old' => $file->description, 'new' => $request->description];
            $file->description = $request->description;
        }

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

        // Log the file update if changes were made
        if (!empty($updatedFields)) {
            ActivityLog::create([
                'admin_id' => Auth::id(),
                'action' => 'Updated File',
                'target' => $file->file_name,
                'changes' => json_encode($updatedFields),
            ]);
        }

        return redirect()->route('file_uploads.index')->with('success', 'File updated successfully.');
    }

    public function restore($id)
    {
        $file = FileUpload::withTrashed()->findOrFail($id);

        // Authorization check
        if (!in_array(Auth::user()->role_id, [1, 2])) {
            abort(403, 'Unauthorized action.');
        }

        $file->restore();

        // Log the file restoration
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Restored File',
            'target' => $file->file_name,
            'changes' => json_encode(['file_name' => $file->file_name]),
        ]);

        return redirect()->route('file_uploads.index')->with('success', 'File restored successfully.');
    }


    
}
