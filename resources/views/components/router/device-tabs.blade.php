@props(['activeTab' => 'mikrotik'])

<!-- Device Type Tabs -->
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
            <a href="{{ route('panel.admin.network.routers') }}" 
               class="{{ $activeTab === 'mikrotik' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                MikroTik Routers
            </a>
            <a href="{{ route('panel.admin.network.nas') }}" 
               class="{{ $activeTab === 'nas' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                NAS Devices
            </a>
            <a href="{{ route('panel.admin.cisco') }}" 
               class="{{ $activeTab === 'cisco' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Cisco Devices
            </a>
        </nav>
    </div>
</div>
