# Deprecated Documentation

This file lists documentation that has been consolidated, superseded, or is no longer maintained.

**Last Updated**: 2026-01-18

---

## Consolidated Documentation

The following files have been consolidated into comprehensive guides. They are marked for future removal after a transition period.

### Role System Documentation → `docs/ROLES_AND_PERMISSIONS.md`

The following files have been merged into the comprehensive **[Roles and Permissions Guide](docs/ROLES_AND_PERMISSIONS.md)**:

| Old File | Status | Notes |
|----------|--------|-------|
| **SUMMARY.md** | ⚠️ Deprecated | Core content merged into ROLES_AND_PERMISSIONS.md |
| **DATA_ISOLATION.md** | ⚠️ Deprecated | All data isolation rules moved to ROLES_AND_PERMISSIONS.md |
| **ROLE_SYSTEM_QUICK_REFERENCE.md** | ⚠️ Deprecated | Quick reference now part of ROLES_AND_PERMISSIONS.md |
| **docs/PR1_TENANCY_AND_ROLES.md** | ⚠️ Deprecated | Historical PR documentation, implementation details in ROLES_AND_PERMISSIONS.md |
| **docs/tenancy.md** | ⚠️ Deprecated | Multi-tenancy basics merged into ROLES_AND_PERMISSIONS.md |

**Migration Path**: Use `docs/ROLES_AND_PERMISSIONS.md` for all role, permission, and data isolation documentation.

---

### API Documentation → `docs/API.md`

The following files have been merged into the unified **[API Documentation](docs/API.md)**:

| Old File | Status | Notes |
|----------|--------|-------|
| **docs/API_DOCUMENTATION.md** | ⚠️ Deprecated | All content merged into docs/API.md |

**Migration Path**: Use `docs/API.md` as the single source of truth for all API documentation.

---

## Redundant Documentation

### Implementation Tracking Files

Multiple tracking files exist with overlapping content:

| File | Status | Notes |
|------|--------|-------|
| **TASK_COMPLETION_SUMMARY.md** | ℹ️ Archive Only | Historical task tracking |
| **COMPLETED_TASKS_SUMMARY.md** | ℹ️ Archive Only | Historical completion summary |
| **IMPLEMENTATION_SUMMARY.md** | ℹ️ Archive Only | Historical implementation notes |
| **IMPLEMENTATION_SUMMARY_PANELS.md** | ℹ️ Archive Only | Historical panel implementation |
| **BILLING_IMPLEMENTATION_SUMMARY.md** | ℹ️ Archive Only | Historical billing implementation |

**Current Status**: Reference **[IMPLEMENTATION_STATUS.md](IMPLEMENTATION_STATUS.md)** for current implementation tracking.

---

### Panel Documentation

Multiple panel-related files with overlapping content:

| File | Status | Notes |
|------|--------|-------|
| **PANEL_DEVELOPMENT_PROGRESS.md** | ℹ️ Reference | Development progress tracking |
| **PANEL_SCREENSHOTS_GUIDE.md** | ✅ Keep | Visual guide still relevant |
| **NAVIGATION_AND_SEARCH_IMPLEMENTATION.md** | ℹ️ Archive | Implementation-specific details |

**Current Reference**: Use **[PANELS_SPECIFICATION.md](PANELS_SPECIFICATION.md)** for panel specifications.

---

## Scheduled for Removal

The following files are scheduled for removal in future releases:

### Phase 1 (Next Release)
- `SUMMARY.md` - Content fully merged
- `DATA_ISOLATION.md` - Content fully merged
- `ROLE_SYSTEM_QUICK_REFERENCE.md` - Content fully merged
- `docs/API_DOCUMENTATION.md` - Content fully merged
- `docs/tenancy.md` - Content merged into ROLES_AND_PERMISSIONS.md

### Phase 2 (After Verification Period)
- `docs/PR1_TENANCY_AND_ROLES.md` - Historical PR documentation
- `TASK_COMPLETION_SUMMARY.md` - Historical tracking
- `COMPLETED_TASKS_SUMMARY.md` - Historical tracking
- `IMPLEMENTATION_SUMMARY.md` - Historical notes
- `IMPLEMENTATION_SUMMARY_PANELS.md` - Historical notes
- `BILLING_IMPLEMENTATION_SUMMARY.md` - Historical notes

