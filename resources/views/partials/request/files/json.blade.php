@php
// Read and decode JSON from storage
$jsonContent = Illuminate\Support\Facades\Storage::disk('public')->get($filePath);
$decodedJson = json_decode($jsonContent, true);
@endphp
<div id="{{ $uniqueId }}" class="mt-4 hidden resizable-container border border-gray-300 rounded-lg">
    <!-- Toolbar with controls for JSON preview -->
    <div id="jsonToolbar-{{ $uniqueId }}" class="p-2 bg-gray-200 border-b border-gray-300 flex space-x-2">
        <button onclick="expandAll('jsonFormatter-{{ $uniqueId }}')" class="px-2 py-1 bg-green-500 text-white rounded">Expand All</button>
        <button onclick="collapseAll('jsonFormatter-{{ $uniqueId }}')" class="px-2 py-1 bg-red-500 text-white rounded">Collapse All</button>
    </div>
    <!-- Container for JSON Formatter output -->
    <div id="jsonFormatter-{{ $uniqueId }}" class="p-4 bg-gray-100"></div>
</div>
<script>
    (function() {
        // Render the JSON tree using json-formatter-js
        var container = document.getElementById('jsonFormatter-{{ $uniqueId }}');
        if (container) {
            var jsonData = {!! json_encode($decodedJson) !!};
            // Create a formatter that expands 2 levels by default.
            var formatter = new JSONFormatter(jsonData, 2);
            container.appendChild(formatter.render());
        }
    })();

    // Expand All: finds all toggle buttons that are collapsed and simulates a click.
    function expandAll(containerId) {
        var container = document.getElementById(containerId);
        var toggles = container.querySelectorAll('.json-formatter__toggle');
        toggles.forEach(function(toggle) {
            // Adjust this check if your library uses a different indicator
            if (toggle.classList.contains('collapsed')) {
                toggle.click();
            }
        });
    }

    // Collapse All: finds all toggle buttons that are expanded and simulates a click.
    function collapseAll(containerId) {
        var container = document.getElementById(containerId);
        var toggles = container.querySelectorAll('.json-formatter__toggle');
        toggles.forEach(function(toggle) {
            // Adjust this check if your library uses a different indicator
            if (!toggle.classList.contains('collapsed')) {
                toggle.click();
            }
        });
    }
</script>