# Testing Guide

This document provides comprehensive instructions for testing the ISP Solution application.

## Table of Contents

1. [Test Environment Setup](#test-environment-setup)
2. [Running Tests](#running-tests)
3. [Test Suites](#test-suites)
4. [Writing Tests](#writing-tests)
5. [CI/CD Integration](#cicd-integration)
6. [Troubleshooting](#troubleshooting)

---

## Test Environment Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL 8.0
- Redis (for integration tests)
- Docker (optional, for isolated testing)

### Environment Configuration

1. **Copy test environment file:**
```bash
cp .env.example .env.testing
```

2. **Update test database configuration in `.env.testing`:**
```env
APP_ENV=testing
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ispsolution_test
DB_USERNAME=root
DB_PASSWORD=root

RADIUS_DB_HOST=127.0.0.1
RADIUS_DB_PORT=3307
RADIUS_DB_DATABASE=radius_test
RADIUS_DB_USERNAME=root
RADIUS_DB_PASSWORD=root

REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

3. **Create test databases:**
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS ispsolution_test;"
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS radius_test;"
```

4. **Run migrations for test databases:**
```bash
php artisan migrate --env=testing --force
php artisan migrate --database=radius --path=database/migrations/radius --env=testing --force
```

5. **Seed test data (optional):**
```bash
php artisan db:seed --class=RoleSeeder --env=testing
php artisan db:seed --class=ServicePackageSeeder --env=testing
```

---

## Running Tests

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Suite

```bash
# Run unit tests
php artisan test --testsuite=Unit

# Run feature tests
php artisan test --testsuite=Feature

# Run integration tests
php artisan test --testsuite=Integration
```

### Run Specific Test File

```bash
php artisan test tests/Unit/Services/IpamServiceTest.php
```

### Run Specific Test Method

```bash
php artisan test --filter testAllocateIP
```

### Run Tests with Coverage

```bash
php artisan test --coverage
```

### Run Tests with Minimum Coverage

```bash
php artisan test --coverage --min=80
```

### Run Tests in Parallel

```bash
php artisan test --parallel
```

---

## Test Suites

### Unit Tests

**Location:** `tests/Unit/`

Tests individual classes and methods in isolation.

**Examples:**
- `tests/Unit/Services/IpamServiceTest.php` - IPAM service logic
- `tests/Unit/Services/RadiusServiceTest.php` - RADIUS service logic
- `tests/Unit/Services/MikrotikServiceTest.php` - MikroTik service logic
- `tests/Unit/Models/RoleTest.php` - Role model logic

**Run unit tests:**
```bash
php artisan test --testsuite=Unit
```

### Feature Tests

**Location:** `tests/Feature/`

Tests complete features and HTTP endpoints.

**Examples:**
- `tests/Feature/Api/V1/IpamApiTest.php` - IPAM API endpoints
- `tests/Feature/Api/V1/RadiusApiTest.php` - RADIUS API endpoints
- `tests/Feature/Api/V1/MikrotikApiTest.php` - MikroTik API endpoints
- `tests/Feature/Commands/IpamCleanupTest.php` - Artisan commands

**Run feature tests:**
```bash
php artisan test --testsuite=Feature
```

### Integration Tests

**Location:** `tests/Integration/`

Tests interactions between multiple components with real database and services.

**Examples:**
- `tests/Integration/IpamIntegrationTest.php` - IPAM with database
- `tests/Integration/RadiusIntegrationTest.php` - RADIUS with separate database
- `tests/Integration/MikrotikIntegrationTest.php` - MikroTik with mock server

**Run integration tests:**
```bash
php artisan test --testsuite=Integration
```

---

## Writing Tests

### Test Structure

```php
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\IpamService;
use App\Models\IpSubnet;

class IpamServiceTest extends TestCase
{
    protected IpamService $ipamService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ipamService = app(IpamService::class);
    }

    public function test_can_allocate_ip_address(): void
    {
        // Arrange
        $subnet = IpSubnet::factory()->create();

        // Act
        $allocation = $this->ipamService->allocateIP(
            $subnet->id,
            '00:11:22:33:44:55',
            'testuser'
        );

        // Assert
        $this->assertNotNull($allocation);
        $this->assertEquals($subnet->id, $allocation->subnet_id);
        $this->assertEquals('testuser', $allocation->username);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
```

### Database Testing

#### Using Factories

```php
use App\Models\IpPool;
use App\Models\IpSubnet;

// Create a pool with factory
$pool = IpPool::factory()->create([
    'name' => 'Test Pool',
    'start_ip' => '10.0.0.1',
    'end_ip' => '10.0.0.254'
]);

// Create multiple subnets
$subnets = IpSubnet::factory()->count(3)->create([
    'pool_id' => $pool->id
]);
```

#### Database Transactions

Tests automatically wrap in transactions and rollback:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyTest extends TestCase
{
    use RefreshDatabase; // Automatically rollback after each test

    public function test_something(): void
    {
        // Database changes will be rolled back
    }
}
```

### API Testing

```php
public function test_can_create_ip_pool(): void
{
    $response = $this->postJson('/api/v1/ipam/pools', [
        'name' => 'Test Pool',
        'start_ip' => '10.0.0.1',
        'end_ip' => '10.0.0.254',
        'gateway' => '10.0.0.1'
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'IP pool created successfully'
        ])
        ->assertJsonStructure([
            'data' => ['id', 'name', 'start_ip']
        ]);
}
```

### Mocking Services

```php
use App\Contracts\MikrotikServiceInterface;
use Mockery;

public function test_with_mocked_service(): void
{
    // Create mock
    $mock = Mockery::mock(MikrotikServiceInterface::class);
    
    // Set expectations
    $mock->shouldReceive('connectRouter')
        ->once()
        ->with(1)
        ->andReturn(true);
    
    // Bind mock to container
    $this->app->instance(MikrotikServiceInterface::class, $mock);
    
    // Test code using the mock
    $result = app(MikrotikServiceInterface::class)->connectRouter(1);
    $this->assertTrue($result);
}
```

### Command Testing

```php
public function test_ipam_cleanup_command(): void
{
    $this->artisan('ipam:cleanup --force')
        ->expectsOutput('Starting IPAM cleanup...')
        ->assertExitCode(0);
}
```

---

## CI/CD Integration

### GitHub Actions

Tests run automatically on:
- Push to `main`, `develop`, or `copilot/**` branches
- Pull requests to `main` or `develop`

**Workflows:**
- `.github/workflows/test.yml` - Unit and feature tests
- `.github/workflows/lint.yml` - Code quality (PHPStan, Pint)
- `.github/workflows/integration.yml` - Integration tests with Docker

### Local CI Simulation

Run the same checks as CI locally:

```bash
# Run tests
php artisan test --coverage --min=80

# Run PHPStan
vendor/bin/phpstan analyse

# Run Laravel Pint
vendor/bin/pint --test

# Run all checks
make test && make lint
```

---

## Test Coverage

### Generate Coverage Report

```bash
# HTML report
php artisan test --coverage-html=coverage

# Open report in browser
open coverage/index.html
```

### View Coverage Summary

```bash
php artisan test --coverage
```

**Expected Coverage:**
- Overall: > 80%
- Services: > 90%
- Controllers: > 85%
- Models: > 75%

### Coverage Configuration

Edit `phpunit.xml` to configure coverage:

```xml
<coverage>
    <include>
        <directory suffix=".php">app</directory>
    </include>
    <exclude>
        <directory>app/Console</directory>
        <directory>app/Exceptions</directory>
    </exclude>
</coverage>
```

---

## Continuous Testing

### Watch Mode

Use Laravel Sail or custom scripts for automatic test running:

```bash
# Using entr (install with: brew install entr)
find app tests -name "*.php" | entr -c php artisan test
```

### PHPUnit Watch

```bash
# Install phpunit-watcher
composer require --dev spatie/phpunit-watcher

# Run watcher
./vendor/bin/phpunit-watcher watch
```

---

## Troubleshooting

### Common Issues

#### 1. Database Connection Errors

**Problem:** `SQLSTATE[HY000] [2002] Connection refused`

**Solution:**
```bash
# Check MySQL is running
sudo systemctl status mysql

# Verify credentials in .env.testing
DB_HOST=127.0.0.1
DB_PORT=3306
```

#### 2. RADIUS Database Not Found

**Problem:** `SQLSTATE[HY000] [1049] Unknown database 'radius_test'`

**Solution:**
```bash
mysql -u root -p -e "CREATE DATABASE radius_test;"
php artisan migrate --database=radius --path=database/migrations/radius --env=testing
```

#### 3. Redis Connection Failed

**Problem:** `Connection refused [tcp://127.0.0.1:6379]`

**Solution:**
```bash
# Start Redis
redis-server

# Or use Docker
docker run -d -p 6379:6379 redis:alpine
```

#### 4. Parallel Tests Failing

**Problem:** Tests pass individually but fail in parallel

**Solution:**
```bash
# Disable parallel execution
php artisan test --parallel=false

# Or configure parallel settings in phpunit.xml
```

#### 5. Mock Server Issues

**Problem:** MikroTik integration tests failing

**Solution:**
```bash
# Start mock MikroTik server
cd tests/mock-servers/mikrotik
npm install
npm start

# Or use Docker Compose
docker-compose up mikrotik-mock
```

---

## Best Practices

### 1. Test Naming

Use descriptive test names:

```php
// Good
public function test_can_allocate_ip_when_subnet_has_available_addresses(): void

// Bad
public function test1(): void
```

### 2. Arrange-Act-Assert Pattern

```php
public function test_example(): void
{
    // Arrange - Set up test data
    $user = User::factory()->create();
    
    // Act - Perform the action
    $result = $service->doSomething($user);
    
    // Assert - Verify the outcome
    $this->assertTrue($result);
}
```

### 3. One Assertion Per Test

Focus each test on a single behavior:

```php
// Good
public function test_allocates_ip_address(): void
{
    $allocation = $this->ipamService->allocateIP(...);
    $this->assertNotNull($allocation);
}

public function test_increments_allocation_count(): void
{
    $before = IpAllocation::count();
    $this->ipamService->allocateIP(...);
    $this->assertEquals($before + 1, IpAllocation::count());
}
```

### 4. Test Data Isolation

Use factories and database transactions:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyTest extends TestCase
{
    use RefreshDatabase;
    
    // Each test starts with clean database
}
```

### 5. Mock External Services

Don't make real API calls in tests:

```php
// Mock MikroTik service
$mock = Mockery::mock(MikrotikServiceInterface::class);
$mock->shouldReceive('connectRouter')->andReturn(true);
$this->app->instance(MikrotikServiceInterface::class, $mock);
```

---

## Performance Testing

### Database Query Optimization

```php
use Illuminate\Support\Facades\DB;

public function test_queries_are_optimized(): void
{
    DB::enableQueryLog();
    
    // Perform action
    $users = NetworkUser::with('package')->get();
    
    // Check query count (should avoid N+1 problems)
    $queries = DB::getQueryLog();
    $this->assertCount(2, $queries); // 1 for users, 1 for packages
}
```

### Load Testing

Use Laravel Dusk for browser testing:

```bash
composer require --dev laravel/dusk
php artisan dusk:install
php artisan dusk
```

---

## Resources

- [Laravel Testing Documentation](https://laravel.com/docs/12.x/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Mockery Documentation](http://docs.mockery.io/)
- [Pest PHP (Alternative Testing Framework)](https://pestphp.com/)

---

## Support

For testing issues:
- Review this guide
- Check [README.md](../README.md)
- Open an issue on GitHub with test output
