<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Models\Course;
use App\Models\AcceptedInternship;
use Auth;

class MessageController extends Controller
{
    // Display the inbox
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'inbox');
        $userId = Auth::id();

        // Get messages based on the filter
        $messagesQuery = Message::where('recipient_id', $userId);

        if ($filter === 'unread') {
            $messagesQuery->where('status', 'unread');
        } elseif ($filter === 'read') {
            $messagesQuery->where('status', 'read');
        } elseif ($filter === 'sent') {
            $messagesQuery = Message::where('sender_id', $userId);
        }

        $messages = $messagesQuery->orderBy('created_at', 'desc')->get();

        return view('messages.index', compact('messages', 'filter'));
    }

    // Compose a new message
    public function create()
    {
        $user = Auth::user();
        $recipients = $this->getRecipientsBasedOnRole($user);

        return view('messages.compose', compact('recipients'));
    }

    // Store a new message
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $request->recipient_id,
            'subject' => $request->subject,
            'body' => $request->body,
            'status' => 'unread',
        ]);

        return redirect()->route('messages.index')->with('success', 'Message sent successfully!');
    }

    public function show($id)
    {
        try {
            // Fetch the message with sender, recipient, and their profiles
            $message = Message::with([
                'sender.profile',
                'recipient.profile',
                'replies.sender.profile'
            ])->findOrFail($id);
    
            // Mark as read if the current user is the recipient of the initial message
            if (Auth::id() === $message->recipient_id && $message->status === 'unread') {
                $message->update(['status' => 'read']);
            }
    
            // Fetch all messages in the conversation, including replies, sorted by creation time
            $conversation = Message::with(['sender.profile', 'recipient.profile'])
                ->where('id', $message->id)
                ->orWhere('parent_id', $message->id)
                ->orderBy('created_at')
                ->get();
    
            if (request()->ajax()) {
                return response()->json([
                    'id' => $message->id,
                    'subject' => $message->subject,
                    'body' => $message->body,
                    'sender_name' => $message->sender->name,
                    'recipient_name' => $message->recipient ? $message->recipient->name : null,
                    'created_at' => $message->created_at->format('M d, Y h:i A'),
                    'replies' => $conversation->map(function ($msg) {
                        return [
                            'id' => $msg->id,
                            'body' => $msg->body,
                            'sender_name' => $msg->sender->name,
                            'created_at' => $msg->created_at->format('M d, Y h:i A')
                        ];
                    })
                ], 200);
            }
    
            return view('messages.show', ['originalMessage' => $message, 'conversation' => $conversation]);
        } catch (\Exception $e) {
            \Log::error("Error in message show: " . $e->getMessage());
            return response()->json(['error' => 'Failed to load message details. Please try again.'], 500);
        }
    }
    
    
    private function getProfilePicture($user)
    {
        if ($user->profile && $user->profile->profile_picture) {
            $path = 'public/' . $user->profile->profile_picture;
            \Log::info("Profile picture path: " . $path);
    
            if (Storage::disk('public')->exists($user->profile->profile_picture)) {
                $url = Storage::url($user->profile->profile_picture);
                \Log::info("Profile picture URL: " . $url);
                return $url;
            } else {
                \Log::error("Profile picture not found or inaccessible: " . $path);
            }
        }
    
        return asset('assets/img/profile-img.jpg');
    }
    
    
    
    


    // Fetch recipients based on user role
    private function getRecipientsBasedOnRole($user)
    {
        $roleId = $user->role_id;

        if ($roleId == 1 || $roleId == 2) {
            // Super Admins/Admins can message any role
            return User::where('status_id', 1)->get();
        } elseif ($roleId == 3) {
            // Faculty can message admins, super admins, and students in the same course
            $students = User::where('role_id', 5)
                            ->where('status_id', 1)
                            ->where('course_id', $user->course_id)
                            ->get();
            $admins = User::whereIn('role_id', [1, 2])
                        ->where('status_id', 1)
                        ->get();

            return $students->merge($admins);
        } elseif ($roleId == 4) {
            // Company can message admins, super admins, and interns under them
            $interns = AcceptedInternship::where('company_id', $user->id)
                                        ->with('student')
                                        ->get()
                                        ->pluck('student');
            $admins = User::whereIn('role_id', [1, 2])
                        ->where('status_id', 1)
                        ->get();

            return $interns->merge($admins);
        } elseif ($roleId == 5) {
            // Students can message admins, super admins, faculty in the same course, and the company they are under
            $faculty = User::where('role_id', 3)
                        ->where('status_id', 1)
                        ->where('course_id', $user->course_id)
                        ->get();
            $admins = User::whereIn('role_id', [1, 2])
                        ->where('status_id', 1)
                        ->get();

            $company = AcceptedInternship::where('student_id', $user->id)
                                        ->with('company')
                                        ->first()
                                        ->company ?? collect();

            return $faculty->merge($admins)->push($company);
        }

        return collect(); // Return empty collection if none match
    }

    public function getCourses()
    {
        $courses = Course::all(['id', 'course_code']);
        return response()->json($courses);
    }

    public function getRecipients($role, Request $request)
    {
        $user = Auth::user();
        $courseId = $request->input('course');
        $recipients = collect();

        if ($user->role_id == 1 || $user->role_id == 2) {
            // Super Admins/Admins can message any role
            $query = User::where('status_id', 1);
            if ($role == 'admins') {
                $query->whereIn('role_id', [1, 2]);
            } elseif ($role == '3' || $role == '5') {
                $query->where('role_id', $role);
                if ($courseId) {
                    $query->where('course_id', $courseId);
                }
            } elseif ($role == '4') { // Company role
                $query->where('role_id', 4);
            }
            $recipients = $query->get(['id', 'name']);
        } elseif ($user->role_id == 3) {
            // Faculty can message students in the same course
            if ($role == '5') {
                $recipients = User::where('role_id', 5)
                    ->where('course_id', $user->course_id)
                    ->where('status_id', 1)
                    ->get(['id', 'name']);
            } else if ($role == 'admins') {
                $recipients = User::whereIn('role_id', [1, 2])
                    ->where('status_id', 1)
                    ->get(['id', 'name']);
            }
        } elseif ($user->role_id == 4) {
            // Company can message admins and their accepted interns
            if ($role == 'admins') {
                $recipients = User::whereIn('role_id', [1, 2])
                    ->where('status_id', 1)
                    ->get(['id', 'name']);
            } elseif ($role == '5') {
                $recipients = AcceptedInternship::where('company_id', $user->id)
                    ->whereHas('student', function ($query) {
                        $query->where('status_id', 1);
                    })
                    ->with('student')
                    ->get()
                    ->pluck('student');
            }
        } elseif ($user->role_id == 5) {
            // Students can message admins, faculty in their course, and their company
            if ($role == 'admins') {
                $recipients = User::whereIn('role_id', [1, 2])
                    ->where('status_id', 1)
                    ->get(['id', 'name']);
            } elseif ($role == '3') {
                $recipients = User::where('role_id', 3)
                    ->where('course_id', $user->course_id)
                    ->where('status_id', 1)
                    ->get(['id', 'name']);
            } elseif ($role == '4') {
                $company = AcceptedInternship::where('student_id', $user->id)
                    ->with('company')
                    ->first()
                    ->company;

                if ($company) {
                    $recipients = collect([$company]);
                }
            }
        }

        return response()->json($recipients);
    }

    
    public function reply(Request $request, $id)
    {
        $request->validate([
            'body' => 'required|string',
        ]);
    
        $originalMessage = Message::findOrFail($id);
    
        // Reply to the sender of the original message
        Message::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $originalMessage->sender_id,
            'subject' => 'Re: ' . $originalMessage->subject,
            'body' => $request->body,
            'status' => 'unread',
            'parent_id' => $originalMessage->id, // Set the parent ID
        ]);
    
        return redirect()->route('messages.index', $id)->with('success', 'Reply sent successfully!');
    }

}
