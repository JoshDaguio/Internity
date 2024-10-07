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
