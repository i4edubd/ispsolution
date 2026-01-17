@extends('panels.layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">My Profile</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">View and manage your account information</p>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="text-center">
                        <div class="mx-auto h-32 w-32 rounded-full bg-indigo-600 flex items-center justify-center mb-4">
                            <span class="text-white text-5xl font-medium">{{ substr($customer->name ?? 'U', 0, 1) }}</span>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $customer->name ?? 'N/A' }}</h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $customer->email ?? 'N/A' }}</p>
                        <div class="mt-4">
                            @if($customer->status === 'active')
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active Account
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactive Account
                                </span>
                            @endif
                        </div>
                        <div class="mt-6">
                            <a href="#" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Stats</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Member Since</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $customer->created_at->format('M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Sessions</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $stats['total_sessions'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Data Used</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $stats['data_used'] ?? '0 GB' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Details -->
        <div class="lg:col-span-2">
            <div class="space-y-6">
                <!-- Personal Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Personal Information</h3>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ $customer->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ $customer->email ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Number</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ $customer->phone ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ $customer->address ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Package Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Current Package</h3>
                            <a href="#" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                Upgrade Package
                            </a>
                        </div>
                        <div class="border-2 border-indigo-200 dark:border-indigo-800 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $customer->package->name ?? 'N/A' }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $customer->package->description ?? '' }}</p>
                                </div>
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    {{ strtoupper($customer->package->service_type ?? 'N/A') }}
                                </span>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-4">
                                <div>
                                    <div class="flex items-center text-sm">
                                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        <span class="text-gray-700 dark:text-gray-300">
                                            <span class="font-semibold">Speed:</span> {{ $customer->package->speed ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center text-sm">
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-gray-700 dark:text-gray-300">
                                            <span class="font-semibold">Price:</span> {{ $customer->package->price ? number_format($customer->package->price, 2) . ' BDT/month' : 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Details -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Account Details</h3>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ $customer->username ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Account Status</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($customer->status ?? 'N/A') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Registration Date</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ $customer->created_at->format('F d, Y') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Login</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ $customer->last_login ? $customer->last_login->diffForHumans() : 'Never' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Connection Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Connection Information</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Connection Status</span>
                                @if($customer->is_online ?? false)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Online
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Offline
                                    </span>
                                @endif
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-sm text-gray-600 dark:text-gray-400">IP Address</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $customer->ip_address ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">MAC Address</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $customer->mac_address ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
