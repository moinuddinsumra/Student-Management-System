<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Student;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentStdController extends Controller
{
    public function index()
    {
        $student = Student::where('user_id', auth()->id())->first();
        $classIds = $student ? $student->classes()->pluck('classes.id')->toArray() : [];

        $announcements = Announcement::where(function ($query) use ($classIds) {
            $query->where('for', 'students')
                  ->orWhereNull('for');
        })
        ->where(function ($query) use ($classIds) {
            if (!empty($classIds)) {
                $query->whereIn('class_id', $classIds)
                      ->orWhereNull('class_id');
            } else {
                $query->whereNull('class_id');
            }
        })
        ->with(['teacher.user', 'class'])
        ->latest()
        ->get();

        return view('pages.students.dashboard', compact('student', 'announcements'));
    }
    public function showProfilePage()
    {
        //        dd(Student::where('user_id', auth()->user()->id)->get());
        $student_data = Student::select(['first_name', 'last_name'])->where('user_id', auth()->user()->id)->first();
        //        dd($student_data);
        return view('pages.students.profile', ['student' => $student_data]);
    }

    public function showSettingsPage(): View|Factory|Application
    {
        return view('pages.students.settings');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'string', 'max:255'],
            'old_password' => ['nullable', 'string', 'min:8'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();

        // Flag to track if email changed
        $emailChanged = false;

        // Check if email has changed
        if ($request->email != $user->email) {
            $request->validate(['email' => ['unique:users,email']]);
            $user->update(['email' => $request->email]);
            $emailChanged = true;
        }

        // Check if password change is requested
        if ($request->old_password && $request->password) {
            if (!password_verify($request->old_password, $user->password)) {
                return back()->with('error', 'Old password is incorrect');
            }

            $user->update(['password' => bcrypt($request->password)]);
        }

        // If email changed, log the user out
        if ($emailChanged) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/')->with('success', 'Login credentials updated. Please login again.');
        }

        return redirect('/')->with('success', 'Login credentials updated successfully. Please login again.');
    }
}
