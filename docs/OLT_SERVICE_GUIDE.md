# OLT Management Service Documentation

## Overview

The OLT (Optical Line Terminal) Management Service provides comprehensive functionality for managing OLT devices in your ISP system. It handles connections, ONU discovery, status monitoring, configuration backups, and more.

## Features

### 1. Connection Management
- **SSH/Telnet Support**: Secure connections to OLT devices using phpseclib3
- **Connection Pooling**: Efficient connection reuse within request lifecycle
- **Health Monitoring**: Automatic health checks with latency measurements
- **Multi-vendor Support**: Extensible architecture for different OLT vendors (Huawei, ZTE, Fiberhome, etc.)

### 2. ONU Discovery & Management
- **Automatic Discovery**: Discover all ONUs connected to an OLT
- **Database Synchronization**: Keep ONU records in sync with actual devices
- **Status Monitoring**: Real-time status updates including signal strength and distance
- **ONU Operations**: Authorize, unauthorize, and reboot ONUs remotely

### 3. Configuration Backups
- **Automated Backups**: Scheduled daily backups at 2 AM
- **Manual Backups**: On-demand backup creation via command or API
- **Backup Management**: List, export, and restore configurations
- **Storage Management**: Organized file storage in `storage/app/backups/olts/`

### 4. Statistics & Monitoring
- **System Statistics**: CPU, memory, temperature monitoring
- **Port Utilization**: Track bandwidth usage per PON port
- **ONU Metrics**: Online/offline counts, signal quality trends
- **Historical Data**: Time-series data for bandwidth analysis

## Installation & Setup

### 1. Dependencies
The service requires phpseclib3 for SSH connections. It has been added to composer.json:

```bash
composer require phpseclib/phpseclib:~3.0
```

### 2. Database Migration
Run the migration to create the `olt_backups` table:

```bash
php artisan migrate
```

### 3. Service Registration
The OLT service is automatically registered in `NetworkServiceProvider` as a scoped service, ensuring clean SSH connections per request.

## Usage

### Artisan Commands

#### Health Check
Check connectivity and health of all active OLTs:

```bash
# Check all active OLTs
php artisan olt:health-check

# Check specific OLT with details
php artisan olt:health-check --olt=1 --details
```

**Output Example:**
```
Checking OLT health...
✓ Main OLT (192.168.1.1) - Healthy (Latency: 25ms)
✓ Branch OLT (192.168.2.1) - Healthy (Latency: 42ms)

Health Check Summary:
Healthy OLTs: 2
Unhealthy OLTs: 0
Total OLTs: 2
```

#### Sync ONUs
Discover and sync ONUs from OLTs:

```bash
# Sync all active OLTs
php artisan olt:sync-onus

# Sync specific OLT
php artisan olt:sync-onus --olt=1

# Force sync inactive OLT
php artisan olt:sync-onus --olt=1 --force
```

**Output Example:**
```
Syncing ONUs from OLT devices...
Syncing OLT: Main OLT
  ✓ Synced 45 ONUs

Sync Summary:
Total ONUs Synced: 45
Successful OLTs: 1
Failed OLTs: 0
```

#### Create Backups
Create configuration backups:

```bash
# Backup all active OLTs
php artisan olt:backup

# Backup specific OLT
php artisan olt:backup --olt=1

# Force backup inactive OLT
php artisan olt:backup --olt=1 --force
```

**Output Example:**
```
Creating OLT configuration backups...
Backing up OLT: Main OLT
  ✓ Backup created successfully
  File: olt_1_backup_2024-01-17_120530.cfg
  Size: 145.23 KB

Backup Summary:
Successful Backups: 1
Failed Backups: 0
Total OLTs: 1
```

### Service Usage in Code

#### Dependency Injection
```php
use App\Contracts\OltServiceInterface;

class OltController extends Controller
{
    public function __construct(
        private OltServiceInterface $oltService
    ) {}
}
```

