# Database Optimization Guide

## Overview

This document outlines the database indexing strategy and optimization techniques implemented in the ISP Solution system for improved query performance.

## Indexing Strategy

### Single Column Indexes

Single column indexes are created on frequently queried columns to improve lookup performance:

#### Users Table
- `email` - For login and user lookup
- `username` - For authentication
- `tenant_id` - For tenant-scoped queries

#### Network Users Table
- `username` - For user lookups
- `tenant_id` - For tenant isolation
- `status` - For filtering active/inactive users
- `service_type` - For filtering by service type (PPPoE, Hotspot, Static IP)

#### Invoices Table
- `tenant_id` - For tenant-scoped queries
- `user_id` - For user-specific invoice queries
- `status` - For filtering by payment status
- `due_date` - For overdue invoice queries
- `invoice_number` - For quick invoice lookup

#### Payments Table
- `tenant_id` - For tenant-scoped queries
- `user_id` - For user-specific payment history
- `invoice_id` - For invoice-payment relationships
- `status` - For filtering by payment status
- `payment_method` - For payment method analysis
- `paid_at` - For date-based queries

#### Packages Table
- `tenant_id` - For tenant-scoped queries
- `status` - For filtering active packages

#### Hotspot Users Table
- `username` - For user authentication
- `tenant_id` - For tenant isolation
- `status` - For filtering by status
- `is_verified` - For filtering verified users

#### MikroTik Routers Table
- `tenant_id` - For tenant-scoped queries
- `status` - For filtering active routers
- `ip_address` - For router lookup by IP

#### Payment Gateways Table
- `tenant_id` - For tenant-scoped queries
- `is_active` - For filtering active gateways

#### Tenants Table
- `domain` - For subdomain-based tenant lookup
- `is_active` - For filtering active tenants

### Composite Indexes

Composite indexes optimize queries that filter on multiple columns:

#### Users Table
- `(tenant_id, is_active)` - For active users within a tenant

#### Network Users Table
- `(tenant_id, status)` - For filtering users by tenant and status
- `(tenant_id, service_type)` - For filtering users by tenant and service type

#### Invoices Table
- `(tenant_id, status)` - For filtering invoices by tenant and status
- `(tenant_id, user_id)` - For user invoices within a tenant
- `(status, due_date)` - For overdue invoice queries

#### Payments Table
- `(tenant_id, status)` - For filtering payments by tenant and status
- `(invoice_id, status)` - For invoice payment status

#### Packages Table
- `(tenant_id, status)` - For active packages within a tenant

#### Hotspot Users Table
- `(tenant_id, status)` - For filtering hotspot users by tenant and status
- `(tenant_id, is_verified)` - For verified users within a tenant

#### MikroTik Routers Table
- `(tenant_id, status)` - For active routers within a tenant

#### Payment Gateways Table
- `(tenant_id, is_active)` - For active gateways within a tenant

## Query Optimization Techniques

### 1. Eager Loading

Use eager loading to prevent N+1 query problems:

```php
// Bad: N+1 queries
$users = NetworkUser::all();
foreach ($users as $user) {
    echo $user->package->name; // Separate query for each user
}

// Good: Eager loading
$users = NetworkUser::with('package')->get();
foreach ($users as $user) {
    echo $user->package->name; // Already loaded
}
```

### 2. Select Specific Columns

Only select the columns you need:

```php
// Bad: Loads all columns
$users = NetworkUser::with('package')->get();

// Good: Select specific columns
$users = NetworkUser::select(['id', 'username', 'package_id', 'status'])
    ->with('package:id,name,price')
    ->get();
```

### 3. Use Query Scopes

Utilize model scopes for common query patterns:

```php
// Defined in NetworkUser model
public function scopeActive($query) {
    return $query->where('status', 'active');
}

// Usage
$activeUsers = NetworkUser::active()->get();
```

### 4. Indexed Filters

Always use indexed columns in WHERE clauses:

```php
// Good: Uses indexed columns
$invoices = Invoice::where('tenant_id', $tenantId)
    ->where('status', 'pending')
    ->where('due_date', '<', now())
    ->get();
```

### 5. Chunk Large Datasets

