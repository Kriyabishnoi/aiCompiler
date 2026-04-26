<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

    <h1 class="text-3xl font-bold mb-6 text-center text-indigo-600">Projects Dashboard</h1>

    <!-- Project Creation Form -->
    <div class="max-w-md mx-auto mb-8 bg-white p-5 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4 text-indigo-700">Create New Project</h2>
        <form action="{{ route('projects.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium mb-2">Project Name</label>
                <input type="text" id="name" name="name" placeholder="Enter project name"
                       class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
            </div>
            <button type="submit" 
                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                Create Project
            </button>
        </form>
    </div>

    <!-- Projects List -->
    @if($projects->isEmpty())
        <p class="text-center text-gray-500">No projects found.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($projects as $project)
                <div class="bg-white shadow-md rounded-lg p-5">
                    <h2 class="text-xl font-semibold text-indigo-700 mb-3">{{ $project->name ?? 'Unnamed Project' }}</h2>

                    @if($project->files->isEmpty())
                        <p class="text-gray-500">No files uploaded.</p>
                    @else
                        <ul class="space-y-2">
                            @foreach($project->files as $file)
                                <li class="border p-3 rounded-md hover:bg-indigo-50 transition">
                                    <strong>File:</strong> {{ $file->name ?? 'Unnamed File' }}
                                    @if($file->executions->isNotEmpty())
                                        <ul class="mt-2 ml-4 list-disc list-inside text-gray-700">
                                            @foreach($file->executions as $execution)
                                                <li>
                                                    {{ $execution->status ?? 'Pending' }} - {{ $execution->created_at->format('d M Y H:i') }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-gray-400 mt-1">No executions yet.</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
<div class="bg-white shadow-lg rounded-lg p-6 mt-10">
    <h2 class="text-2xl font-bold mb-4 text-indigo-700">Projects Table</h2>

    @if(session('success'))
        <p class="mb-4 text-green-600">{{ session('success') }}</p>
    @endif

    <table class="w-full border border-gray-200">
        <thead class="bg-indigo-600 text-white">
            <tr>
                <th class="p-2">Project Name</th>
                <th class="p-2">Language</th>
                <th class="p-2">Created</th>
                <th class="p-2">Updated</th>
                <th class="p-2">Action</th>
            </tr>
        </thead>

        <tbody>
            @forelse($projects as $project)
                <tr class="text-center border {{ $project->deleted_at ? 'bg-red-50' : '' }}">
                    <td class="p-2 font-medium">{{ $project->name }}</td>

                    <td class="p-2">{{ $project->language ?? 'N/A' }}</td>

                    <td class="p-2">{{ $project->created_at->format('d M Y') }}</td>

                    <td class="p-2">{{ $project->updated_at->format('d M Y') }}</td>

                    <td class="p-2">
                        @if(!$project->deleted_at)
                            <!-- Delete -->
                            <form method="POST" action="{{ route('projects.delete', $project->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                                    Delete
                                </button>
                            </form>
                        @else
                            <!-- Undo -->
                            <form method="POST" action="{{ route('projects.restore', $project->id) }}">
                                @csrf
                                <button class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                                    Undo
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">
                        No projects found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

</body>
</html>
