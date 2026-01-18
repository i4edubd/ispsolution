# API Documentation

**Version:** 2.0  
**Base URL:** `/api`  
**Format:** JSON  
**Authentication:** Laravel Sanctum (Token-based)

## Table of Contents

1. [Authentication](#authentication)
2. [Data API](#data-api)
3. [Chart API](#chart-api)
4. [IPAM Endpoints](#ipam-endpoints)
5. [RADIUS Endpoints](#radius-endpoints)
6. [MikroTik Endpoints](#mikrotik-endpoints)
7. [Network Users Endpoints](#network-users-endpoints)
8. [OLT API](#olt-api)
9. [Monitoring API](#monitoring-api)
10. [Error Handling](#error-handling)
11. [Rate Limiting](#rate-limiting)
12. [Pagination](#pagination)

---

## Authentication

All API endpoints require authentication via Laravel Sanctum tokens.

### Headers Required
```
Authorization: Bearer {your-token}
Content-Type: application/json
Accept: application/json
```

### Token Generation
```php
// Generate API token for user
$token = $user->createToken('api-token')->plainTextToken;
```

---

## Data API

### Get Users
**Endpoint:** `GET /api/data/users`

**Description:** Retrieve paginated list of users with optional filtering.

**Query Parameters:**
- `search` (string, optional): Search by name, email, or username
- `role` (string, optional): Filter by role name
- `status` (string, optional): Filter by status ('active' or 'inactive')
- `per_page` (integer, optional): Results per page (default: 20, max: 100)
- `page` (integer, optional): Page number

**Response:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "username": "johndoe",
      "is_active": true,
      "role": {
        "id": 2,
        "name": "admin"
      },
      "created_at": "2026-01-15T10:00:00.000000Z"
    }
  ],
  "total": 100,
  "per_page": 20
}
```

---

### Get Network Users
**Endpoint:** `GET /api/data/network-users`

**Description:** Retrieve network users (PPPoE, Hotspot, etc.)

**Query Parameters:**
- `search` (string, optional): Search by username or IP address
- `status` (string, optional): Filter by status
- `per_page` (integer, optional): Results per page

**Response:** Paginated network users with package details

---

### Get Invoices
**Endpoint:** `GET /api/data/invoices`

**Description:** Retrieve invoices with filtering options

**Query Parameters:**
- `search` (string, optional): Search by invoice number or user name
- `status` (string, optional): Filter by status (pending, paid, overdue)
- `from_date` (date, optional): Filter from date (Y-m-d format)
- `to_date` (date, optional): Filter to date (Y-m-d format)
- `per_page` (integer, optional): Results per page

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "invoice_number": "INV-20260115-00001",
      "user": {
        "id": 5,
        "name": "Jane Smith"
      },
      "amount": 1500.00,
      "tax_amount": 150.00,
      "total_amount": 1650.00,
      "status": "pending",
      "due_date": "2026-02-15",
      "created_at": "2026-01-15T10:00:00.000000Z"
    }
  ]
}
```

---

### Get Payments
**Endpoint:** `GET /api/data/payments`

**Description:** Retrieve payment records

**Query Parameters:**
- `search` (string, optional): Search by payment number, transaction ID, or user
- `method` (string, optional): Filter by payment method
- `status` (string, optional): Filter by status
- `from_date` (date, optional): Filter from date
- `to_date` (date, optional): Filter to date
- `per_page` (integer, optional): Results per page

---

### Get Dashboard Stats
**Endpoint:** `GET /api/data/dashboard-stats`

**Description:** Get comprehensive dashboard statistics

**Response:**
```json
{
  "users": {
    "total": 1250,
    "active": 1180
  },
  "invoices": {
    "total": 5432,
    "pending": 234,
    "overdue": 45,
    "paid": 5153
  },
  "revenue": {
    "today": 15000.00,
    "this_month": 450000.00,
    "this_year": 5400000.00
  },
  "network_users": {
    "total": 980,
    "active": 856,
    "suspended": 124
  }
}
```

---

### Get Recent Activities
**Endpoint:** `GET /api/data/recent-activities`

**Description:** Get recent system activities

**Query Parameters:**
- `limit` (integer, optional): Number of activities to return (default: 10)

**Response:**
```json
[
  {
    "type": "payment",
    "message": "Payment received from John Doe",
    "amount": 1500.00,
    "timestamp": "2026-01-15T10:30:00.000000Z"
  },
  {
    "type": "invoice",
    "message": "Invoice generated for Jane Smith",
    "amount": 2000.00,
    "timestamp": "2026-01-15T10:25:00.000000Z"
  }
]
```

---

## Chart API

### Get Revenue Chart
**Endpoint:** `GET /api/charts/revenue`

**Description:** Get monthly revenue data for the year

**Query Parameters:**
- `year` (integer, optional): Year to retrieve data for (default: current year)

**Response:**
```json
{
  "categories": ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
  "series": [
    {
      "name": "Revenue",
      "data": [45000, 52000, 48000, 55000, 60000, 58000, 62000, 65000, 63000, 70000, 75000, 80000]
    }
  ]
}
```

---

### Get Invoice Status Chart
**Endpoint:** `GET /api/charts/invoice-status`

**Description:** Get invoice distribution by status

**Response:**
```json
{
  "labels": ["Pending", "Overdue", "Paid"],
  "series": [234, 45, 5153]
}
```

---

### Get User Growth Chart
**Endpoint:** `GET /api/charts/user-growth`

**Description:** Get user growth over time

**Query Parameters:**
- `months` (integer, optional): Number of months to show (default: 12)

**Response:**
```json
{
  "categories": ["Jan 2025", "Feb 2025", "Mar 2025", ...],
  "series": [
    {
      "name": "Total Users",
      "data": [850, 920, 985, 1050, 1120, 1180, 1250]
    }
  ]
}
```

---

### Get Payment Method Chart
**Endpoint:** `GET /api/charts/payment-methods`

**Description:** Get payment distribution by method

**Response:**
```json
{
  "labels": ["Cash", "Bkash", "Nagad", "Bank Transfer", "Stripe"],
  "series": [125000, 85000, 72000, 45000, 28000]
}
```

---

### Get Dashboard Charts
**Endpoint:** `GET /api/charts/dashboard`

**Description:** Get all dashboard charts in one request

**Response:** Combined response of revenue, invoice status, user growth, and payment methods charts

---

## IPAM Endpoints

### IP Pools

#### List All IP Pools

```http
GET /api/v1/ipam/pools
```

**Query Parameters:**
- `status` (optional): Filter by status (`active`, `inactive`)
- `per_page` (optional): Results per page (default: 15)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Main Pool",
      "description": "Primary IP pool",
      "start_ip": "192.168.1.1",
      "end_ip": "192.168.1.254",
      "gateway": "192.168.1.1",
      "dns_servers": "8.8.8.8,8.8.4.4",
      "vlan_id": 100,
      "status": "active",
      "created_at": "2026-01-15T10:00:00.000000Z",
      "updated_at": "2026-01-15T10:00:00.000000Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### Create IP Pool

```http
POST /api/v1/ipam/pools
```

**Request Body:**
```json
{
  "name": "Main Pool",
  "description": "Primary IP pool",
  "start_ip": "192.168.1.1",
  "end_ip": "192.168.1.254",
  "gateway": "192.168.1.1",
  "dns_servers": "8.8.8.8,8.8.4.4",
  "vlan_id": 100,
  "status": "active"
}
```

**Response (201):**
```json
{
  "message": "IP pool created successfully",
  "data": {
    "id": 1,
    "name": "Main Pool",
    ...
  }
}
```

#### Get IP Pool

```http
GET /api/v1/ipam/pools/{id}
```

#### Update IP Pool

```http
PUT /api/v1/ipam/pools/{id}
```

#### Delete IP Pool

```http
DELETE /api/v1/ipam/pools/{id}
```

**Note:** Cannot delete pools with active allocations.

#### Get Pool Utilization

```http
GET /api/v1/ipam/pools/{id}/utilization
```

**Response:**
```json
{
  "pool": {...},
  "utilization": {
    "total_ips": 254,
    "allocated_ips": 120,
    "available_ips": 134,
    "utilization_percentage": 47.24
  }
}
```

### IP Subnets

#### List All Subnets

```http
GET /api/v1/ipam/subnets
```

**Query Parameters:**
- `pool_id` (optional): Filter by pool ID
- `status` (optional): Filter by status
- `per_page` (optional): Results per page

#### Create Subnet

```http
POST /api/v1/ipam/subnets
```

**Request Body:**
```json
{
  "pool_id": 1,
  "network": "192.168.1.0",
  "prefix_length": 24,
  "gateway": "192.168.1.1",
  "vlan_id": 100,
  "status": "active"
}
```

**Response (400):** If subnet overlaps
```json
{
  "message": "Subnet overlaps with existing subnet"
}
```

#### Get Available IPs in Subnet

```http
GET /api/v1/ipam/subnets/{id}/available-ips
```

**Response:**
```json
{
  "subnet": {...},
  "available_ips": ["192.168.1.2", "192.168.1.3", ...],
  "count": 134
}
```

### IP Allocations

#### List All Allocations

```http
GET /api/v1/ipam/allocations
```

**Query Parameters:**
- `subnet_id` (optional): Filter by subnet
- `status` (optional): Filter by status (`allocated`, `released`)
- `username` (optional): Filter by username

#### Allocate IP Address

```http
POST /api/v1/ipam/allocations
```

**Request Body:**
```json
{
  "subnet_id": 1,
  "mac_address": "00:11:22:33:44:55",
  "username": "user@example.com"
}
```

**Response (201):**
```json
{
  "message": "IP address allocated successfully",
  "data": {
    "id": 1,
    "subnet_id": 1,
    "ip_address": "192.168.1.10",
    "mac_address": "00:11:22:33:44:55",
    "username": "user@example.com",
    "allocated_at": "2026-01-15T10:00:00.000000Z",
    "status": "allocated"
  }
}
```

**Response (400):** If subnet is full
```json
{
  "message": "Failed to allocate IP address. Subnet may be full or inactive."
}
```

#### Release IP Address

```http
DELETE /api/v1/ipam/allocations/{id}
```

**Response:**
```json
{
  "message": "IP address released successfully"
}
```

---

## RADIUS Endpoints

### Authentication

#### Authenticate User

```http
POST /api/v1/radius/authenticate
```

**Request Body:**
```json
{
  "username": "testuser",
  "password": "password123"
}
```

**Response (200):** Success
```json
{
  "message": "Authentication successful",
  "authenticated": true,
  "attributes": {
    "Framed-IP-Address": "192.168.1.10",
    "Session-Timeout": 3600
  }
}
```

**Response (401):** Failure
```json
{
  "message": "Authentication failed",
  "authenticated": false
}
```

### Accounting

#### Start Accounting Session

```http
POST /api/v1/radius/accounting/start
```

**Request Body:**
```json
{
  "username": "testuser",
  "session_id": "sess_123456",
  "nas_ip_address": "192.168.88.1",
  "nas_port_id": "pppoe-1",
  "framed_ip_address": "10.0.0.5",
  "calling_station_id": "00:11:22:33:44:55",
  "called_station_id": "ISP-PPPoE"
}
```

**Response (201):**
```json
{
  "message": "Accounting session started successfully"
}
```

#### Update Accounting Session

```http
POST /api/v1/radius/accounting/update
```

**Request Body:**
```json
{
  "session_id": "sess_123456",
  "username": "testuser",
  "session_time": 3600,
  "input_octets": 1048576,
  "output_octets": 2097152
}
```

#### Stop Accounting Session

```http
POST /api/v1/radius/accounting/stop
```

**Request Body:**
```json
{
  "session_id": "sess_123456",
  "username": "testuser",
  "session_time": 7200,
  "input_octets": 5242880,
  "output_octets": 10485760,
  "terminate_cause": "User-Request"
}
```

### User Management

#### Create RADIUS User

```http
POST /api/v1/radius/users
```

**Request Body:**
```json
{
  "username": "newuser",
  "password": "password123",
  "attributes": {
    "Framed-IP-Address": "192.168.1.10",
    "Session-Timeout": 3600
  }
}
```

#### Update RADIUS User

```http
PUT /api/v1/radius/users/{username}
```

**Request Body:**
```json
{
  "password": "newpassword123",
  "attributes": {
    "Session-Timeout": 7200
  }
}
```

#### Delete RADIUS User

```http
DELETE /api/v1/radius/users/{username}
```

#### Sync User to RADIUS

```http
POST /api/v1/radius/users/{username}/sync
```

**Request Body:**
```json
{
  "password": "password123"
}
```

### Statistics

#### Get User Statistics

```http
GET /api/v1/radius/users/{username}/stats
```

**Response:**
```json
{
  "username": "testuser",
  "stats": {
    "total_sessions": 45,
    "total_duration": 162000,
    "total_input": 524288000,
    "total_output": 1048576000,
    "last_session": "2026-01-15T10:00:00.000000Z"
  }
}
```

---

## MikroTik Endpoints

### Router Management

#### List All Routers

```http
GET /api/v1/mikrotik/routers
```

**Query Parameters:**
- `status` (optional): Filter by status
- `per_page` (optional): Results per page

#### Connect to Router

```http
POST /api/v1/mikrotik/routers/{id}/connect
```

**Response:**
```json
{
  "message": "Connected to router successfully"
}
```

#### Check Router Health

```http
GET /api/v1/mikrotik/routers/{id}/health
```

**Response:**
```json
{
  "router": {...},
  "healthy": true,
  "checked_at": "2026-01-15T10:00:00.000000Z"
}
```

### PPPoE Users

#### List PPPoE Users

```http
GET /api/v1/mikrotik/pppoe-users
```

**Query Parameters:**
- `router_id` (optional): Filter by router
- `status` (optional): Filter by status

#### Create PPPoE User

```http
POST /api/v1/mikrotik/pppoe-users
```

**Request Body:**
```json
{
  "router_id": 1,
  "username": "pppoe_user",
  "password": "password123",
  "service": "pppoe",
  "profile": "default",
  "local_address": "10.0.0.1",
  "remote_address": "10.0.0.2",
  "status": "active"
}
```

#### Update PPPoE User

```http
PUT /api/v1/mikrotik/pppoe-users/{username}
```

#### Delete PPPoE User

```http
DELETE /api/v1/mikrotik/pppoe-users/{username}
```

### Sessions

#### List Active Sessions

```http
GET /api/v1/mikrotik/sessions?router_id=1
```

**Response:**
```json
{
  "router_id": 1,
  "sessions": [
    {
      "id": "session_123",
      "username": "testuser",
      "address": "10.0.0.5",
      "uptime": 3600,
      "bytes-in": 1048576,
      "bytes-out": 2097152
    }
  ],
  "count": 1
}
```

#### Disconnect Session

```http
DELETE /api/v1/mikrotik/sessions/{id}
```

**Response:**
```json
{
  "message": "Session disconnected successfully"
}
```

### Profiles

#### List PPPoE Profiles

```http
GET /api/v1/mikrotik/profiles?router_id=1
```

**Response:**
```json
{
  "router_id": 1,
  "profiles": [
    {
      "name": "default",
      "local-address": "10.0.0.1",
      "remote-address": "pool1",
      "rate-limit": "10M/10M"
    }
  ],
  "count": 1
}
```

---

## Network Users Endpoints

### User Management

#### List Network Users

```http
GET /api/v1/network-users
```

**Query Parameters:**
- `service_type` (optional): Filter by type (`pppoe`, `hotspot`, `static_ip`)
- `status` (optional): Filter by status
- `search` (optional): Search username or email
- `per_page` (optional): Results per page

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "username": "user001",
      "email": "user@example.com",
      "service_type": "pppoe",
      "package_id": 1,
      "status": "active",
      "created_at": "2026-01-15T10:00:00.000000Z",
      "package": {...}
    }
  ]
}
```

#### Create Network User

```http
POST /api/v1/network-users
```

**Request Body:**
```json
{
  "username": "user001",
  "password": "password123",
  "email": "user@example.com",
  "service_type": "pppoe",
  "package_id": 1,
  "status": "active"
}
```

**Note:** Automatically syncs to RADIUS upon creation.

#### Get Network User

```http
GET /api/v1/network-users/{id}
```

**Response:**
```json
{
  "id": 1,
  "username": "user001",
  "email": "user@example.com",
  "service_type": "pppoe",
  "package": {...},
  "ip_allocations": [...],
  "sessions": [...]
}
```

#### Update Network User

```http
PUT /api/v1/network-users/{id}
```

**Request Body:**
```json
{
  "email": "newemail@example.com",
  "status": "suspended",
  "package_id": 2
}
```

#### Delete Network User

```http
DELETE /api/v1/network-users/{id}
```

**Note:** Also removes user from RADIUS database.

#### Sync User to RADIUS

```http
POST /api/v1/network-users/{id}/sync-radius
```

**Request Body:**
```json
{
  "password": "newpassword"
}
```

---

## OLT API

### List OLTs
**Endpoint:** `GET /api/v1/olt/devices`

**Description:** List all OLT devices with status

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "OLT-Main",
      "ip_address": "192.168.1.100",
      "vendor": "Huawei",
      "model": "MA5608T",
      "status": "active",
      "total_onus": 24,
      "online_onus": 22
    }
  ]
}
```

---

### Get ONU Status
**Endpoint:** `GET /api/v1/olt/devices/{id}/onus`

**Description:** Get status of all ONUs on an OLT

**Response:**
```json
{
  "olt_id": 1,
  "onus": [
    {
      "id": 1,
      "serial_number": "HWTC12345678",
      "status": "online",
      "rx_power": -18.5,
      "distance": 450,
      "description": "Customer ABC"
    }
  ]
}
```

---

### Provision ONU
**Endpoint:** `POST /api/v1/olt/devices/{id}/onus`

**Description:** Provision a new ONU on an OLT

**Request Body:**
```json
{
  "serial_number": "HWTC12345678",
  "description": "Customer ABC",
  "profile": "profile_100M",
  "vlan": 100
}
```

---

## Monitoring API

### Get Device Status
**Endpoint:** `GET /api/v1/monitoring/devices/{id}/status`

**Description:** Get real-time status of a network device

**Response:**
```json
{
  "device_id": 1,
  "status": "online",
  "uptime": 864000,
  "cpu_usage": 35.5,
  "memory_usage": 62.3,
  "temperature": 45.0,
  "last_checked": "2026-01-18T10:00:00.000000Z"
}
```

---

### Get Bandwidth Usage
**Endpoint:** `GET /api/v1/monitoring/devices/{id}/bandwidth`

**Description:** Get bandwidth usage statistics

**Query Parameters:**
- `from` (datetime): Start time
- `to` (datetime): End time
- `interval` (string): Aggregation interval (5min, hour, day)

**Response:**
```json
{
  "device_id": 1,
  "interval": "hour",
  "data": [
    {
      "timestamp": "2026-01-18T10:00:00Z",
      "rx_bytes": 1048576000,
      "tx_bytes": 524288000,
      "rx_rate": 10485760,
      "tx_rate": 5242880
    }
  ]
}
```

---

## Error Handling

### HTTP Status Codes

- `200 OK` - Request successful
- `201 Created` - Resource created
- `400 Bad Request` - Invalid request or business logic error
- `401 Unauthorized` - Authentication failed
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation error
- `500 Internal Server Error` - Server error

### Error Response Format

```json
{
  "message": "Error description",
  "errors": {
    "field_name": [
      "Validation error message"
    ]
  }
}
```

### Common Error Examples

**Validation Error (422):**
```json
{
  "message": "Validation failed",
  "errors": {
    "username": ["The username field is required."],
    "password": ["The password must be at least 6 characters."]
  }
}
```

**Resource Not Found (404):**
```json
{
  "message": "No query results for model [App\\Models\\IpPool] 999"
}
```

**Business Logic Error (400):**
```json
{
  "message": "Cannot delete pool with active allocations"
}
```

---

## Rate Limiting

API endpoints are subject to rate limiting. Current limits:
- **60 requests per minute** per IP address
- Rate limit headers included in response:
  - `X-RateLimit-Limit`
  - `X-RateLimit-Remaining`
  - `X-RateLimit-Reset`

---

## Pagination

List endpoints support pagination with the following parameters:
- `per_page` - Results per page (default: 15, max: 100)
- `page` - Page number (default: 1)

Pagination meta data included in response:
```json
{
  "data": [...],
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "per_page": 15,
    "to": 15,
    "total": 150
  }
}
```

---

## Examples

### Complete Workflow: Create User with IP Allocation

1. **Create IP Pool:**
```bash
curl -X POST http://localhost:8000/api/v1/ipam/pools \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Customer Pool",
    "start_ip": "10.0.0.1",
    "end_ip": "10.0.0.254",
    "gateway": "10.0.0.1"
  }'
```

2. **Create Subnet:**
```bash
curl -X POST http://localhost:8000/api/v1/ipam/subnets \
  -H "Content-Type: application/json" \
  -d '{
    "pool_id": 1,
    "network": "10.0.0.0",
    "prefix_length": 24,
    "gateway": "10.0.0.1"
  }'
```

3. **Create Network User:**
```bash
curl -X POST http://localhost:8000/api/v1/network-users \
  -H "Content-Type: application/json" \
  -d '{
    "username": "customer001",
    "password": "secure123",
    "email": "customer@example.com",
    "service_type": "pppoe",
    "package_id": 1
  }'
```

4. **Allocate IP:**
```bash
curl -X POST http://localhost:8000/api/v1/ipam/allocations \
  -H "Content-Type: application/json" \
  -d '{
    "subnet_id": 1,
    "mac_address": "00:11:22:33:44:55",
    "username": "customer001"
  }'
```

5. **Create PPPoE User on MikroTik:**
```bash
curl -X POST http://localhost:8000/api/v1/mikrotik/pppoe-users \
  -H "Content-Type: application/json" \
  -d '{
    "router_id": 1,
    "username": "customer001",
    "password": "secure123",
    "profile": "default",
    "remote_address": "10.0.0.5"
  }'
```

---

## Support

For questions and issues:
- Review the [README.md](../README.md)
- Check [Network Services Guide](NETWORK_SERVICES.md)
- Open an issue on GitHub