Process large datasets in chunks to reduce memory usage:

```php
NetworkUser::where('tenant_id', $tenantId)
    ->chunk(1000, function ($users) {
        foreach ($users as $user) {
            // Process user
        }
    });
```

## Performance Monitoring

### Slow Query Logging

The system logs queries taking longer than 100ms:

```php
use App\Services\PerformanceMonitoringService;

PerformanceMonitoringService::start();

// Your code here

$metrics = PerformanceMonitoringService::stop();
// Returns: query_count, slow_queries, duration_ms
```

### Query Debugging

Enable query logging for debugging:

```php
PerformanceMonitoringService::enableQueryLogging();

// Execute queries

$queries = PerformanceMonitoringService::getQueryLog();
```

## Best Practices

### 1. Multi-Tenant Queries
Always scope queries by `tenant_id` for data isolation and index utilization:

```php
$users = NetworkUser::where('tenant_id', auth()->user()->tenant_id)
    ->where('status', 'active')
    ->get();
```

### 2. Pagination
Use pagination for large result sets:

```php
$users = NetworkUser::where('tenant_id', $tenantId)
    ->paginate(20);
```

### 3. Count Queries
Use efficient counting methods:

```php
// Bad: Loads all records
$count = NetworkUser::all()->count();

// Good: Database count
$count = NetworkUser::count();

// Better: With conditions
$count = NetworkUser::where('status', 'active')->count();
```

### 4. Existence Checks
Use `exists()` instead of `count()` for existence checks:

```php
// Bad
if (Invoice::where('user_id', $userId)->count() > 0) {
    // ...
}

// Good
if (Invoice::where('user_id', $userId)->exists()) {
    // ...
}
```

### 5. Avoid SELECT *
Always specify columns when possible:

```php
// Bad
$users = User::all();

// Good
$users = User::select(['id', 'name', 'email'])->get();
```

## Migration Commands

### Apply Performance Indexes
```bash
php artisan migrate
```

### Rollback Performance Indexes
```bash
php artisan migrate:rollback
```

### Check Migration Status
```bash
php artisan migrate:status
```

## Monitoring and Maintenance

### 1. Check Index Usage
Periodically review index usage in database logs to identify unused indexes.

### 2. Analyze Query Performance
Use the PerformanceMonitoringService to identify slow queries and optimize them.

### 3. Update Statistics
Ensure database statistics are up to date for optimal query planning (database-specific).

### 4. Regular Maintenance
- MySQL: Run `OPTIMIZE TABLE` periodically
- PostgreSQL: Run `VACUUM ANALYZE` regularly

## Common Query Patterns

### Dashboard Statistics
```php
$stats = Cache::remember('dashboard:stats:' . $tenantId, 300, function () use ($tenantId) {
    return [
        'total_users' => NetworkUser::where('tenant_id', $tenantId)->count(),
        'active_users' => NetworkUser::where('tenant_id', $tenantId)
            ->where('status', 'active')->count(),
        'pending_invoices' => Invoice::where('tenant_id', $tenantId)
            ->where('status', 'pending')->count(),
    ];
});
```

### User with Related Data
```php
$user = NetworkUser::select(['id', 'username', 'package_id', 'status'])
    ->with([
        'package:id,name,price',
        'sessions' => fn($q) => $q->latest()->limit(10)
    ])
    ->findOrFail($id);
```

### Invoice Listing
```php
$invoices = Invoice::select(['id', 'invoice_number', 'user_id', 'total_amount', 'status', 'due_date'])
    ->with([
        'user:id,name,email',
        'payments:id,invoice_id,amount,status'
    ])
    ->where('tenant_id', $tenantId)
    ->latest()
    ->paginate(20);
```

## Performance Targets

- Page load time: < 500ms
- API response time: < 200ms
- Database query time: < 100ms per query
- Maximum queries per request: < 20

## Troubleshooting

### High Query Count
- Enable query logging
- Check for N+1 problems
- Implement eager loading

### Slow Queries
- Check EXPLAIN output
- Verify indexes are being used
- Consider query rewrite or caching

### Lock Contention
- Use optimistic locking where possible
- Keep transactions short
- Consider queue for long-running operations
