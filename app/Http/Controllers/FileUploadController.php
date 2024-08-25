<?php

namespace App\Http\Controllers;

use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function index()
    {
        $files = FileUpload::orderBy('created_at', 'desc')->get();
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
    
}