#### Connection Management
```php
// Test connection
$result = $this->oltService->testConnection($oltId);
if ($result['success']) {
    echo "Connected! Latency: {$result['latency']}ms";
}

// Manual connect/disconnect
$this->oltService->connect($oltId);
// ... perform operations ...
$this->oltService->disconnect($oltId);
```

#### ONU Discovery
```php
// Discover ONUs
$onus = $this->oltService->discoverOnus($oltId);

// Sync to database
$syncedCount = $this->oltService->syncOnus($oltId);
```

#### ONU Operations
```php
// Get ONU status
$status = $this->oltService->getOnuStatus($onuId);

// Refresh status from OLT
$this->oltService->refreshOnuStatus($onuId);

// Authorize ONU
$this->oltService->authorizeOnu($onuId);

// Reboot ONU
$this->oltService->rebootOnu($onuId);

// Unauthorize ONU
$this->oltService->unauthorizeOnu($onuId);
```

#### Backup Management
```php
// Create backup
$this->oltService->createBackup($oltId);

// List backups
$backups = $this->oltService->getBackupList($oltId);

// Export backup
$filePath = $this->oltService->exportBackup($oltId, $backupId);

// Apply configuration
$config = [
    'interface gpon 0/1',
    'description Main PON Port',
];
$this->oltService->applyConfiguration($oltId, $config);
```

#### Statistics
```php
// Get OLT statistics
$stats = $this->oltService->getOltStatistics($oltId);
echo "Total ONUs: {$stats['total_onus']}";
echo "Online ONUs: {$stats['online_onus']}";

// Get port utilization
$utilization = $this->oltService->getPortUtilization($oltId);

// Get bandwidth usage
$usage = $this->oltService->getBandwidthUsage($oltId, 'daily');
```

## Scheduled Tasks

The following tasks are automatically scheduled in `routes/console.php`:

| Command | Schedule | Description |
|---------|----------|-------------|
| `olt:health-check` | Every 15 minutes | Check connectivity and update health status |
| `olt:sync-onus` | Hourly | Sync ONU data from all active OLTs |
| `olt:backup` | Daily at 2:00 AM | Create configuration backups |

## Configuration

### OLT Model Fields
- `name`: Friendly name for the OLT
- `ip_address`: IP address or hostname
- `port`: SSH/Telnet port (default: 22)
- `management_protocol`: ssh, telnet, or snmp
- `username`: Authentication username (encrypted)
- `password`: Authentication password (encrypted)
- `snmp_community`: SNMP community string (encrypted, optional)
- `snmp_version`: SNMP version (optional)
- `status`: active, inactive, or maintenance
- `health_status`: healthy, unhealthy, or unknown

### Backup Storage
Backups are stored in: `storage/app/backups/olts/{olt_id}/`

Filename format: `olt_{olt_id}_backup_{timestamp}.cfg`

### OltBackup Model
- `olt_id`: Foreign key to olts table
- `file_path`: Relative path to backup file
- `file_size`: File size in bytes
- `backup_type`: auto or manual
- `created_at`: Backup creation timestamp

## Extending the Service

### Adding Vendor-Specific Commands

The service uses a vendor-agnostic approach. To add vendor-specific implementations:

1. Create a vendor-specific command mapper:
```php
private function getVendorCommands(string $vendor): array
{
    return match($vendor) {
        'huawei' => [
            'show_onus' => 'display gpon onu state',
            'show_version' => 'display version',
        ],
        'zte' => [
            'show_onus' => 'show gpon onu state',
            'show_version' => 'show version',
        ],
        default => [
            'show_onus' => 'show gpon onu state',
            'show_version' => 'show version',
        ],
    };
}
```

2. Update the service to detect OLT vendor from model field
3. Use appropriate commands based on vendor

### Adding SNMP Support

For OLTs that use SNMP instead of SSH:

1. Install SNMP extension: `composer require phpsnmp/phpsnmp`
2. Create SNMP connection method in service
3. Implement SNMP-based operations

## Testing

The service includes comprehensive unit tests:

