@extends('panels.layouts.app')

@section('title', 'Reseller Management')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Reseller Management</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Manage resellers and their child accounts</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Resellers List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($resellers as $reseller)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <!-- Reseller Info -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                                {{ strtoupper(substr($reseller->name, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $reseller->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $reseller->username }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Child Accounts:</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $reseller->childAccounts->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Active Children:</span>
                            <span class="text-sm font-semibold text-green-600">
                                {{ $reseller->childAccounts->where('status', 'active')->count() }}
                            </span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <a href="{{ route('panel.admin.customers.show', $reseller) }}" 
                           class="flex-1 text-center px-3 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition-colors">
                            View Details
                        </a>
                        <a href="{{ route('panel.admin.customers.index', ['parent_id' => $reseller->id]) }}" 
                           class="flex-1 text-center px-3 py-2 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700 transition-colors">
                            View Children
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-center text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No resellers found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Resellers are customers with child accounts.
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($resellers->hasPages())
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            {{ $resellers->links() }}
        </div>
    @endif
</div>
@endsection
