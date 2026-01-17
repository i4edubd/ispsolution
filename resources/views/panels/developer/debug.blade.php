@extends('panels.layouts.app')

@section('title', 'Debug Tools')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Debugging Tools</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Advanced tools for debugging and troubleshooting</p>
        </div>
    </div>

    <!-- System Information -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Server Info -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Server Information</h2>
                <div class="space-y-2">
                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">PHP Version</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ PHP_VERSION }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Laravel Version</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ app()->version() }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Environment</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ config('app.env') }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Debug Mode</span>
                        <span class="text-sm font-medium {{ config('app.debug') ? 'text-red-600' : 'text-green-600' }}">
                            {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Timezone</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ config('app.timezone') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cache Info -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Cache Management</h2>
                <div class="space-y-3">
                    <button class="w-full flex items-center justify-between px-4 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Clear Application Cache</span>
                        </div>
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <button class="w-full flex items-center justify-between px-4 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Clear Route Cache</span>
                        </div>
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <button class="w-full flex items-center justify-between px-4 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Clear Config Cache</span>
                        </div>
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <button class="w-full flex items-center justify-between px-4 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Clear View Cache</span>
                        </div>
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Database Inspector -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Database Inspector</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-4">
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Tables</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $dbInfo['tables'] ?? 0 }}</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Database Size</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $dbInfo['size'] ?? 'N/A' }}</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Records</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $dbInfo['records'] ?? 0 }}</div>
                </div>
            </div>
            <div class="flex space-x-2">
                <button class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Run Migrations
                </button>
                <button class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Seed Database
                </button>
                <button class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Optimize Database
                </button>
            </div>
        </div>
    </div>

    <!-- Queue Monitor -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Queue Monitor</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4 mb-4">
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Pending Jobs</div>
                    <div class="text-2xl font-bold text-yellow-600 mt-1">{{ $queueInfo['pending'] ?? 0 }}</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Processing</div>
                    <div class="text-2xl font-bold text-blue-600 mt-1">{{ $queueInfo['processing'] ?? 0 }}</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Completed</div>
                    <div class="text-2xl font-bold text-green-600 mt-1">{{ $queueInfo['completed'] ?? 0 }}</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Failed</div>
                    <div class="text-2xl font-bold text-red-600 mt-1">{{ $queueInfo['failed'] ?? 0 }}</div>
                </div>
            </div>
            <div class="flex space-x-2">
                <button class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Start Queue Worker
                </button>
                <button class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Retry Failed Jobs
                </button>
                <button class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Clear Failed Jobs
                </button>
            </div>
        </div>
    </div>

    <!-- Artisan Commands -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Artisan Commands</h2>
            <div class="space-y-2">
                <div class="flex items-center space-x-3">
                    <input type="text" placeholder="php artisan..." class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">
                    <button class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Execute
                    </button>
                </div>
                <div class="bg-gray-900 rounded-lg p-4 font-mono text-sm text-gray-300 overflow-x-auto" style="min-height: 200px;">
                    <div class="text-gray-500">Output will appear here...</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
