@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5>{{ $originalMessage->subject }}</h5>
            <small>From: {{ $originalMessage->sender->name }}
            @if($originalMessage->recipient)
                | To: {{ $originalMessage->recipient->name }}
            @endif
            | {{ $originalMessage->created_at->format('M d, Y h:i A') }}</small>
        </div>
        <div class="card-body">
            <p>{{ $originalMessage->body }}</p>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h6>Conversation</h6>
            <hr>
            <div id="conversation-thread">
                @foreach($conversation as $msg)
                    <div class="mb-3 d-flex align-items-start">
                        <div>
                            <small>From: {{ $msg->sender->name }} | {{ $msg->created_at->format('M d, Y h:i A') }}</small>
                            <p>{{ $msg->body }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <form action="{{ route('messages.reply', $originalMessage->id) }}" method="POST" class="mt-3">
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
