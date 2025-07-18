<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class JobController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:View Jobs', only: ['index']),
            new Middleware('permission:Edit Jobs', only: ['edit']),
            new Middleware('permission:Create Jobs', only: ['create']),
            new Middleware('permission:Job Details View', only: ['show']),
            new Middleware('permission:Delete Jobs', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = JobPost::query();

        // if log in user is employer
        if (Auth::user()->hasRole('Employer')) {
            $query->where('user_id', Auth::id());
        } else {
            // For candidates and other users, only show active jobs
            $query->where('is_active', true);
        }

        
        // Search functionality
        if ($request->has('search_btn') && $request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('job_title', 'like', "%{$request->search}%")
                ->orWhere('job_category', 'like', "%{$request->search}%")
                    ->orWhere('company_name', 'like', "%{$request->search}%")
                    ->orWhere('location', 'like', "%{$request->search}%");
            });
        }

        // Filter functionality
        if ($request->has('filter_btn')) {
            if ($request->filled('job_type')) {
                $query->where('job_type', $request->job_type);
            }

            if ($request->filled('location')) {
                $query->where('location', $request->location);
            }
            if ($request->filled('job_category')) {
                $query->where('job_category', $request->job_category);
            }
        }

        $jobs = $query->latest()->paginate(10);

        // Pass job types and unique locations to the view
        $jobTypes = JobPost::select('job_type')->distinct()->pluck('job_type');
        $locations = JobPost::select('location')->distinct()->pluck('location');
        $jobCategories = JobPost::select('job_category')->distinct()->pluck('job_category');

        return view('jobs.list', [
            'jobs' => $jobs,
            'jobTypes' => $jobTypes,
            'locations' => $locations,
            'jobCategories' => $jobCategories,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("jobs.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_title' => 'required|min:3|max:255',
            'job_category' => 'required|min:3|max:255|string',
            'job_description' => 'nullable|string',
            'company_name' => 'required|string|min:3|max:255',
            'salary_range' => 'required|string',
            'location' => 'required|string',
            'job_type' => 'required|in:full-time,part-time,internship,contract',
            'job_level' => 'nullable|in:entry,mid,senior',
            'experience_required' => 'nullable|integer|min:0',
            'education_level' => 'nullable|string|max:255',
            'application_deadline' => 'nullable|date|after_or_equal:today',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->passes()) {

            JobPost::create([
                'user_id' => Auth::id(), // Assuming the user is logged in
                'job_title' => $request->job_title,
                'job_category' => $request->job_category,
                'job_description' => $request->job_description,
                'salary_range' => $request->salary_range,
                'location' => $request->location,
                'company_name' => $request->company_name,
                'job_type' => $request->job_type,
                'job_level' => $request->job_level,
                'experience_required' => $request->experience_required,
                'education_level' => $request->education_level,
                'application_deadline' => $request->application_deadline,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            return redirect()->route('jobPost.index')->with('success', 'Job post created successfully.');

        } else {
            return redirect()->route('jobPost.create')->withInput()->withErrors($validator);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $job = JobPost::findOrFail($id);
        
        // If user is not the job owner and job is inactive, deny access
        if (!Auth::user()->hasRole('Employer') || $job->user_id !== Auth::id()) {
            if (!$job->is_active) {
                abort(404, 'Job not found or no longer available');
            }
        }

        // Increment views count only if the viewer is not the job owner
        if (Auth::id() !== $job->user_id) {
            $job->incrementViews(
                Auth::id(), 
                request()->ip(), 
                request()->userAgent()
            );
        }
        
        $alreadyApplied = Application::where('user_id', Auth::id())
            ->where('job_post_id', $job->id)
            ->exists();
        return view('jobs.show', ['job' => $job, 'alreadyApplied' => $alreadyApplied]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $job = JobPost::findOrFail($id);
        return view('jobs.edit', ['job' => $job]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'job_title' => 'required|min:3|max:255',
            'job_category' => 'required|min:3|max:255|string',
            'job_description' => 'nullable|string',
            'company_name' => 'required|string|min:3|max:255',
            'salary_range' => 'required|string',
            'location' => 'required|string',
            'job_type' => 'required|in:full-time,part-time,internship,contract',
            'job_level' => 'nullable|in:entry,mid,senior',
            'experience_required' => 'nullable|integer|min:0',
            'education_level' => 'nullable|string|max:255',
            'application_deadline' => 'nullable|date|after_or_equal:today',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('jobPost.edit', $id)
                ->withInput()
                ->withErrors($validator);
        } else {

            $job = JobPost::findOrFail($id);
            $job->update([
                'job_title' => $request->job_title,
                'job_category' => $request->job_category,
                'job_description' => $request->job_description,
                'company_name' => $request->company_name,
                'salary_range' => $request->salary_range,
                'location' => $request->location,
                'job_type' => $request->job_type,
                'job_level' => $request->job_level,
                'experience_required' => $request->experience_required,
                'education_level' => $request->education_level,
                'application_deadline' => $request->application_deadline,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);
            return redirect()->route('jobPost.index')->with('success', 'Job post updated successfully.');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $job = JobPost::findOrFail($id);
        $job->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Show view analytics for a job post (only for job owner)
     */
    public function viewAnalytics(string $id)
    {
        $job = JobPost::findOrFail($id);
        
        // Check if user is the job owner
        if (Auth::id() !== $job->user_id) {
            abort(403, 'Unauthorized access');
        }

        // Get view analytics
        $viewStats = [
            'total_views' => $job->views ?? 0,
            'unique_users' => $job->jobViews()->whereNotNull('user_id')->distinct('user_id')->count(),
            'anonymous_views' => $job->jobViews()->whereNull('user_id')->count(),
            'views_today' => $job->jobViews()->whereDate('created_at', today())->count(),
            'views_this_week' => $job->jobViews()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'views_this_month' => $job->jobViews()->whereMonth('created_at', now()->month)->count(),
        ];

        // Get daily views for the last 30 days
        $dailyViews = $job->jobViews()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as views')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('jobs.analytics', [
            'job' => $job,
            'viewStats' => $viewStats,
            'dailyViews' => $dailyViews
        ]);
    }
}
