<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Telko Digital Identity</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <div class="p-8">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-6">
                    <img src="https://i.ibb.co/5xKQcxPm/Telko-logo-Update-1-jpg-1.png" alt="Telko Logo"
                        class="h-24 object-contain" />
                </div>
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                    Admin Portal
                </h2>
                <p class="text-gray-500 mt-2">Log in to manage the system.</p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 text-red-500 p-4 rounded-xl mb-6 text-sm font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input name="email" type="email" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="admin@telko.local" value="{{ old('email') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input name="password" type="password" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="••••••••">
                </div>

                <button type="submit"
                    class="w-full py-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition-all shadow-lg hover:shadow-blue-500/30 mt-4">
                    Sign In to Dashboard
                </button>
            </form>
        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 text-center">
            <a href="/" class="text-blue-600 font-medium hover:underline text-sm">&larr; Back to API Service</a>
        </div>
    </div>

</body>

</html>