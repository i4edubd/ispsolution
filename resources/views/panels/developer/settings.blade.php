@extends('panels.layouts.app')

@section('title', 'Developer Settings')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Developer Settings</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Configure API access and development tools</p>
        </div>
    </div>

    <!-- API Configuration -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">API Configuration</h2>
            <form class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">API Status</label>
                    <div class="flex items-center">
                        <input type="checkbox" checked class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">Enable API Access</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rate Limiting</label>
                    <select class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option>60 requests per minute</option>
                        <option>120 requests per minute</option>
                        <option>300 requests per minute</option>
                        <option>Unlimited</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Allowed IP Addresses</label>
                    <textarea rows="3" placeholder="Enter IP addresses, one per line" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave blank to allow all IPs</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">CORS Settings</label>
                    <input type="text" placeholder="https://yourdomain.com" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Allowed origins for CORS requests</p>
                </div>

                <div class="pt-4">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Webhooks -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Webhooks</h2>
                <button class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Add Webhook
                </button>
            </div>
            <div class="space-y-3">
                @forelse($webhooks ?? [] as $webhook)
                    <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $webhook->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $webhook->url }}</p>
                                <div class="mt-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Events: </span>
                                    @foreach($webhook->events as $event)
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-xs rounded mr-1">{{ $event }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($webhook->active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                @endif
                                <button class="text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Edit</button>
                                <button class="text-sm text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <p class="mt-2">No webhooks configured</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- OAuth Applications -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">OAuth Applications</h2>
                <button class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Create Application
                </button>
            </div>
            <div class="space-y-3">
                @forelse($oauthApps ?? [] as $app)
                    <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $app->name }}</h3>
                                <div class="mt-2 space-y-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        <span class="font-medium">Client ID:</span> {{ $app->client_id }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        <span class="font-medium">Redirect URI:</span> {{ $app->redirect_uri }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button class="text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Edit</button>
                                <button class="text-sm text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <p class="mt-2">No OAuth applications created</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Sandbox Environment -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Sandbox Environment</h2>
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border-2 border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Sandbox Mode Active</h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <p>You are currently using the sandbox environment. All API calls will be simulated.</p>
                        </div>
                        <div class="mt-4">
                            <button class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Switch to Production
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
