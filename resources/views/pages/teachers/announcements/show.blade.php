@extends('pages.teachers.teacher-content');
<!-- Slotted content -->
@section('content')
<div class="card shadow-sm mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">{{ $announcement->title }}</h4>
        <small class="text-muted">{{ $announcement->created_at->format('M d, Y h:i A') }}</small>
    </div>
    <div class="card-body">
        <p class="card-text" style="white-space: pre-line;">{{ $announcement->content }}</p>
    </div>
    <div class="card-footer d-flex gap-2">
        <a href="/teacher/announcements/{{ $announcement->id }}/edit" class="btn btn-warning btn-sm">Edit</a>
        <form action="/teacher/announcements/{{ $announcement->id }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
        </form>
        <a href="/teacher/announcements/show" class="btn btn-secondary btn-sm ms-auto">Back to Announcements</a>
    </div>
</div>

<script>
    $(document).ready(function() {
        // set page title
        $(document).prop('title', 'Announcement | Student Management System');
    });
</script>

@endsection