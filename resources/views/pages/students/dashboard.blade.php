@extends('pages.students.student-content')

@section('content')
    @if (session('greeting'))
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: "{{session('greeting')}}"
            });
        </script>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
        <h2>Welcome, {{ $student ? $student->first_name . ' ' . $student->last_name : auth()->user()->email }}</h2>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <i class="fa fa-bullhorn me-2"></i>
            <h5 class="mb-0">Announcements & Messages</h5>
        </div>
        <div class="card-body">
            @if(isset($announcements) && $announcements->count() > 0)
                <div class="list-group">
                    @foreach($announcements as $announcement)
                        <div class="list-group-item list-group-item-action flex-column align-items-start mb-3 border rounded shadow-sm">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h5 class="mb-1 text-primary">{{ $announcement->title }}</h5>
                                <small class="text-muted">{{ $announcement->created_at->diffForHumans() }} ({{ $announcement->created_at->format('M d, Y') }})</small>
                            </div>
                            @if($announcement->teacher)
                                <small class="text-secondary d-block mb-2">
                                    <i class="fa fa-user me-1"></i> Posted by: {{ $announcement->teacher->salutation }} {{ $announcement->teacher->first_name }} {{ $announcement->teacher->last_name }}
                                    @if($announcement->class)
                                        <span class="badge bg-secondary ms-2">{{ $announcement->class->name }}</span>
                                    @endif
                                </small>
                            @endif
                            <p class="mb-1" style="white-space: pre-line;">{{ $announcement->content }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fa fa-inbox fa-3x mb-3 text-secondary"></i>
                    <p class="mb-0">No announcements or messages posted yet.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
