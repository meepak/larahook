@php
    $hasJsonFile = false;
    if (!empty($files)) {
        foreach ($files as $file) {
            if ($file['mime_type'] === 'application/json') {
                $hasJsonFile = true;
                break;
            }
        }
    }
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to LaraHook</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        clifford: '#da373d',
                    }
                }
            }
        }
    </script>

    @if ($hasJsonFile)
        <script src="https://cdn.jsdelivr.net/npm/json-formatter-js@2.5.18/dist/json-formatter.umd.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/json-formatter-js@2.5.18/dist/json-formatter.min.css" rel="stylesheet">
    @endif
    <style>
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .group-1 {
            background-color: #e0f7fa;
        }
        .group-2 {
            background-color: #ffecb3;
        }
        .group-3 {
            background-color: #dcedc8;
        }
        .group-4 {
            background-color: #bdd3f8;
        }
        .group-5 {
            background-color: #ffc7e7;
        }
        .resizable-container {
            resize: vertical;    
            overflow: auto;  
            width: 100%;
            height: 16rem;
            min-height: 16rem;
        }
    </style>
</head>
<body class="bg-gray-100">
<!-- Header -->
<header class="bg-white shadow-md py-4">
    <div class="mx-auto flex justify-between items-center px-4">
        <!-- Logo (Left Aligned) -->
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                LH
            </div>
            <h1 class="text-xl font-bold text-gray-800">LARAHOOK</h1>
        </div>
        <!-- User Info and Logout (Right Aligned) -->
        <div class="flex items-center space-x-4">
            <span class="text-gray-800">Welcome {{ Auth::user()->username }}!</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-red-500 hover:text-red-700 font-semibold">Logout</button>
            </form>
        </div>
    </div>
</header>

<!-- Main Content -->
<div class="min-h-screen flex flex-row">
    <!-- Sidebar -->
    <div class="w-1/4 bg-white shadow-lg">
        <div class="p-4 border-b flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800">API Requests</h2>
            <form method="POST" action="{{ route('deleteRequests') }}" id="delete-requests-form">
                @csrf
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                    Delete Selected
                </button>
        </div>
        <div class="p-4 overflow-y-auto h-full">
                @csrf
                <div>
                    <input type="checkbox" id="select-all" class="mr-2" onclick="toggleAll(this)">
                    <label for="select-all" class="text-md font-bold text-gray-600">Select All  (@php echo count($apiRequests); @endphp)</label>
                    <hr/>
                    <br/>
                </div>
                <ul>
                    @forelse ($apiRequests as $request)
                        <li class="mb-2">
                            <input type="checkbox" name="ids[]" value="{{ $request->id }}" class="mr-2">
                            <a href="{{ route('previewRequest', ['id' => $request->id]) }}" class="text-blue-500 hover:underline <?= (isset($requestId) && $request->id == $requestId) ? 'bg-blue-500 text-white font-bold' : '' ?>">
                                {{ $request->created_at->format('Y-m-d H:i:s') }}
                            </a>
                        </li>
                    @empty
                        <p>No API requests available.</p>
                    @endforelse
                </ul>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6 overflow-x-scroll"> <!-- Prevent horizontal scrolling -->
        @if (session('message'))
            <div class="container mx-auto my-4">
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                    {{ session('message') }}
                </div>
            </div>
        @endif
        <h2 class="text-xl font-bold text-gray-800 mb-4">API Request Preview <?= isset($createdAt) ? '[ ' . $createdAt . ' ]' : '' ?></h2>
        @if (isset($requestDetails))
            @include('partials.request.preview')
        @else
            <p>Select a request from the left sidebar to preview details.</p>
        @endif
    </div>
</div>

<script>
    function toggleAll(source) {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="ids[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = source.checked);
    }

    function togglePreview(id) {
        const preview = document.getElementById(id);
        if (preview.classList.contains('hidden')) {
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    }
</script>
</body>
</html>
