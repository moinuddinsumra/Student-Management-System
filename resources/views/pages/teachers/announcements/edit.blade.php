@extends('pages.teachers.teacher-content');
<!-- Slotted content -->
@section('content')
<h2>Edit Announcement</h2>
<form action="/teacher/announcements/{{$announcement->id}}" method="post" class="shadow-lg p-3 mb-5 mt-3 bg-body-tertiary rounded">
    @csrf
    @method('PATCH')

    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $announcement->title) }}" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="5" required>{{ old('description', $announcement->content) }}</textarea>
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-primary">Update Announcement</button>
        <a href="/teacher/announcements/show" class="btn btn-secondary">Cancel</a>
    </div>
</form>
<!--  -->

<script>
    $(document).ready(function() {
        // set page title
        $(document).prop('title', 'Edit Announcement | Student Management System');
    });
</script>

@endsection