```bash
# Run OLT service tests
php artisan test --filter OltServiceTest

# Run with coverage
php artisan test --filter OltServiceTest --coverage
```

### Test Coverage
- Connection management (connect, disconnect, test)
- ONU discovery and synchronization
- ONU operations (authorize, unauthorize, reboot)
- Backup creation and management
- Statistics retrieval
- Error handling scenarios

## Error Handling

The service implements comprehensive error handling:

- All methods return appropriate types (bool, array, null)
- Errors are logged to Laravel log
- Database transactions ensure data consistency
- Connection failures are gracefully handled
- Invalid OLT configurations are validated before operations

## Security Considerations

1. **Encrypted Credentials**: Username, password, and SNMP community strings are encrypted in database
2. **Tenant Isolation**: Multi-tenant support with tenant_id filtering
3. **SSH Security**: Uses phpseclib3 for secure SSH connections
4. **Connection Cleanup**: Automatic connection cleanup on service destruction
5. **File Permissions**: Backup files have restricted permissions

## Performance Tips

1. **Scoped Service**: The service is scoped per request to avoid connection leaks
2. **Connection Pooling**: Reuses connections within a request
3. **Batch Operations**: Use bulk sync instead of individual ONU operations
4. **Scheduled Tasks**: Offload heavy operations to background tasks
5. **Caching**: Consider caching OLT statistics for frequently accessed data

## Troubleshooting

### Connection Failures
- Verify OLT IP address and port
- Check firewall rules
- Verify credentials
- Test SSH access manually: `ssh admin@192.168.1.1 -p 22`

### ONU Discovery Issues
- Verify OLT vendor and model
- Check command syntax for specific vendor
- Review Laravel logs for error details
- Test commands manually via SSH

### Backup Failures
- Check storage permissions: `storage/app/backups/olts/`
- Verify disk space
- Check OLT supports configuration export
- Review Laravel logs for errors

## Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Review OLT vendor documentation
3. Test commands manually via SSH
4. Check GitHub issues

## Future Enhancements

Completed features:
- [x] Web UI for OLT management
- [x] Real-time ONU monitoring dashboard
- [x] Automated ONU provisioning
- [x] Configuration templates
- [ ] Multi-vendor auto-detection
- [x] SNMP trap handling
- [x] Performance metrics visualization
- [x] Automated firmware updates
- [x] Bulk ONU operations
- [ ] Configuration comparison and rollback

### API Endpoints

All OLT management features are accessible via REST API:

#### OLT Management
- `GET /api/v1/olt/` - List all OLTs with statistics
- `GET /api/v1/olt/{id}` - Get OLT details
- `POST /api/v1/olt/{id}/test-connection` - Test OLT connection
- `POST /api/v1/olt/{id}/sync-onus` - Sync ONUs from OLT
- `GET /api/v1/olt/{id}/statistics` - Get OLT statistics
- `POST /api/v1/olt/{id}/backup` - Create configuration backup
- `GET /api/v1/olt/{id}/backups` - List backups
- `GET /api/v1/olt/{id}/port-utilization` - Get port utilization
- `GET /api/v1/olt/{id}/bandwidth-usage` - Get bandwidth usage
- `GET /api/v1/olt/{id}/monitor-onus` - Real-time ONU monitoring

#### ONU Operations
- `GET /api/v1/olt/onu/{onuId}` - Get ONU details
- `POST /api/v1/olt/onu/{onuId}/refresh` - Refresh ONU status
- `POST /api/v1/olt/onu/{onuId}/authorize` - Authorize ONU
- `POST /api/v1/olt/onu/{onuId}/unauthorize` - Unauthorize ONU
- `POST /api/v1/olt/onu/{onuId}/reboot` - Reboot ONU
- `POST /api/v1/olt/onu/bulk-operations` - Bulk ONU operations

### Web UI Views

