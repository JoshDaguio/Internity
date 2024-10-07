@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <img src="{{ $message->sender->profile && $message->sender->profile->profile_picture ? Storage::url($message->sender->profile->profile_picture) : asset('assets/img/profile-img.jpg') }}" class="rounded-circle me-2" style="width: 50px; height: 50px;">
            <div>
                <h5>{{ $message->subject }}</h5>
                <small>From: {{ $message->sender->name }} | {{ $message->created_at->format('M d, Y h:i A') }}</small>
            </div>
        </div>
        <div class="card-body">
            <p>{{ $message->body }}</p>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h6>Conversation</h6>
            <hr>
            <div id="conversation-thread">
                <p><strong>Original Message:</strong></p>
                <div class="mb-3 d-flex align-items-start">
                    <img src="{{ $message->sender->profile && $message->sender->profile->profile_picture ? Storage::url($message->sender->profile->profile_picture) : asset('assets/img/profile-img.jpg') }}" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                    <div>
                        <small>From: {{ $message->sender->name }} | {{ $message->created_at->format('M d, Y h:i A') }}</small>
                        <p>{{ $message->body }}</p>
                    </div>
                </div>

                @foreach($message->replies as $reply)
                    <div class="mb-3 ms-4 d-flex align-items-start">
                        <img src="{{ $reply->sender->profile && $reply->sender->profile->profile_picture ? Storage::url($reply->sender->profile->profile_picture) : asset('assets/img/profile-img.jpg') }}" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                        <div>
                            <small>From: {{ $reply->sender->name }} | {{ $reply->created_at->format('M d, Y h:i A') }}</small>
                            <p>{{ $reply->body }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <form action="{{ route('messages.reply', $message->id) }}" method="POST" class="mt-3">
                @csrf
                <div class="mb-3">
                    <label for="reply-body" class="form-label">Reply</label>
                    <textarea class="form-control" id="reply-body" name="body" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Reply</button>
            </form>
        </div>
    </div>
</div>
@endsection
