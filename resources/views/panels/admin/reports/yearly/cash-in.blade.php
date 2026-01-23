@extends('panels.layouts.app')
@section('title', 'Yearly Cash In Report')
@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Yearly Cash In Report - {{ $year }}</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Total Income: {{ number_format($yearlyTotal, 2) }}</p>
        </div>
    </div>
    <!-- Report content would go here -->
</div>
@endsection
