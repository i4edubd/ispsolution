# Multi-Tenancy Implementation Guide

## Overview

The ISP Solution implements a multi-tenancy architecture that allows multiple ISPs (tenants) to operate independently within the same application instance. Each tenant's data is isolated from others, ensuring security and privacy.

## Architecture

### Components

1. **Tenant Model** (`App\Models\Tenant`)
   - Stores tenant metadata (name, domain, subdomain, settings, status)
   - Supports soft deletes
   - Has relationships with users, packages, IP pools, and network resources

2. **TenancyService** (`App\Services\TenancyService`)
   - Manages the current tenant context
   - Resolves tenant by domain or subdomain
   - Provides methods to run code in specific tenant contexts
   - Implements caching for performance

3. **BelongsToTenant Trait** (`App\Traits\BelongsToTenant`)
   - Automatically sets `tenant_id` when creating records
   - Adds global scope to filter queries by current tenant
   - Provides `forTenant()` and `allTenants()` scopes

4. **ResolveTenant Middleware** (`App\Http\Middleware\ResolveTenant`)
   - Resolves tenant from request host
   - Sets current tenant in TenancyService
   - Returns 404 for invalid tenants
   - Allows public routes to bypass tenant resolution

## Usage

### Setting Up a New Tenant

1. **Create Tenant Record**
```php
use App\Models\Tenant;

$tenant = Tenant::create([
    'name' => 'Example ISP',
    'domain' => 'example-isp.com',
    'subdomain' => 'example',
    'status' => 'active',
    'settings' => [
        'currency' => 'BDT',
        'timezone' => 'Asia/Dhaka',
        'billing_day' => 1,
    ],
]);
```

2. **Point Domain**
   - Configure DNS to point `example-isp.com` to your server
   - Or use subdomain: `example.yourdomain.com`

### Using Models with Tenancy

**Automatic tenant_id assignment:**
```php
use App\Models\User;
use App\Traits\BelongsToTenant;

class Customer extends Model
{
    use BelongsToTenant;
    
    // tenant_id will be automatically set on create
}

// Create customer - tenant_id set automatically
$customer = Customer::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
]);
```

**Querying with tenant scope:**
```php
// Returns only customers for current tenant
$customers = Customer::all();

// Bypass tenant scope to get all customers
$allCustomers = Customer::allTenants()->get();

// Query for specific tenant
$tenant2Customers = Customer::allTenants()
    ->forTenant(2)
    ->get();
```

### Running Code in Tenant Context

```php
use App\Services\TenancyService;

$tenancyService = app(TenancyService::class);
$tenant = Tenant::find(1);

$result = $tenancyService->runForTenant($tenant, function () {
    // Code here runs in tenant 1's context
    return Customer::count();
});
```

### Helper Functions

```php
// Get current tenant
$tenant = getCurrentTenant();

// Get current tenant ID
$tenantId = getCurrentTenantId();
```

## Migration Guide

### Adding tenant_id to Existing Tables

Use the provided migration as a template:

```php
public function up(): void
{
    Schema::table('your_table', function (Blueprint $table) {
        $table->foreignId('tenant_id')
              ->nullable()  // Important: nullable for backward compatibility
              ->after('id')
              ->constrained()
              ->nullOnDelete();
        
        $table->index('tenant_id');
    });
}
```

**Important:** Make `tenant_id` nullable to preserve existing data.

### Updating Existing Models

1. Add `BelongsToTenant` trait
2. Add `tenant_id` to `$fillable` array
3. Test thoroughly

```php
use App\Traits\BelongsToTenant;

class YourModel extends Model
{
    use BelongsToTenant;
    
    protected $fillable = [
        'tenant_id',
        // ... other fields
    ];
}
```

## Running Seeders

```bash
# Seed tenants
php artisan db:seed --class=TenantSeeder

# Seed operators
php artisan db:seed --class=OperatorSeeder

# Seed packages (if applicable)
php artisan db:seed --class=PackageSeeder
```

## Testing

Run tenancy tests:

```bash
php artisan test --filter=Tenancy
```

## Troubleshooting

### Issue: Tenant not resolved

**Solution:** Check that:
1. Tenant record exists with correct domain/subdomain
2. Tenant status is 'active'
3. DNS is configured correctly
4. ResolveTenant middleware is applied to routes

### Issue: Seeing data from other tenants

**Solution:** Ensure:
1. Model uses `BelongsToTenant` trait
2. Global scope is not being bypassed unintentionally
3. Direct queries use `whereHas()` or proper scoping

### Issue: Can't create records without tenant

**Solution:**
1. Ensure a tenant is set in current context
2. Or explicitly set `tenant_id` when creating records
3. For super admin operations, use `allTenants()` scope

## Security Considerations

1. **Always validate tenant ownership** before performing sensitive operations
2. **Use policies** to enforce tenant-level authorization
3. **Log tenant context** in audit trails
4. **Test thoroughly** with multiple tenants
5. **Never expose tenant_id** in URLs or public APIs

## Performance Tips

1. TenancyService uses caching - clear cache when tenant changes
2. Index `tenant_id` columns for better query performance
3. Use eager loading to prevent N+1 queries: `->with('tenant')`
4. Consider read replicas for large multi-tenant deployments

## Advanced Usage

### Multi-Database Tenancy

For true database isolation per tenant:

1. Set `database` field in tenant record
2. Modify TenancyService to switch connections
3. Update migrations to run per-tenant database
4. Implement tenant database provisioning workflow

### Custom Tenant Resolution

To use custom logic for tenant resolution, extend `ResolveTenant` middleware or modify `TenancyService::resolveTenantByDomain()`.

## API Reference

### TenancyService Methods

- `setCurrentTenant(?Tenant $tenant): void` - Set current tenant
- `getCurrentTenant(): ?Tenant` - Get current tenant
- `getCurrentTenantId(): ?int` - Get current tenant ID
- `hasTenant(): bool` - Check if tenant is set
- `resolveTenantByDomain(string $host): ?Tenant` - Resolve tenant by host
- `runForTenant(?Tenant $tenant, callable $callback): mixed` - Run code in tenant context
- `forgetTenant(): void` - Clear current tenant

### BelongsToTenant Trait Scopes

- `forTenant(int $tenantId)` - Filter by specific tenant
- `allTenants()` - Bypass global tenant scope

## Support

For issues or questions:
- Check the FAQ in docs/faq.md
- Review code examples in tests/Feature/
- Contact development team
