<?php

/**
 * Pagination Fix Verification Script
 * 
 * This script tests that all fixed controller methods return proper paginator objects.
 * Run with: php artisan tinker < tests/verify_pagination_fixes.php
 */

// Test 1: AdminController - packages method returns paginator
echo "Testing AdminController::packages()...\n";
$controller = new \App\Http\Controllers\Panel\AdminController();
$view = $controller->packages();
$packages = $view->getData()['packages'];
assert($packages instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator, 
    'AdminController::packages() should return LengthAwarePaginator');
assert(method_exists($packages, 'hasPages'), 
    'Paginator should have hasPages() method');
assert(method_exists($packages, 'links'), 
    'Paginator should have links() method');
echo "✓ AdminController::packages() returns proper paginator\n\n";

// Test 2: AdminController - deletedCustomers method returns paginator
echo "Testing AdminController::deletedCustomers()...\n";
$view = $controller->deletedCustomers();
$customers = $view->getData()['customers'];
assert($customers instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator,
    'AdminController::deletedCustomers() should return LengthAwarePaginator');
echo "✓ AdminController::deletedCustomers() returns proper paginator\n\n";

// Test 3: AdminController - customerImportRequests method returns paginator
echo "Testing AdminController::customerImportRequests()...\n";
$view = $controller->customerImportRequests();
$importRequests = $view->getData()['importRequests'];
assert($importRequests instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator,
    'AdminController::customerImportRequests() should return LengthAwarePaginator');
echo "✓ AdminController::customerImportRequests() returns proper paginator\n\n";

// Test 4: DeveloperController - logs method returns paginator
echo "Testing DeveloperController::logs()...\n";
$controller = new \App\Http\Controllers\Panel\DeveloperController();
$view = $controller->logs();
$logs = $view->getData()['logs'];
assert($logs instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator,
    'DeveloperController::logs() should return LengthAwarePaginator');
echo "✓ DeveloperController::logs() returns proper paginator\n\n";

// Test 5: SubResellerController - commission method returns proper data
echo "Testing SubResellerController::commission()...\n";
$controller = new \App\Http\Controllers\Panel\SubResellerController();
$view = $controller->commission();
$data = $view->getData();
assert(isset($data['transactions']), 'Should have transactions variable');
assert(isset($data['summary']), 'Should have summary variable');
assert($data['transactions'] instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator,
    'SubResellerController::commission() should return LengthAwarePaginator for transactions');
echo "✓ SubResellerController::commission() returns proper data\n\n";

// Test 6: Verify paginator methods work
echo "Testing paginator methods...\n";
$testPaginator = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20, 1);
assert(method_exists($testPaginator, 'hasPages'), 'Should have hasPages method');
assert(method_exists($testPaginator, 'links'), 'Should have links method');
assert($testPaginator->hasPages() === false, 'Empty paginator should return false for hasPages');
echo "✓ Paginator methods work correctly\n\n";

echo "==================================================\n";
echo "✓✓✓ All pagination fixes verified successfully! ✓✓✓\n";
echo "==================================================\n";
