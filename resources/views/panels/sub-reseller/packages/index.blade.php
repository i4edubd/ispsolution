@extends('panels.layouts.app')

@section('title', 'Available Packages')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Available Packages</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Browse and offer packages to your customers</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                    <input type="text" placeholder="Search packages..." class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Service Type</label>
                    <select class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Types</option>
                        <option value="pppoe">PPPoE</option>
                        <option value="hotspot">Hotspot</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price Range</label>
                    <select class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Prices</option>
                        <option value="low">Under 1000 BDT</option>
                        <option value="mid">1000 - 2000 BDT</option>
                        <option value="high">Above 2000 BDT</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Packages Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($packages as $package)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow duration-300">
                <div class="p-6">
                    <!-- Package Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $package->name }}</h3>
                            <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $package->service_type === 'pppoe' ? 'bg-purple-100 text-purple-800' : 'bg-orange-100 text-orange-800' }}">
                                {{ strtoupper($package->service_type) }}
                            </span>
                        </div>
                        @if($package->status === 'active')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        @endif
                    </div>

                    <!-- Package Description -->
                    @if($package->description)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            {{ Str::limit($package->description, 100) }}
                        </p>
                    @endif

                    <!-- Package Details -->
                    <div class="space-y-3 mb-4">
                        <!-- Speed -->
                        <div class="flex items-center text-sm">
                            <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300">
                                <span class="font-semibold">Speed:</span> {{ $package->speed ?? 'N/A' }}
                            </span>
                        </div>

                        <!-- Price -->
                        <div class="flex items-center text-sm">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300">
                                <span class="font-semibold">Price:</span> {{ $package->price ? number_format($package->price, 2) . ' BDT' : 'N/A' }}
                            </span>
                        </div>

                        <!-- Commission -->
                        @if(isset($package->commission))
                            <div class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">
                                    <span class="font-semibold">Commission:</span> {{ $package->commission }}%
                                </span>
                            </div>
                        @endif

                        <!-- Your Customers -->
                        @if(isset($package->my_customers_count))
                            <div class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">
                                    <span class="font-semibold">Your Customers:</span> {{ $package->my_customers_count }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Package Actions -->
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="#" class="block w-full text-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="mt-2 text-gray-500 dark:text-gray-400">No packages available.</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500">Contact your reseller for available packages.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($packages->hasPages())
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4">
                {{ $packages->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
