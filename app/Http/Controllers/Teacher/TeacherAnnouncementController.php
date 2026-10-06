<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Mail\AnnouncementPosted;
use Illuminate\Http\Request;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class TeacherAnnouncementController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacher;
        $announcements = $teacher ? $teacher->announcements : collect();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $todayAnnouncements = $announcements->filter(function ($announcement) use ($today) {
            return $announcement->created_at->isSameDay($today);
        });

        $yesterdayAnnouncements = $announcements->filter(function ($announcement) use ($yesterday) {
            return $announcement->created_at->isSameDay($yesterday);
        });

        $otherAnnouncements = $announcements->filter(function ($announcement) use ($today, $yesterday) {
            return !$announcement->created_at->isSameDay($today) && !$announcement->created_at->isSameDay($yesterday);
        });

        return view('pages.teachers.announcements.index', compact('todayAnnouncements', 'yesterdayAnnouncements', 'otherAnnouncements'));
    }

    public function create()
    {
        return view('pages.teachers.announcements.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        $teacher = auth()->user()->teacher;
        $class = $teacher ? $teacher->classes()->first() : null;

        $anc = Announcement::create([
            'title' => $request->title,
            'content' => $request->description,
            'teacher_id' => $teacher?->id,
            'class_id' => $class?->id,
            'for' => 'students',
            'created_at' => now(),
        ]);

        // Send mail to students if class and students exist
        if ($class) {
            try {
                $students = $class->students()->with('user')->get();
                foreach ($students as $student) {
                    if ($student->user && $student->user->email) {
                        try {
                            Mail::to($student->user->email)->send(new AnnouncementPosted($anc));
                        } catch (\Throwable $e) {
                            \Log::warning('Announcement mail send failed: ' . $e->getMessage());
                        }
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('Announcement mail processing failed: ' . $e->getMessage());
            }
        }

        return redirect('/teacher/announcements/show')->with('success', 'Announcement created successfully');
    }

    public function show(Announcement $announcement)
    {
        return view('pages.teachers.announcements.show', ['announcement' => $announcement]);
    }

    public function edit(Announcement $announcement)
    {
        return view('pages.teachers.announcements.edit', ['announcement' => $announcement]);
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        $announcement->update([
            'title' => $request->title,
            'content' => $request->description,
        ]);

        return redirect('/teacher/announcements/show')->with('success', 'Announcement updated successfully');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect('/teacher/announcements/show')->with('success', 'Announcement deleted successfully');
    }
}
