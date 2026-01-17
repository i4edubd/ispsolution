@extends('panels.layouts.app')

@section('title', 'API Documentation')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">API Documentation</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Complete API reference and integration guides</p>
                </div>
                <div>
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Postman Collection
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- API Key -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">API Key</h2>
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <input type="text" readonly value="{{ $apiKey ?? 'YOUR-API-KEY-HERE' }}" class="w-full font-mono text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <button class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Copy
                </button>
                <button class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Regenerate
                </button>
            </div>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Keep your API key secure. Do not share it publicly.</p>
        </div>
    </div>

    <!-- Quick Start -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Start</h2>
            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Base URL</h3>
                    <div class="bg-gray-100 dark:bg-gray-900 rounded-md p-4">
                        <code class="text-sm text-gray-900 dark:text-gray-100">https://api.yourdomain.com/v1</code>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Authentication</h3>
                    <div class="bg-gray-100 dark:bg-gray-900 rounded-md p-4">
                        <code class="text-sm text-gray-900 dark:text-gray-100">Authorization: Bearer YOUR_API_KEY</code>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Example Request</h3>
                    <div class="bg-gray-900 rounded-md p-4 overflow-x-auto">
                        <pre class="text-sm text-green-400"><code>curl -X GET "https://api.yourdomain.com/v1/users" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json"</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- API Endpoints -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">API Endpoints</h2>
            <div class="space-y-4">
                <!-- Users Endpoint -->
                <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center space-x-3 mb-3">
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">GET</span>
                        <code class="text-sm font-mono text-gray-900 dark:text-gray-100">/api/v1/users</code>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Retrieve a list of all users</p>
                    <details class="text-sm">
                        <summary class="cursor-pointer text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">View Details</summary>
                        <div class="mt-3 space-y-2">
                            <p class="text-gray-900 dark:text-gray-100"><strong>Parameters:</strong></p>
                            <ul class="list-disc list-inside text-gray-600 dark:text-gray-400">
                                <li>page (optional) - Page number</li>
                                <li>limit (optional) - Items per page</li>
                            </ul>
                        </div>
                    </details>
                </div>

                <!-- User by ID Endpoint -->
                <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center space-x-3 mb-3">
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">GET</span>
                        <code class="text-sm font-mono text-gray-900 dark:text-gray-100">/api/v1/users/{id}</code>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Retrieve a specific user by ID</p>
                    <details class="text-sm">
                        <summary class="cursor-pointer text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">View Details</summary>
                        <div class="mt-3 space-y-2">
                            <p class="text-gray-900 dark:text-gray-100"><strong>Parameters:</strong></p>
                            <ul class="list-disc list-inside text-gray-600 dark:text-gray-400">
                                <li>id (required) - User ID</li>
                            </ul>
                        </div>
                    </details>
                </div>

                <!-- Create User Endpoint -->
                <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center space-x-3 mb-3">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">POST</span>
                        <code class="text-sm font-mono text-gray-900 dark:text-gray-100">/api/v1/users</code>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Create a new user</p>
                    <details class="text-sm">
                        <summary class="cursor-pointer text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">View Details</summary>
                        <div class="mt-3 space-y-2">
                            <p class="text-gray-900 dark:text-gray-100"><strong>Body:</strong></p>
                            <div class="bg-gray-900 rounded-md p-3 overflow-x-auto">
                                <pre class="text-xs text-green-400"><code>{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "secure_password",
  "role": "customer"
}</code></pre>
                            </div>
                        </div>
                    </details>
                </div>

                <!-- Update User Endpoint -->
                <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center space-x-3 mb-3">
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">PUT</span>
                        <code class="text-sm font-mono text-gray-900 dark:text-gray-100">/api/v1/users/{id}</code>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Update an existing user</p>
                </div>

                <!-- Delete User Endpoint -->
                <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center space-x-3 mb-3">
                        <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">DELETE</span>
                        <code class="text-sm font-mono text-gray-900 dark:text-gray-100">/api/v1/users/{id}</code>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Delete a user</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Response Codes -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Response Codes</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Code
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Description
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">200</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">Success</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">201</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">Created</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-yellow-600">400</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">Bad Request</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">401</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">Unauthorized</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">404</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">Not Found</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">500</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">Internal Server Error</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
