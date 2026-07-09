<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Door Locks & Access - Telko Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">

    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-3">
                    <img src="https://i.ibb.co/5xKQcxPm/Telko-logo-Update-1-jpg-1.png" alt="Telko Logo"
                        class="h-12 object-contain" />
                    <span class="font-bold text-xl text-gray-900">Door Locks & Access</span>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-blue-600 font-medium text-sm transition-colors">&larr; Dashboard</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-red-600 font-medium px-3 py-2 rounded-md text-sm transition-colors">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm font-medium">
                ✓ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm font-medium">
                ✕ {{ session('error') }}
            </div>
        @endif
        @if(session('secret_key'))
            <div class="mb-6 p-4 bg-amber-50 border border-amber-300 rounded-2xl">
                <p class="text-sm font-bold text-amber-800 mb-2">⚠️ Secret Key — Save this now! It will not be shown again.</p>
                <code class="block bg-white p-3 rounded-lg font-mono text-sm break-all text-gray-900 border border-amber-200">{{ session('secret_key') }}</code>
                <p class="text-xs text-amber-600 mt-2">This key is used by the physical lock hardware to authenticate with the API via <code>POST /api/locks/verify</code>.</p>
            </div>
        @endif

        {{-- Metrics --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-white overflow-hidden shadow rounded-2xl border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-cyan-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Door Locks</dt>
                            <dd class="text-3xl font-bold text-gray-900">{{ $totalLocks }}</dd>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-2xl border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Access Cards</dt>
                            <dd class="text-3xl font-bold text-gray-900">{{ $totalCards }}</dd>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-2xl border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Access Granted</dt>
                            <dd class="text-3xl font-bold text-gray-900">{{ $totalGranted }}</dd>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-2xl border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Access Denied</dt>
                            <dd class="text-3xl font-bold text-gray-900">{{ $totalDenied }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">

            {{-- ===== CREATE / EDIT DOOR LOCK ===== --}}
            <div class="bg-white shadow rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900">
                        {{ isset($editLock) ? 'Edit Lock: ' . $editLock->name : 'Add New Door Lock' }}
                    </h3>
                </div>
                <form action="{{ isset($editLock) ? route('admin.doorlocks.update', $editLock->id) : route('admin.doorlocks.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    @if(isset($editLock))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lock Name *</label>
                            <input name="name" type="text" required placeholder="Main Entrance"
                                   value="{{ old('name', $editLock->name ?? '') }}"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <input name="location" type="text" placeholder="Building A, Floor 2"
                                   value="{{ old('location', $editLock->location ?? '') }}"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lock Type *</label>
                        <select name="lock_type" required class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                            <option value="both" {{ old('lock_type', $editLock->lock_type ?? '') == 'both' ? 'selected' : '' }}>NFC + BLE (Both)</option>
                            <option value="nfc" {{ old('lock_type', $editLock->lock_type ?? '') == 'nfc' ? 'selected' : '' }}>NFC Only</option>
                            <option value="ble" {{ old('lock_type', $editLock->lock_type ?? '') == 'ble' ? 'selected' : '' }}>BLE Only</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NFC AID</label>
                            <input name="nfc_aid" type="text" placeholder="F054454C4B4F01"
                                   value="{{ old('nfc_aid', $editLock->nfc_aid ?? '') }}"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500 transition-all text-sm font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">BLE Service UUID</label>
                            <input name="ble_service_uuid" type="text" placeholder="0000180a-0000-..."
                                   value="{{ old('ble_service_uuid', $editLock->ble_service_uuid ?? '') }}"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500 transition-all text-sm font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">BLE Characteristic UUID</label>
                        <input name="ble_characteristic_uuid" type="text" placeholder="00002a29-0000-..."
                               value="{{ old('ble_characteristic_uuid', $editLock->ble_characteristic_uuid ?? '') }}"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500 transition-all text-sm font-mono">
                    </div>

                    @if(isset($editLock))
                        <div>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" {{ $editLock->is_active ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="font-medium text-gray-700">Lock is Active</span>
                            </label>
                        </div>
                    @endif

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition-all shadow-lg hover:shadow-blue-500/30 text-sm">
                            {{ isset($editLock) ? 'Update Lock' : 'Create Lock' }}
                        </button>
                        @if(isset($editLock))
                            <a href="{{ route('admin.doorlocks') }}" class="px-6 py-3 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium transition-all text-sm">Cancel</a>
                        @endif
                    </div>

                    @if($errors->any())
                        <div class="mt-2 text-red-500 text-sm">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                </form>
            </div>

            {{-- ===== ASSIGN ACCESS CARD ===== --}}
            <div class="bg-white shadow rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900">Assign Access Card to User</h3>
                </div>
                <form action="{{ route('admin.accesscards.assign') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">User *</label>
                        <select name="user_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                            <option value="">Select a user...</option>
                            @foreach($allUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name ?? $user->username }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Door Lock *</label>
                        <select name="door_lock_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                            <option value="">Select a lock...</option>
                            @foreach($locks as $lock)
                                <option value="{{ $lock->id }}">{{ $lock->name }} ({{ $lock->location ?? 'No location' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Card Label</label>
                            <input name="card_name" type="text" placeholder="Optional custom name" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expires At</label>
                            <input name="expires_at" type="datetime-local" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                        </div>
                    </div>
                    <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-all shadow-lg hover:shadow-emerald-500/30 text-sm">
                        Assign Access Card
                    </button>
                </form>
            </div>
        </div>

        {{-- ===== ALL DOOR LOCKS ===== --}}
        <div class="bg-white shadow rounded-2xl border border-gray-100 overflow-hidden mb-10">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h3 class="text-xl font-bold text-gray-900">All Door Locks</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-6 py-4 text-gray-500 font-medium">Lock</th>
                            <th class="text-left px-6 py-4 text-gray-500 font-medium">Type</th>
                            <th class="text-left px-6 py-4 text-gray-500 font-medium">Cards Assigned</th>
                            <th class="text-left px-6 py-4 text-gray-500 font-medium">Status</th>
                            <th class="text-right px-6 py-4 text-gray-500 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($locks as $lock)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900">{{ $lock->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $lock->location ?? '—' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                        {{ $lock->lock_type === 'both' ? 'bg-cyan-100 text-cyan-700' : ($lock->lock_type === 'nfc' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700') }}">
                                        {{ $lock->lock_type === 'both' ? 'NFC + BLE' : strtoupper($lock->lock_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $lock->access_cards_count }}</td>
                                <td class="px-6 py-4">
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full {{ $lock->is_active ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                                        <span class="{{ $lock->is_active ? 'text-emerald-600' : 'text-red-600' }} text-xs font-medium">
                                            {{ $lock->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.doorlocks') }}?edit={{ $lock->id }}" class="text-blue-600 hover:underline text-xs font-medium">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-400">No door locks configured yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== ALL ACCESS CARDS ===== --}}
        <div class="bg-white shadow rounded-2xl border border-gray-100 overflow-hidden mb-10">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h3 class="text-xl font-bold text-gray-900">All Access Cards</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-6 py-4 text-gray-500 font-medium">User</th>
                            <th class="text-left px-6 py-4 text-gray-500 font-medium">Door Lock</th>
                            <th class="text-left px-6 py-4 text-gray-500 font-medium">Card Name</th>
                            <th class="text-left px-6 py-4 text-gray-500 font-medium">Status</th>
                            <th class="text-left px-6 py-4 text-gray-500 font-medium">Expires</th>
                            <th class="text-right px-6 py-4 text-gray-500 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($cards as $card)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900">{{ $card->user->name ?? $card->user->username ?? '—' }}</p>
                                    <p class="text-xs text-gray-500">{{ $card->user->email ?? '' }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-900">{{ $card->doorLock->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $card->card_name ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full {{ $card->is_active ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                                        <span class="{{ $card->is_active ? 'text-emerald-600' : 'text-red-600' }} text-xs font-medium">
                                            {{ $card->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs">{{ $card->expires_at ? $card->expires_at->format('M d, Y H:i') : 'Never' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('admin.accesscards.revoke', $card->id) }}" method="POST" class="inline" onsubmit="return confirm('Revoke this access card? The user will lose door access.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Revoke</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-400">No access cards assigned yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== ACCESS LOGS ===== --}}
        <div class="bg-white shadow rounded-2xl border border-gray-100 overflow-hidden mb-10">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h3 class="text-xl font-bold text-gray-900">Access Activity Log</h3>
                <p class="text-sm text-gray-500 mt-1">See who used their access card and when.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-6 py-4 text-gray-500 font-medium">User</th>
                            <th class="text-left px-6 py-4 text-gray-500 font-medium">Door</th>
                            <th class="text-left px-6 py-4 text-gray-500 font-medium">Method</th>
                            <th class="text-left px-6 py-4 text-gray-500 font-medium">Status</th>
                            <th class="text-left px-6 py-4 text-gray-500 font-medium">IP Address</th>
                            <th class="text-left px-6 py-4 text-gray-500 font-medium">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900">
                                        {{ $log->accessCard && $log->accessCard->user ? ($log->accessCard->user->name ?? $log->accessCard->user->username) : 'Unknown' }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-gray-900">
                                    {{ $log->doorLock->name ?? '—' }}
                                    <span class="text-xs text-gray-500 block">{{ $log->doorLock->location ?? '' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        {{ strtoupper($log->method) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                        {{ $log->status === 'granted' ? 'bg-green-100 text-green-700' : ($log->status === 'expired' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-mono">{{ $log->ip_address ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-500 text-xs">
                                    {{ $log->created_at->format('M d, Y') }}
                                    <span class="block text-gray-400">{{ $log->created_at->format('H:i:s') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-400">No access logs recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

    </main>

</body>

</html>
