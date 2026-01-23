# Feature Request Summary

This document outlines the feature requests mentioned in the issue that require significant development work and are beyond the scope of bug fixes.

## Feature Requests Not Implemented (Require New Development)

### 1. SMS Gateway Management
**Request**: "There is no way to setup SMS gateway. Adding SMS gateway must be under SMS Management menu"

**Required Work**:
- Create SMS Gateway configuration controller
- Add SMS provider integration (Twilio, Nexmo, etc.)
- Create database table for SMS gateways
- Add UI for SMS gateway setup under SMS Management menu
- Implement SMS template management
- Add SMS sending functionality

**Estimated Effort**: Medium to Large feature

---

### 2. Package-Profile-IP Pool Mapping
**Request**: "There is no way to map Packages with PPP Profile, also there is no way to map PPP profiles with IP Pools."

**Current State**: 
- `PackageProfileMapping` model exists
- Migration `2026_01_23_050005_add_ip_pool_id_to_package_profile_mappings_table.php` exists

**Required Work**:
- Create UI for managing package-to-profile mappings
- Create UI for profile-to-IP-pool assignments
- Add validation and conflict resolution
- Update package creation/edit forms to include mapping options

**Estimated Effort**: Medium feature

---

### 3. Operator-Specific Package Management
**Request**: "There is no way to Allow different packages to different Operators"

**Current State**:
- Packages table has `operator_id` field
- Migration `2026_01_23_050000_add_operator_specific_fields_to_packages_table.php` exists

**Required Work**:
- Create operator package assignment UI
- Add package visibility controls per operator
- Update package listing to filter by operator permissions
- Add bulk assignment tools

**Estimated Effort**: Medium feature

---

### 4. Operator Custom Package Rates
**Request**: "There is no way to Allow different / custom package rates to different Operators"

**Current State**:
- `OperatorPackageRate` model exists
- Database structure supports this

**Required Work**:
- Create UI for setting custom rates per operator
- Add rate override management interface
- Update billing calculations to use custom rates
- Add audit logging for rate changes

**Estimated Effort**: Medium feature

---

### 5. Operator-Specific Billing Profiles
**Request**: "There is no way to Allow different billing profile, billing cycle to different operator."

**Current State**:
- User table has operator billing fields
- Migration `2026_01_23_050002_add_operator_billing_fields_to_users_table.php` exists

**Required Work**:
- Create billing profile management UI
- Add billing cycle configuration per operator
- Implement custom billing logic
- Add invoice generation with custom cycles

**Estimated Effort**: Large feature

---

### 6. Operator Wallet Management
**Request**: "There is no way to Allow to manually add fund to operators."

**Current State**:
- `OperatorWalletTransaction` model exists
- Migration `2026_01_23_050003_create_operator_wallet_transactions_table.php` exists

**Required Work**:
- Create wallet balance management UI
- Add manual fund addition interface
- Implement transaction history viewer
- Add wallet balance validation
- Create wallet reports and statements

**Estimated Effort**: Medium feature

---

### 7. Operator Payment Type Configuration
**Request**: "There is no way to set Operators payment type to prepaid or post paid."

**Required Work**:
- Add payment_type field to operators
- Create UI for selecting prepaid/postpaid
- Implement different billing logic for each type
- Add validation and restrictions based on payment type
- Update invoicing to handle both types

**Estimated Effort**: Medium feature

---

### 8. SMS Fee Configuration
**Request**: "There is no way to set who cover operators sms fees and how much each sms cost"

**Current State**:
- `OperatorSmsRate` model exists

**Required Work**:
- Create SMS cost configuration UI
- Add per-operator SMS rate settings
- Implement SMS fee calculation
- Add SMS cost to operator bills
- Create SMS usage reports

**Estimated Effort**: Medium feature

---

### 9. Admin Operator Impersonation
**Request**: "There is no way to login to Operators account by admin by clicking login"

**Current State**:
- Route exists: `Route::post('/operators/{operatorId}/login-as', [AdminController::class, 'loginAsOperator'])`
- Method exists in AdminController

**Status**: ✅ **Partially Implemented**

**Required Work** (if not fully functional):
- Verify impersonation session handling
- Add UI button for "Login As" in operator list
- Test and ensure proper permission checks
- Add ability to return to admin account

**Estimated Effort**: Small fix (if basic implementation exists)

---

### 10. Missing Functionality Issues
**Request**: "There is lots of button not working at all, looks like you never develop and design before"

**Response**: This is too vague to address specifically. Each non-working button needs to be:
1. Identified by page and location
2. Expected functionality documented
3. Error logs captured
4. Filed as individual bug reports

**Recommendation**: 
- Conduct thorough UI testing
- Create individual tickets for each broken feature
- Provide specific reproduction steps
- Include expected vs actual behavior

---

### 11. Demo Customer Location Issue
**Request**: "Demo Customer appears under user, customer must be at Customers menu-- All Customers /panel/admin/customers"

**Required Work**:
- Review customer/user data model
- Identify why demo customer is misplaced
- Update navigation or data categorization
- Ensure proper role-based filtering

**Estimated Effort**: Small fix

---

### 12. Duplicate Menu Items
**Request**: "Network Device, Network, OLT management and settings /panel/admin/settings show repeated submenu for same function"

**Required Work**:
- Audit navigation menu structure
- Identify duplicate entries
- Consolidate menu items
- Update blade templates for navigation

**Estimated Effort**: Small fix

---

## Recommended Approach

### Phase 1: Critical Bugs (COMPLETED in this PR)
- ✅ Database column errors
- ✅ Routing errors
- ✅ Template syntax errors

### Phase 2: Quick Wins
1. Fix Admin operator impersonation (if needed)
2. Fix demo customer placement
3. Remove duplicate menu items

### Phase 3: Core Features
1. SMS Gateway Management
2. Package-Profile-IP Pool Mapping UI
3. Operator Wallet Management

### Phase 4: Advanced Features
1. Operator-specific package rates
2. Custom billing profiles
3. SMS fee configuration
4. Prepaid/Postpaid operator types

## Development Recommendations

1. **Create Individual Stories**: Each feature should be a separate story in your project management system
2. **Prioritize**: Work with stakeholders to prioritize features
3. **Estimate**: Get proper estimates for each feature
4. **Test**: Create comprehensive tests for new features
5. **Document**: Update user documentation as features are added
6. **Incremental**: Release features incrementally, not all at once

## Notes

- The database structure for many of these features already exists (migrations created)
- The models for these features exist
- What's missing is primarily the UI layer and business logic
- This indicates good planning but incomplete implementation
- A systematic approach to completing these features is recommended

---

**Date**: 2026-01-23
**Status**: Feature requests documented
**Action Required**: Product owner prioritization and sprint planning
