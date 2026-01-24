@extends('panels.layouts.app')

@section('title', 'NAS Device Details')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold">{{ $device->name }}</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">NAS Device Details</p>
                </div>
                <div class="flex space-x-3">
                    <button data-test-nas="{{ $device->id }}" class="test-nas-btn inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Test Connection
                    </button>
                    <a href="{{ route('panel.admin.network.nas.edit', $device->id) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
                    <a href="{{ route('panel.admin.network.nas') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Device Details -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Basic Information</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Name:</span>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $device->name }}</p>
                        </div>

                        <div>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">NAS Name:</span>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $device->nas_name }}</p>
                        </div>

                        <div>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Short Name:</span>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $device->short_name }}</p>
                        </div>

                        <div>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Server:</span>
                            <p class="mt-1 text-gray-900 dark:text-gray-100 font-mono">{{ $device->server }}</p>
                        </div>

                        <div>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Type:</span>
                            <p class="mt-1 text-gray-900 dark:text-gray-100 capitalize">{{ $device->type }}</p>
                        </div>

                        <div>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Ports:</span>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $device->ports }}</p>
                        </div>
                    </div>
                </div>

                <!-- Status & Additional Info -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Status & Details</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status:</span>
                            <p class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($device->status === 'active') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                    @elseif($device->status === 'inactive') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                    @endif">
                                    {{ ucfirst($device->status) }}
                                </span>
                            </p>
                        </div>

                        @if($device->description)
                        <div>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Description:</span>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $device->description }}</p>
                        </div>
                        @endif

                        <div>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Created:</span>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $device->created_at->format('M d, Y H:i') }}</p>
                        </div>

                        <div>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated:</span>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $device->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-2 border-red-200 dark:border-red-800">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-4">Danger Zone</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Once you delete a NAS device, there is no going back. Please be certain.</p>
            <form action="{{ route('panel.admin.network.nas.destroy', $device->id) }}" method="POST" id="delete-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete NAS Device
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce }}">
// Test NAS connection
document.addEventListener('click', function(e) {
    const button = e.target.closest('.test-nas-btn');
    if (button) {
        const nasId = button.getAttribute('data-test-nas');
        testNasConnection(nasId);
    }
});

// Confirm delete
document.getElementById('delete-form').addEventListener('submit', function(e) {
    if (!confirm('Are you sure you want to delete this NAS device? This action cannot be undone.')) {
        e.preventDefault();
    }
});

function testNasConnection(nasId) {
    fetch(`/panel/admin/network/nas/${nasId}/test-connection`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('✓ ' + data.message);
        } else {
            alert('✗ ' + data.message);
        }
    })
    .catch(error => {
        alert('✗ Connection test failed: ' + error.message);
        console.error('Error:', error);
    });
}
</script>
@endpush
