<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Conflict - Hotspot Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-lg mx-auto">
            <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-8 text-white text-center">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold mb-2">Device Already Logged In</h1>
                    <p class="text-yellow-100">You can only be logged in on one device at a time</p>
                </div>

                <div class="p-8">
                    <!-- Info Message -->
                    <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-800">
                                    <strong>Your account is currently logged in on another device.</strong>
                                </p>
                                <p class="text-xs text-yellow-700 mt-1">
                                    For security reasons, only one device can be logged in at a time.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Current Session Info -->
                    <div class="mb-6 bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Current Session Details:</h3>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Username:</span>
                                <span class="font-medium text-gray-800">{{ $user->username }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Mobile:</span>
                                <span class="font-medium text-gray-800">{{ $user->phone_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Current Device:</span>
                                <span class="font-mono text-xs text-gray-800">{{ $current_mac }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Last Login:</span>
                                <span class="font-medium text-gray-800">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="space-y-3">
                        <!-- Force Login on This Device -->
                        <form action="{{ route('hotspot.login.force-login') }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-150 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            >
                                <div class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    Login Here & Logout Other Device
                                </div>
                            </button>
                        </form>

                        <!-- Cancel -->
                        <a
                            href="{{ route('hotspot.login') }}"
                            class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-4 rounded-lg text-center transition duration-150 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                        >
                            Cancel & Go Back
                        </a>
                    </div>

                    <!-- Warning -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-xs text-blue-800">
                                <strong>Note:</strong> If you continue, your previous session will be logged out immediately.
                                Any active connection will be terminated.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 mb-2">Having trouble logging in?</p>
                <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500">Contact Support</a>
            </div>
        </div>
    </div>
</body>
</html>