#### Admin Panel
- `/panel/admin/olt/dashboard` - Main OLT management dashboard
- `/panel/admin/olt/{id}/monitor` - Real-time ONU monitoring
- `/panel/admin/olt/{id}/performance` - Performance metrics visualization
- `/panel/admin/olt/templates` - Configuration template management
- `/panel/admin/olt/snmp-traps` - SNMP trap viewer
- `/panel/admin/olt/firmware` - Firmware update management
- `/panel/admin/olt/backups` - Backup scheduling and management

### Features Description

#### 1. Web UI for OLT Management
Comprehensive web interface for managing OLTs with:
- Dashboard showing all OLTs with health status
- Real-time statistics and metrics
- Quick actions for sync, backup, and monitoring

#### 2. Real-time ONU Monitoring Dashboard
Interactive monitoring interface featuring:
- Live ONU status updates every 15 seconds
- Signal quality indicators with color coding
- Filtering by status, signal quality, and search
- Individual ONU operations (refresh, authorize, reboot)
- Bulk operations for multiple ONUs

#### 3. Automated ONU Provisioning
Backend support for automated ONU provisioning with:
- API endpoints for authorize/unauthorize operations
- Integration with OLT service layer
- Support for various ONU operations

#### 4. Configuration Templates
Template management system with:
- Create/edit/delete configuration templates
- Vendor-specific templates (Huawei, ZTE, Fiberhome, Nokia)
- Variable substitution support ({{variable_name}})
- Active/inactive template status

#### 5. SNMP Trap Handling
SNMP trap monitoring and management:
- Real-time trap notifications from OLT devices
- Severity-based filtering (critical, error, warning, info)
- Acknowledgement workflow
- Detailed trap information display
- Database storage for trap history

#### 6. Performance Metrics Visualization
Comprehensive performance monitoring:
- CPU, memory, and temperature tracking
- Bandwidth usage visualization
- PON port utilization monitoring
- ONU status distribution charts
- Time-range selection (1h, 6h, 24h, 7d, 30d)

#### 7. Automated Firmware Updates
Firmware update tracking system:
- Update status tracking (pending, uploading, installing, completed, failed)
- Progress monitoring
- Version history
- Error logging

#### 8. Bulk ONU Operations
Efficient bulk operations:
- Select multiple ONUs
- Authorize/unauthorize/reboot/refresh in bulk
- Operation status reporting
- Result tracking per ONU

#### 9. Firmware Update Management
Complete firmware management system:
- Upload firmware files for OLTs
- Version tracking (current and previous)
- Progress monitoring with percentage updates
- Status tracking (pending, uploading, installing, completed, failed)
- Error logging and retry functionality
- Manual and scheduled updates

#### 10. Backup Scheduling & Management
Comprehensive backup solution:
- Schedule automated backups (daily, weekly, monthly)
- Manual backup creation on-demand
- Backup history with size tracking
- Download and restore capabilities
- Backup comparison (configuration diff)
- Storage management and organization

### Database Schema

#### olt_configuration_templates
- id, tenant_id, name, vendor, model, description
- template_content, variables (JSON), is_active
- timestamps

#### olt_snmp_traps
- id, tenant_id, olt_id, source_ip, trap_type, oid
- severity, message, trap_data (JSON)
- is_acknowledged, acknowledged_at, acknowledged_by
- timestamps

#### olt_firmware_updates
- id, tenant_id, olt_id, firmware_version, previous_version
- file_path, status, progress, error_message
- started_at, completed_at, initiated_by
- timestamps

#### olt_performance_metrics
- id, tenant_id, olt_id
- cpu_usage, memory_usage, temperature
- bandwidth_rx, bandwidth_tx
- total_onus, online_onus, offline_onus
- port_utilization (JSON)
- timestamps

Additional enhancements planned:
- [ ] WebSocket for real-time updates
- [ ] Alert notifications system
- [ ] Automated health monitoring
- [ ] Backup scheduling UI
- [ ] ONU signal quality tracking
- [ ] Bandwidth utilization charts
- [ ] OLT performance reports
- [ ] Configuration audit logs
- [ ] User activity tracking
- [ ] Comprehensive API documentation

## License

This service is part of the ISP Solution project and follows the project's license.
