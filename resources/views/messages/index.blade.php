@extends('layouts.app')

@section('body')

<div class="pagetitle">
    <h1>Inbox</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item active">Messages</li>
        </ol>
    </nav>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

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
            <i class="bi bi-envelope-plus"></i>
        </button>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm" id="sidebar-card">
            @if($messages->isEmpty())
                <div id="no-messages-card">
                    <i class="bi bi-envelope-open no-messages-icon"></i>
                    <p class="no-messages-text">No messages available</p>
                </div>
            @else
                <ul class="list-group" id="message-list">
                    @foreach($messages as $message)
                        @include('messages.partials.message-row', ['message' => $message])
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="col-md-8">
        <div id="message-detail" class="card shadow-sm empty-state">
            <div class="card-body d-flex flex-column align-items-center justify-content-center" style="height: calc(100vh - 160px);">
                <i class="bi bi-chat-dots no-conversation-icon mb-3" style="font-size: 3rem; color: #a90b06;"></i>
                <h5 class="card-title">No Conversation Selected</h5>
                <p>Select a message to view its details.</p>
            </div>
        </div>
    </div>

</div>

@include('messages.compose')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filter = document.getElementById('filter');
        filter.addEventListener('change', function() {
            window.location.href = `{{ url('/messages') }}?filter=${this.value}`;
        });

        document.querySelectorAll('.message-link').forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const url = this.dataset.url;
                const listItem = this.closest('.list-group-item'); // Get the parent list item

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        console.error('Network response was not ok:', response);
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        console.error('Error from server:', data.error);
                        alert('An error occurred while loading the message details. Please try again.');
                        return;
                    }

                    // Remove 'unread' class when the message is read
                    if (listItem.classList.contains('unread')) {
                        listItem.classList.remove('unread');
                    }

                    const messageDetail = document.getElementById('message-detail');
                    let repliesHtml = '';

                    data.replies.forEach(reply => {
                        repliesHtml += `
                            <div class="mb-3 ms-4 d-flex align-items-start">
                                <div>
                                    <small><strong>${reply.sender_name}</strong> | ${reply.created_at}</small>
                                    <p>${reply.body}</p>
                                </div>
                            </div>`;
                    });

                    // Update the message-detail card to include the body
                    messageDetail.innerHTML = `
                        <div class="card-body d-flex align-items-start">
                            <div>
                                <h5 class="card-title">${data.subject}</h5>
                                <small><strong class="sender-name">${data.sender_name}</strong> 
                                ${data.recipient_name ? `| To: <strong>${data.recipient_name}</strong>` : ''}
                                | ${data.created_at}</small>
                                <p class="mt-3">${data.body}</p>
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
                        </form>`;

                    messageDetail.classList.remove('empty-state');
                })
                .catch(error => {
                    console.error('Error occurred:', error);
                    alert('An error occurred while loading the message details. Please try again.');
                });
            });
        });
    });
</script>





@endsection
