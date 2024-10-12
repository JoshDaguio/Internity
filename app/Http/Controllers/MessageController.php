<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Models\Course;
use App\Models\AcceptedInternship;
use Auth;
use App\Mail\MessageNotification;
use Illuminate\Support\Facades\Mail;

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

        $message = Message::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $request->recipient_id,
            'subject' => $request->subject,
            'body' => $request->body,
            'status' => 'unread',
        ]);

        // Send email notification
        $recipient = User::find($request->recipient_id);
        $sender = Auth::user();
        $messageDetails = [
            'title' => 'New Message Received',
            'recipient_name' => $recipient->name,
            'sender_name' => $sender->name,
            'subject' => $message->subject,
            'body_snippet' => substr($message->body, 0, 100) . '...',
            'created_at' => $message->created_at->format('M d, Y h:i A'),
            'action' => 'sent you a message',
        ];

    Mail::to($recipient->email)->send(new MessageNotification($messageDetails));

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
    
            // Find the root message of the conversation
            $rootMessage = $message;
            while ($rootMessage->parent_id) {
                $rootMessage = Message::with(['sender.profile', 'recipient.profile'])
                    ->findOrFail($rootMessage->parent_id);
            }
    
            // Fetch all messages recursively in the conversation from the root message
            $conversation = $this->fetchFullConversation($rootMessage);
    
            // Mark as read if the current user is the recipient of the initial message
            if (Auth::id() === $message->recipient_id && $message->status === 'unread') {
                $message->update(['status' => 'read']);
            }
    
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
    
            return view('messages.show', [
                'originalMessage' => $rootMessage,
                'conversation' => $conversation
            ]);
        } catch (\Exception $e) {
            \Log::error("Error in message show: " . $e->getMessage());
            return response()->json(['error' => 'Failed to load message details. Please try again.'], 500);
        }
    }
    
    /**
     * Recursively fetch all messages in the conversation starting from the root message.
     *
     * @param \App\Models\Message $rootMessage
     * @return \Illuminate\Support\Collection
     */
    private function fetchFullConversation($rootMessage)
    {
        $messages = collect([$rootMessage]);
        
        // Get all replies for the current message
        $replies = Message::with(['sender.profile', 'recipient.profile'])
            ->where('parent_id', $rootMessage->id)
            ->orderBy('created_at')
            ->get();
    
        // Recursively fetch replies
        foreach ($replies as $reply) {
            $messages = $messages->merge($this->fetchFullConversation($reply));
        }
    
        return $messages;
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
            $query = User::with('profile')->where('status_id', 1);
            if ($role == 'admins') {
                $query->whereIn('role_id', [1, 2]);
            } elseif (in_array($role, ['3', '5'])) {
                $query->where('role_id', $role);
                if ($courseId) {
                    $query->where('course_id', $courseId);
                }
            } elseif ($role == '4') { // Company role
                $query->where('role_id', 4); // Get companies from User table
            }
    
            // Combine first and last name from the profile, or use company name directly
            $recipients = $query->get()->map(function ($recipient) {
                return [
                    'id' => $recipient->id,
                    'name' => $recipient->role_id == 4
                        ? $recipient->name // Company name from User table
                        : $recipient->profile->first_name . ' ' . $recipient->profile->last_name, // For others, use profile
                ];
            });
        } elseif ($user->role_id == 3) {
            // Faculty can message students in the same course
            if ($role == '5') {
                $recipients = User::with('profile')
                    ->where('role_id', 5)
                    ->where('course_id', $user->course_id)
                    ->where('status_id', 1)
                    ->get()
                    ->map(function ($recipient) {
                        return [
                            'id' => $recipient->id,
                            'name' => $recipient->profile->first_name . ' ' . $recipient->profile->last_name,
                        ];
                    });
            } elseif ($role == 'admins') {
                $recipients = User::with('profile')
                    ->whereIn('role_id', [1, 2])
                    ->where('status_id', 1)
                    ->get()
                    ->map(function ($recipient) {
                        return [
                            'id' => $recipient->id,
                            'name' => $recipient->profile->first_name . ' ' . $recipient->profile->last_name,
                        ];
                    });
            }
        } elseif ($user->role_id == 4) {
            // Company can message admins and their accepted interns
            if ($role == 'admins') {
                $recipients = User::with('profile')
                    ->whereIn('role_id', [1, 2])
                    ->where('status_id', 1)
                    ->get()
                    ->map(function ($recipient) {
                        return [
                            'id' => $recipient->id,
                            'name' => $recipient->profile->first_name . ' ' . $recipient->profile->last_name,
                        ];
                    });
            } elseif ($role == '5') {
                $recipients = AcceptedInternship::where('company_id', $user->id)
                    ->whereHas('student', function ($query) {
                        $query->where('status_id', 1);
                    })
                    ->with('student.profile')
                    ->get()
                    ->pluck('student')
                    ->map(function ($student) {
                        return [
                            'id' => $student->id,
                            'name' => $student->profile->first_name . ' ' . $student->profile->last_name,
                        ];
                    });
            }
        } elseif ($user->role_id == 5) {
            // Students can message admins, faculty in their course, and their company
            if ($role == 'admins') {
                $recipients = User::with('profile')
                    ->whereIn('role_id', [1, 2])
                    ->where('status_id', 1)
                    ->get()
                    ->map(function ($recipient) {
                        return [
                            'id' => $recipient->id,
                            'name' => $recipient->profile->first_name . ' ' . $recipient->profile->last_name,
                        ];
                    });
            } elseif ($role == '3') {
                $recipients = User::with('profile')
                    ->where('role_id', 3)
                    ->where('course_id', $user->course_id)
                    ->where('status_id', 1)
                    ->get()
                    ->map(function ($recipient) {
                        return [
                            'id' => $recipient->id,
                            'name' => $recipient->profile->first_name . ' ' . $recipient->profile->last_name,
                        ];
                    });
            } elseif ($role == '4') {
                $company = AcceptedInternship::where('student_id', $user->id)
                    ->with('company')
                    ->first()
                    ->company;
    
                if ($company) {
                    $recipients = collect([[
                        'id' => $company->id,
                        'name' => $company->name, // Get company name from users table
                    ]]);
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
        $reply = Message::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $originalMessage->sender_id,
            'subject' => 'Re: ' . $originalMessage->subject,
            'body' => $request->body,
            'status' => 'unread',
            'parent_id' => $originalMessage->id, // Set the parent ID
        ]);

        // Send email notification
        $recipient = User::find($originalMessage->sender_id);
        $sender = Auth::user();
        $messageDetails = [
            'title' => 'Reply Received',
            'recipient_name' => $recipient->name,
            'sender_name' => $sender->name,
            'subject' => $reply->subject,
            'body_snippet' => substr($reply->body, 0, 100) . '...',
            'created_at' => $reply->created_at->format('M d, Y h:i A'),
            'action' => 'replied to your message',
        ];

        Mail::to($recipient->email)->send(new MessageNotification($messageDetails));
    
        return redirect()->route('messages.index', $id)->with('success', 'Reply sent successfully!');
    }

}