---

## Migration Guide

### For Documentation Readers

**Old Reference** → **New Reference**

| Old | New |
|-----|-----|
| SUMMARY.md | docs/ROLES_AND_PERMISSIONS.md |
| DATA_ISOLATION.md | docs/ROLES_AND_PERMISSIONS.md |
| ROLE_SYSTEM_QUICK_REFERENCE.md | docs/ROLES_AND_PERMISSIONS.md |
| docs/API_DOCUMENTATION.md | docs/API.md |
| docs/tenancy.md | docs/ROLES_AND_PERMISSIONS.md (Tenancy section) |

### For Documentation Links

Update any links to deprecated files:

```markdown
<!-- Old -->
[Role System](SUMMARY.md)
[Data Isolation](DATA_ISOLATION.md)
[API Docs](docs/API_DOCUMENTATION.md)

<!-- New -->
[Role System](docs/ROLES_AND_PERMISSIONS.md)
[Data Isolation](docs/ROLES_AND_PERMISSIONS.md#data-isolation-rules)
[API Docs](docs/API.md)
```

### For Code Comments

Update code comments referencing old documentation:

```php
// Old
// See SUMMARY.md for role hierarchy

// New
// See docs/ROLES_AND_PERMISSIONS.md for role hierarchy
```

---

## Removed Files

Files that have been completely removed (not just deprecated):

| File | Removed Date | Reason |
|------|-------------|--------|
| *(None yet)* | - | - |

---

## Files Kept As-Is

The following files are **NOT** deprecated and should continue to be used:

### Core Documentation
- ✅ **README.md** - Main project documentation
- ✅ **CHANGELOG.md** - Version history
- ✅ **docs/INDEX.md** - Documentation index
- ✅ **docs/ROLES_AND_PERMISSIONS.md** - Role system guide
- ✅ **docs/API.md** - API documentation
- ✅ **docs/DEPLOYMENT.md** - Deployment guide
- ✅ **docs/TESTING.md** - Testing guide
- ✅ **docs/USER_GUIDES.md** - User guides
- ✅ **docs/developer-guide.md** - Developer guide

### Feature Documentation
- ✅ **TODO.md** - Current TODO list
- ✅ **TODO_FEATURES_A2Z.md** - Feature specifications
- ✅ **Feature.md** - Feature requests
- ✅ **PANELS_SPECIFICATION.md** - Panel specs
- ✅ **MULTI_TENANCY_ISOLATION.md** - Multi-tenancy overview
- ✅ **IMPLEMENTATION_STATUS.md** - Current status

### MikroTik Documentation
- ✅ **MIKROTIK_QUICKSTART.md** - Quick start guide
- ✅ **MIKROTIK_ADVANCED_FEATURES.md** - Advanced features

### Network Services
- ✅ **docs/NETWORK_SERVICES.md** - Network services guide
- ✅ **docs/OLT_SERVICE_GUIDE.md** - OLT guide
- ✅ **docs/OLT_API_REFERENCE.md** - OLT API reference
- ✅ **docs/MONITORING_SYSTEM.md** - Monitoring guide
- ✅ **docs/ROLE_BASED_MENU.md** - Menu system

---

## Deprecation Policy

### Criteria for Deprecation
1. Content is fully covered in another document
2. Information is outdated or no longer accurate
3. File causes confusion due to redundancy
4. Content has been superseded by better documentation

### Deprecation Process
1. **Mark as Deprecated** - Add to this file with ⚠️ status
2. **Add Redirect** - Add note at top of deprecated file pointing to new location
3. **Transition Period** - Keep file for at least one release cycle
4. **Archive** - Move to archive folder if needed for history
5. **Remove** - Remove file and update all references

### Status Indicators
- ⚠️ **Deprecated** - Scheduled for removal, use new location
- ℹ️ **Archive Only** - Historical reference, not actively maintained
- ✅ **Keep** - Current and actively maintained
- ❌ **Removed** - File has been deleted

---

## Questions?

If you have questions about deprecated documentation:
1. Check the **[Documentation Index](docs/INDEX.md)** for current docs
2. Review the migration guide above
3. Open an issue on GitHub if you need clarification

---

**Note**: Deprecated files will remain in the repository for at least one release cycle (approximately 3 months) to allow for smooth transition.
