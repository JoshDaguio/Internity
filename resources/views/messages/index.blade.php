@extends('layouts.app')

@section('body')
<div class="container">
    <div class="row">
        <!-- Filters and Compose Button -->
        <div class="d-flex justify-content-between mb-3">
            <div>
                <select id="filter" class="form-select">
                    <option value="inbox" {{ $filter == 'inbox' ? 'selected' : '' }}>Inbox</option>
                    <option value="sent" {{ $filter == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="unread" {{ $filter == 'unread' ? 'selected' : '' }}>Unread</option>
                    <option value="read" {{ $filter == 'read' ? 'selected' : '' }}>Read</option>
                </select>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#composeMessageModal">
                <i class="bi bi-pencil"></i> Compose
            </button>
        </div>

        <!-- Sidebar with messages list -->
        <div class="col-md-4">
            <ul class="list-group" id="message-list">
                @foreach($messages as $message)
                    <li class="list-group-item {{ $message->status == 'unread' ? 'bg-light' : '' }}">
                        <a href="#" data-url="{{ route('messages.show', $message->id) }}" class="message-link d-flex align-items-center">
                            <img src="{{ $message->sender->profile && $message->sender->profile->profile_picture ? Storage::url($message->sender->profile->profile_picture) : asset('assets/img/profile-img.jpg') }}" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                            <div>
                                <h6>{{ $message->sender->name }}</h6>
                                <p>{{ Str::limit($message->subject, 30) }}</p>
                                <small>{{ $message->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Message details view -->
        <div class="col-md-8">
            <div id="message-detail" class="card">
                <div class="card-body">
                    <h5 class="card-title">No Conversation Selected</h5>
                    <p>Select a message to view its details.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@include('messages.compose')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('filter').addEventListener('change', function() {
            const filter = this.value;
            window.location.href = `{{ url('/messages') }}?filter=${filter}`;
        });

        document.querySelectorAll('.message-link').forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const url = this.dataset.url;

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const messageDetail = document.getElementById('message-detail');
                    let repliesHtml = '';

                    data.replies.forEach(reply => {
                        repliesHtml += `
                            <div class="mb-3 ms-4 d-flex align-items-start">
                                <img src="${reply.sender_picture}" alt="Profile" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                                <div>
                                    <small>From: ${reply.sender_name} | ${reply.created_at}</small>
                                    <p>${reply.body}</p>
                                </div>
                            </div>`;
                    });

                    messageDetail.innerHTML = `
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <img src="${data.sender_picture}" alt="Profile" class="rounded-circle me-2" style="width: 50px; height: 50px;">
                                <div>
                                    <h5 class="card-title">${data.subject}</h5>
                                    <small>From: ${data.sender_name} | ${data.created_at}</small>
                                    <div class="mt-3">${data.body}</div>
                                </div>
                            </div>
                            <hr>
                            <h6>Conversation</h6>
                            ${repliesHtml}
                            <form action="/messages/reply/${data.id}" method="POST" class="mt-3">
                                @csrf
                                <div class="mb-3">
                                    <label for="reply-body" class="form-label">Reply</label>
                                    <textarea class="form-control" id="reply-body" name="body" rows="4" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Send Reply</button>
                            </form>
                        </div>`;
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });
</script>

@endsection
