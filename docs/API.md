# API Documentation

**Version:** 1.0  
**Base URL:** `/api/v1`  
**Format:** JSON

## Table of Contents

1. [Authentication](#authentication)
2. [IPAM Endpoints](#ipam-endpoints)
3. [RADIUS Endpoints](#radius-endpoints)
4. [MikroTik Endpoints](#mikrotik-endpoints)
5. [Network Users Endpoints](#network-users-endpoints)
6. [Error Handling](#error-handling)

---

## Authentication

Currently, the API does not require authentication. In production, implement Laravel Sanctum or Passport for API token authentication.

```bash
# Future authentication header
Authorization: Bearer YOUR_API_TOKEN
```

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
