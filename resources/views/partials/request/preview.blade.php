@php
function renderJsonAsTable($json, $groupCounter = 1): void
{
    foreach ($json as $key => $value) {
        echo '<tr class="group-' . ($groupCounter % 3 + 1) . '">';
        echo '<td class="w-max p-2 font-medium text-gray-800 break-words whitespace-pre-wrap">' . htmlspecialchars(trim($key)) . '</td>';

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

<table class="table-auto w-full border border-gray-300">
    @php renderJsonAsTable($requestDetails, 1); @endphp
    <tr>
        @if (!empty($files))
            <td colspan="2" class="p-2 text-gray-700">
                @foreach ($files as $index => $file)
                    @php
                        $filePath    = $file['stored_path'];
                        $originalName= htmlspecialchars($file['original_name']);
                        $uniqueId    = 'file-preview-' . $index;
                        $mimeType    = $file['mime_type'];
                    @endphp
                    <div class="mb-4 p-4 {{ $index % 2 == 0 ? 'group-4' : 'group-5' }} border border-gray-300">
                        <strong>Field Name:</strong> {{ htmlspecialchars($file['field_name']) }}<br>
                        <strong>Original Name:</strong> {!! $originalName !!}<br>
                        <strong>Size:</strong> {{ htmlspecialchars($file['size']) }} bytes<br>
                        <strong>MIME Type:</strong> {{ htmlspecialchars($mimeType) }}<br>
                        <strong>Actions:</strong> 
                        <a target="_blank" href="/download/{{ $file['uuid'] ?? '' }}" class="text-blue-500 hover:underline mr-4">Download File</a>
                        <button type="button" onclick="togglePreview('{{ $uniqueId }}')" class="text-blue-500 hover:underline">Toggle Preview</button>
                        @if ($mimeType === 'application/json')
                            @php
                                $jsonContent = Storage::disk('public')->get($filePath);
                                $decodedJson = json_decode($jsonContent, true);
                            @endphp
                            <div id="{{ $uniqueId }}" class="mt-4 hidden resizable-container border border-gray-300 rounded-lg">
                                <!-- Container for JSON Formatter -->
                                <div id="jsonFormatter-{{ $uniqueId }}" class="p-4 bg-gray-100"></div>
                            </div>
                            <script>
                                (function() {
                                    var container = document.getElementById('jsonFormatter-{{ $uniqueId }}');
                                    if (container) {
                                        var jsonData = {!! json_encode($decodedJson) !!};
                                        // Create a formatter that expands 2 levels by default
                                        var formatter = new JSONFormatter(jsonData, 10);
                                        container.appendChild(formatter.render());
                                    }
                                })();
                            </script>
                        @elseif ($mimeType === 'text/html')
                            <div id="{{ $uniqueId }}" class="mt-4 hidden resizable-container border border-gray-300 rounded-lg" style="background-color: white;">
                                <iframe src="/storage/{{ $filePath }}" class="w-full h-full" style="background-color: white;"></iframe>
                            </div>
                        @else
                            <!-- Fallback preview (using iframe) for other file types -->
                            <div id="{{ $uniqueId }}" class="mt-4 hidden resizable-container border border-gray-300">
                                <iframe src="/storage/{{ $filePath }}" class="w-full h-full"></iframe>
                            </div>
                        @endif
                    </div>
                @endforeach
            </td>
        @endif

    </tr>
</table>