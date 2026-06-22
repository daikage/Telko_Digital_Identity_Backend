<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Telko Digital Identity</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">

    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-3">
                    <img src="https://i.ibb.co/5xKQcxPm/Telko-logo-Update-1-jpg-1.png" alt="Telko Logo"
                        class="h-12 object-contain" />
                    <span class="font-bold text-xl text-gray-900">Admin Control Panel</span>
                </div>
                <div class="flex items-center">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="text-gray-500 hover:text-red-600 font-medium px-3 py-2 rounded-md text-sm transition-colors">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900">Dashboard Overview</h1>
            <p class="mt-2 text-gray-600">Welcome back, {{ Auth::user()->name }}. Here's what's happening today.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <!-- Metric 1 -->
            <div class="bg-white overflow-hidden shadow rounded-2xl border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Registered Users</dt>
                                <dd class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metric 2 -->
            <div class="bg-white overflow-hidden shadow rounded-2xl border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Profiles Built</dt>
                                <dd class="text-3xl font-bold text-gray-900">{{ $totalProfiles }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metric 3 -->
            <div class="bg-white overflow-hidden shadow rounded-2xl border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Identity Cards</dt>
                                <dd class="text-3xl font-bold text-gray-900">{{ $activeCards }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Signups -->
            <div class="bg-white shadow rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-semibold text-gray-900">Recent Signups</h3>
                </div>
                <ul class="divide-y divide-gray-200">
                    @forelse($recentSignups as $user)
                        <li class="px-6 py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div
                                        class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                                </div>
                                <div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $user->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-4 text-gray-500 text-sm text-center">No signups yet.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Recent Profile Views -->
            <div class="bg-white shadow rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-semibold text-gray-900">Recent Profile Views</h3>
                </div>
                <ul class="divide-y divide-gray-200">
                    @forelse($recentViews as $view)
                        <li class="px-6 py-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        Viewer IP: {{ $view->viewer_ip ?? 'Unknown' }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Viewed Profile: <span
                                            class="font-medium text-blue-600">{{ $view->profile->user->username ?? 'Unknown' }}</span>
                                    </p>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $view->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-4 text-gray-500 text-sm text-center">No profile views yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- All Registered Users Database -->
        <div class="mt-8 bg-white shadow rounded-2xl border border-gray-100 overflow-hidden mb-10">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="text-xl leading-6 font-bold text-gray-900">All Registered Users Database</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($allUsers as $user)
                    <details class="group cursor-pointer">
                        <summary
                            class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors list-none">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    @if($user->profile && $user->profile->avatar_url)
                                        <img src="{{ $user->profile->avatar_url }}" alt="Avatar"
                                            class="h-10 w-10 rounded-full object-cover">
                                    @else
                                        <div
                                            class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                            {{ substr($user->name ?? $user->username, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $user->name }} <span
                                            class="text-gray-500 font-normal">(&#64;{{ $user->username }})</span></p>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-500">Joined {{ $user->created_at->format('M d, Y') }}</span>
                                <svg class="h-5 w-5 text-gray-400 group-open:rotate-180 transition-transform" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </summary>

                        <div
                            class="px-6 py-5 bg-gray-50 border-t border-gray-100 grid grid-cols-1 md:grid-cols-3 gap-6 cursor-default">
                            @if($user->profile)
                                <!-- Professional Details -->
                                <div>
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Professional</h4>
                                    <div class="space-y-2 text-sm">
                                        <p><span class="font-semibold text-gray-700">Headline:</span>
                                            {{ $user->profile->headline ?? 'N/A' }}</p>
                                        <p><span class="font-semibold text-gray-700">Bio:</span>
                                            {{ $user->profile->bio ?? 'N/A' }}</p>
                                        <p class="flex items-center">
                                            <span class="font-semibold text-gray-700 mr-2">Theme Color:</span>
                                            <span class="inline-block w-4 h-4 rounded-full border border-gray-300 mr-1"
                                                style="background-color: {{ $user->profile->theme_color ?? '#3B82F6' }}"></span>
                                            {{ $user->profile->theme_color ?? '#3B82F6' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Portfolio -->
                                <div>
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Portfolio</h4>
                                    <div class="space-y-2 text-sm">
                                        <div>
                                            <span class="font-semibold text-gray-700">Skills:</span>
                                            @if($user->profile->skills && is_array($user->profile->skills) && count($user->profile->skills) > 0)
                                                <div class="mt-1 flex flex-wrap gap-1">
                                                    @foreach($user->profile->skills as $skill)
                                                        <span
                                                            class="px-2 py-0.5 bg-gray-200 text-gray-700 text-xs rounded-full">{{ $skill }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-gray-500">None</span>
                                            @endif
                                        </div>
                                        <p><span class="font-semibold text-gray-700">Projects Count:</span>
                                            {{ is_array($user->profile->projects) ? count($user->profile->projects) : 0 }}</p>
                                    </div>
                                </div>

                                <!-- Contact & Social Links -->
                                <div>
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Contact & Links
                                    </h4>
                                    <div class="space-y-2 text-sm">
                                        <p><span class="font-semibold text-gray-700">Phone:</span>
                                            {{ $user->profile->contact_phone ?? 'N/A' }}</p>
                                        <p><span class="font-semibold text-gray-700">Contact Email:</span>
                                            {{ $user->profile->contact_email ?? 'N/A' }}</p>
                                        <div>
                                            <span class="font-semibold text-gray-700">Links:</span>
                                            <ul class="list-disc list-inside text-blue-600 mt-1">
                                                @if($user->profile->linkedin)
                                                    <li><a href="{{ $user->profile->linkedin }}" target="_blank"
                                                            class="hover:underline">LinkedIn</a></li>
                                                @endif
                                                @if($user->profile->social_links && is_array($user->profile->social_links))
                                                    @foreach($user->profile->social_links as $link)
                                                        <li><a href="{{ $link['url'] ?? '#' }}" target="_blank"
                                                                class="hover:underline">{{ $link['platform'] ?? 'Link' }}</a></li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-span-3 py-4 text-center text-gray-500 text-sm">
                                    This user has not generated a digital identity profile yet.
                                </div>
                            @endif
                        </div>
                    </details>
                @empty
                    <div class="px-6 py-10 text-center text-gray-500">
                        No users found in the database.
                    </div>
                @endforelse
            </div>

            @if($allUsers->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $allUsers->links() }}
                </div>
            @endif
        </div>

    </main>

    <style>
        /* Hide the default details marker */
        details>summary::-webkit-details-marker {
            display: none;
        }
    </style>

</body>

</html>