<li class="nav-item dropdown">
    <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
        <i class="bi bi-chat-left-text"></i>
        <span class="badge bg-success badge-number">{{ $unreadMessagesCount }}</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
        <li class="dropdown-header">
            You have {{ $unreadMessagesCount }} new message(s)
            <a href="{{ route('messages.index') }}"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
        </li>
        <li>
            <hr class="dropdown-divider">
        </li>
        @foreach($unreadMessages as $unreadMessage)
            <li class="message-item">
                <a href="{{ route('messages.show', $unreadMessage->id) }}">
                    <img src="{{ $unreadMessage->sender->profile_picture ? asset('storage/' . $unreadMessage->sender->profile_picture) : asset('assets/img/profile-img.jpg') }}" class="rounded-circle">
                    <div>
                        <h4>{{ $unreadMessage->sender->name }}</h4>
                        <p>{{ Str::limit($unreadMessage->subject, 20) }}</p>
                        <p>{{ $unreadMessage->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </a>
            </li>
            <li>
                <hr class="dropdown-divider">
            </li>
        @endforeach
        <li class="dropdown-footer">
            <a href="{{ route('messages.index') }}">Show all messages</a>
        </li>
    </ul>
</li>
