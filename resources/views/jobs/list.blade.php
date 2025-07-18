<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">
                {{ __('Job Posts') }}
            </h2>
            @can('Create Jobs')
                <a href="{{ route('jobPost.create') }}"
                    class="px-6 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700 transition">
                    Create
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-message />


            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

                <!-- Filter Section -->
                <form method="GET" action="{{ route('jobPost.index') }}"
                    class="bg-white p-4 rounded-xl shadow-md border border-gray-200 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                    <!-- Job Type -->
                    <div>
                        <label for="job_type" class="block text-sm font-medium text-gray-700 mb-1">Job Type</label>
                        <select name="job_type" id="job_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-slate-600 focus:border-slate-600 text-sm">
                            <option value="">All Types</option>
                            @foreach ($jobTypes as $type)
                                <option value="{{ $type }}" {{ request('job_type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <select name="location" id="location"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-slate-600 focus:border-slate-600 text-sm">
                            <option value="">All Locations</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                    {{ $location }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Job Category -->
                    <div>
                        <label for="job_category" class="block text-sm font-medium text-gray-700 mb-1">Job
                            Category</label>
                        <select name="job_category" id="job_category"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-slate-600 focus:border-slate-600 text-sm">
                            <option value="">All Categories</option>
                            @foreach ($jobCategories as $category)
                                <option value="{{ $category }}" {{ request('job_category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Button -->
                    <div class="flex items-end">
                        <button type="submit" name="filter_btn"
                            class="w-full px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded-lg text-sm font-medium shadow-md transition">
                            Filter
                        </button>
                    </div>
                </form>

                <!-- Search Section -->
                <form method="GET" action="{{ route('jobPost.index') }}"
                    class="bg-white p-4 rounded-xl shadow-md border border-gray-200 flex flex-col sm:flex-row gap-4 sm:items-end justify-between items-center">

                    <div class="w-full">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Search jobs..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-slate-600 focus:border-slate-600 text-sm">
                    </div>

                    <div class="w-full sm:w-auto">
                        <button type="submit" name="search_btn"
                            class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium shadow-md transition">
                            Search
                        </button>
                    </div>
                </form>

            </div>



            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mx-4 md:mx-0">
                <div class="overflow-x-auto bg-white rounded-xl shadow-md">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-100 text-gray-700 text-sm uppercase font-semibold">
                            <tr>
                                <th class="px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">Title</th>
                                <th class="px-4 py-3 text-left">Job Category</th>
                                <th class="px-4 py-3 text-left">Location</th>
                                @can('Viewers Seen')

                                    <th class="px-4 py-3 text-left">Views</th>
                                @endcan
                                <th class="px-4 py-3 text-left">Type</th>
                                <th class="px-4 py-3 text-left">Deadline</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Created At</th>
                                <th class="px-4 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-700 divide-y divide-gray-200">
                            @forelse($jobs as $job)
                                <tr id="job-row-{{ $job->id }}">
                                    <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3">{{ $job->job_title }}</td>
                                    <td class="px-4 py-3">{{ $job->job_category }}</td>
                                    <td class="px-4 py-3">{{ $job->location }}</td>
                                    @can('Viewers Seen')
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-blue-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd"
                                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-blue-600 font-medium">{{ $job->views ?? 0 }}</span>
                                        </div>
                                    </td>
                                    @endcan
                                    <td class="px-4 py-3 capitalize">{{ $job->job_type }}</td>
                                    <td class="px-4 py-3">{{ $job->application_deadline ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="px-2 py-1 rounded text-xs {{ $job->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $job->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $job->created_at->format('d-m-Y') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <!-- View -->
                                            @can('Job Details View')
                                                <a href="{{ route('jobPost.show', $job->id) }}"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
                                                    View
                                                </a>
                                            @endcan

                                            <!-- View Applicants (Only for job owner/employer) -->
                                            @if(auth()->user()->hasRole('Employer') && $job->user_id == auth()->id())
                                                <a href="{{ route('jobs.applicants', $job->id) }}"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-purple-600 rounded hover:bg-purple-700">
                                                    Applicants
                                                </a>
                                                <a href="{{ route('jobPost.analytics', $job->id) }}"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-orange-600 rounded hover:bg-orange-700">
                                                    Analytics
                                                </a>
                                            @endif

                                            <!-- Edit -->
                                            @can('Edit Jobs')
                                                <a href="{{ route('jobPost.edit', $job->id) }}"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-indigo-600 rounded hover:bg-indigo-700">
                                                    Edit
                                                </a>
                                            @endcan

                                            <!-- Delete -->
                                            @can('Delete Jobs')
                                                <button onclick="deleteJob('{{ $job->id }}')"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-red-600 rounded hover:bg-red-700">
                                                    Delete
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-sm text-gray-500">No job posts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="py-4">
                {{ $jobs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function deleteJob(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This job post will be deleted permanently!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/jobs/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Delete failed');
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Job post deleted successfully',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        document.getElementById(`job-row-${id}`).remove();
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Something went wrong!', 'error');
                        console.log(error)
                    });
            }
        });
    }
</script>