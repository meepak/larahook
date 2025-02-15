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
            resize: both;    
            overflow: auto;  
            width: 100%;
            height: 16rem;  
        }
    </style>
</head>
<body class="bg-gray-100">
<!-- Header -->
<header class="bg-white shadow-md py-4">
    <div class="container mx-auto flex justify-between items-center px-4">
        <!-- Logo -->
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                LH
            </div>
            <h1 class="text-xl font-bold text-gray-800">LARAHOOK</h1>
        </div>
        <!-- User Info and Logout -->
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
                            <a href="{{ route('previewRequest', ['id' => $request->id]) }}" class="text-blue-500 hover:underline">
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
        <h2 class="text-xl font-bold text-gray-800 mb-4">API Request Preview <?= '- ' . $createdAt ?? '' ?></h2>
        @if (isset($requestDetails))
            <table class="table-auto w-full border border-gray-300">
                @php
                    function renderJsonAsTable($json, $groupCounter = 1): void
                    {
                        foreach ($json as $key => $value) {
                            echo '<tr class="group-' . ($groupCounter % 3 + 1) . '">';
                            echo '<td class="p-2 font-medium text-gray-800 break-words whitespace-pre-wrap">' . htmlspecialchars(trim($key)) . '</td>';

                            // Check the type of value
                            if (is_array($value)) {
                                // If value is an array, format it properly
                                echo '<td class="p-2 text-gray-700 break-words whitespace-pre-wrap">';
                              if (array_keys($value) === range(0, count($value) - 1)) {
                                // If the array is sequential (e.g., a list), process each value
                                $wrappedValues = array_map(function ($item) {
                                    $trimmedValue = trim($item); // Trim the value
                                    $safeValue = htmlspecialchars($trimmedValue); // Sanitize the value
                                    return wordwrap($safeValue, 50, ' ', true); // Break the value every 50 characters
                                }, $value);

                                echo implode(', ', $wrappedValues); // Join the wrapped values with commas
                            } else {
                                    // If the array is associative, render as a nested table
                                    echo '<table class="table-auto w-full border border-gray-300 mt-2">';
                                    renderJsonAsTable($value, $groupCounter + 1);
                                    echo '</table>';
                                }
                                echo '</td>';
                            }
                            elseif (is_object($value)) {
                                // If value is an object, recursively render it as a nested table
                                echo '<td class="p-2 text-gray-700 break-words whitespace-pre-wrap">';
                                echo '<table class="table-auto w-full border border-gray-300 mt-2">';
                                renderJsonAsTable((array)$value, $groupCounter + 1);
                                echo '</table>';
                                echo '</td>';
                            } else {
                                // Otherwise, render the value as-is
                                    echo '<td class="p-2 text-gray-700 break-words whitespace-pre-wrap">' . htmlspecialchars(trim($value)) . '</td>';
                            }

                            echo '</tr>';
                        }
                    }
                @endphp
                @php renderJsonAsTable($requestDetails, 1); @endphp
                <tr>
                    @php
                        if (!empty($files)) {
                            echo '<td colspan="2" class="p-2 text-gray-700">';
                            foreach ($files as $index => $file) {
                                $filePath = htmlspecialchars($file['stored_path']);
                                $originalName = htmlspecialchars($file['original_name']);
                                $uniqueId = 'file-preview-' . $index; // Unique ID for toggling the iframe

                                echo '<div class="mb-4 p-4 ' . ($index % 2 == 0 ? 'group-4' : 'group-5') . ' border border-gray-300 rounded-lg">';
                                echo '<strong>Field Name:</strong> ' . htmlspecialchars($file['field_name']) . '<br>';
                                echo '<strong>Original Name:</strong> ' . $originalName . '<br>';
                                echo '<strong>Size:</strong> ' . htmlspecialchars($file['size']) . ' bytes<br>';
                                echo '<strong>MIME Type:</strong> ' . htmlspecialchars($file['mime_type']) . '<br>';
                                echo '<strong>Actions:</strong> ';
                                echo '<a target="_blank" href="/download/' . ($file['uuid'] ?? '') . '" class="text-blue-500 hover:underline mr-4">Download File</a>';
                                echo '<button type="button" onclick="togglePreview(\'' . $uniqueId . '\')" class="text-blue-500 hover:underline">Toggle Preview</button>';

                                // Collapsible iframe for file preview
                                echo '<div id="' . $uniqueId . '" class="mt-4 hidden resizable-container border border-gray-300 rounded-lg">';
                                echo '<iframe src="/storage/' . $filePath . '" class="w-full h-full rounded-lg"></iframe>';
                                echo '</div>';

                                echo '</div>';
                            }
                            echo '</td>';
                        }
                    @endphp

                </tr>
            </table>
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
