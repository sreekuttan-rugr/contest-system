<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $contest->name }} - Leaderboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="max-w-3xl mx-auto mt-10 bg-white p-6 rounded-2xl shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-center text-blue-700">{{ $contest->name }} Leaderboard</h2>

    @if ($participants->count() > 0)
        <table class="w-full border-collapse border border-gray-200">
            <thead>
                <tr class="bg-blue-50">
                    <th class="border border-gray-200 px-4 py-2 text-left">Rank</th>
                    <th class="border border-gray-200 px-4 py-2 text-left">User</th>
                    <th class="border border-gray-200 px-4 py-2 text-center">Score</th>
                    <th class="border border-gray-200 px-4 py-2 text-center">Submitted At</th>
                </tr>
            </thead>
            <tbody>
                @php $rank = ($participants->currentPage() - 1) * $participants->perPage() + 1; @endphp
                @foreach ($participants as $entry)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                        <td class="border border-gray-200 px-4 py-2">{{ $rank++ }}</td>
                        <td class="border border-gray-200 px-4 py-2">{{ $entry->user->name }}</td>
                        <td class="border border-gray-200 px-4 py-2 text-center font-semibold">{{ $entry->score }}</td>
                        <td class="border border-gray-200 px-4 py-2 text-center text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($entry->submitted_at)->format('d M Y, h:i A') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="mt-4 flex justify-center">
            {{ $participants->links('pagination::tailwind') }}
        </div>

    @else
        <p class="text-center text-gray-500 mt-6">No participants yet.</p>
    @endif
</div>

</body>
</html>
