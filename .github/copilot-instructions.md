# Copilot Instructions for SIM Kelurahan Batua

## Project Overview

This is a **Laravel 12** application for managing kelurahan (village) administration, including population data, document services (persuratan), business registrations (usaha/PK5), and reporting. The system is built with role-based access control and centers around NIK (national ID) as the primary key for all citizen data.

## Current Implementation Status (February 2026)

✅ **Completed**:
- Full admin panel with 8 controllers (Dashboard, User, Role, Penduduk, Keluarga, Wilayah, Penandatangan, Pegawai)
- Role-based dashboards for all 6 roles (Admin, Operator, Verifikator, Penandatangan, RT/RW, Warga)
- Complete test suite: 108 tests passing (9 test files covering admin module)
- Database seeding: 39 seeders working with realistic data
- 46 admin routes fully implemented
- Migration system with FK constraints

🚧 **In Progress / Pending**:
- Operator panel features (data entry, document processing)
- Verifikator features (document verification workflow)
- Penandatangan features (digital signing)
- RT/RW features (neighborhood data management)
- Warga portal (citizen self-service)
- Document template system
- Reporting modules

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+), MySQL (production), SQLite (testing)
- **Frontend**: Blade templates, Alpine.js, Tailwind CSS v4, DaisyUI v5
- **Build**: Vite with laravel-vite-plugin
- **Testing**: PHPUnit with RefreshDatabase trait
- **Components**: Custom Blade components in `resources/views/components/ui/`

## Architecture & Key Concepts

### 1. Role-Based Access Control

The system uses a strict role-based architecture with 6 distinct roles:

```php
// app/Models/Role.php constants
Role::ADMIN         → Full system access
Role::OPERATOR      → Data entry, document processing
Role::VERIFIKATOR   → Document verification & approval
Role::PENANDATANGAN → Document signing (Lurah)
Role::RT_RW         → Neighborhood data management
Role::WARGA         → Citizen portal (optional)
```

**Pattern**: Each role has a dedicated dashboard controller under `app/Http/Controllers/{Role}/DashboardController.php` and corresponding routes prefixed by role name.

**Middleware Usage**: Apply `middleware('role:admin,operator')` to routes - admin automatically gets access to everything (see `app/Http/Middleware/CheckRole.php`).

### 2. Dashboard Routing Pattern

The main `/dashboard` route redirects users to role-specific dashboards using a match expression:

```php
// app/Http/Controllers/DashboardController.php
return match ($user->getRoleName()) {
    Role::ADMIN => redirect()->route('admin.dashboard'),
    Role::OPERATOR => redirect()->route('operator.dashboard'),
    // ...
};
```

Routes are organized by role with clear prefixes: `/admin/*`, `/operator/*`, `/verifikator/*`, etc.

### 3. Data Model Hierarchy

**Core Entity**: `DataPenduduk` (Citizen) with `nik` as primary identifier
- Uses enums for typed fields: `JenisKelaminEnum`, `StatusAktifEnum`, `JabatanRtRwEnum`
- Implements `SoftDeletes` for data audit trails
- Related to `DataKeluarga` (Family), `DataRtRw` (Neighborhood), and various services

**Pattern**: Models are prefixed with `Data` (e.g., `DataPenduduk`, `DataKeluarga`, `DataUsaha`) to distinguish from reference/lookup tables.

### 4. UI Component System

**ALWAYS use the custom Blade components** from `resources/views/components/ui/`:
- `<x-ui.button>`, `<x-ui.input>`, `<x-ui.select>`, `<x-ui.card>`, etc.
- Components follow DaisyUI conventions with Alpine.js interactivity

**Select Component Pattern** (see `.github/gunakan komponen ui ini.instructions.md`):
```blade
<x-ui.select 
    label="Pilih Kategori" 
    name="category" 
    :options="$categories" 
    selected="{{ old('category', $selectedCategory) }}" 
/>
```

Options can be: `[value => label]` OR `[['value' => x, 'label' => y]]` format. Component handles normalization.

### 5. Document Processing Workflow

**Standard Letter Flow** (UC-SRT-01 to UC-SRT-06):
1. **Registration** (Draft) → 2. **Verification** → 3. **Auto-numbering** → 4. **Signing** → 5. **Print/Archive** → 6. **Tracking**

- Templates use field mapping for dynamic content generation
- Automatic numbering per document type with year sequence
- QR/Barcode support for document verification
- PDF archival with audit trail (who processed when)

### 6. Business Logic Patterns

**User Active Status Check**: Always verify `is_active` before allowing access (implemented in `CheckRole` middleware)

**Audit Logging**: System tracks all data modifications - maintain this pattern when adding features

**RT/RW Hierarchy**: Neighborhood (RW) contains multiple community units (RT) - enforce this relationship in data validation

## Development Workflow

### Setup & Running

```bash
# Initial setup
composer run setup  # Installs deps, copies .env, generates key, migrates DB

# Development (runs all services concurrently)
composer run dev    # Starts: server, queue, logs (pail), vite

# Testing
composer run test   # Clears config & runs PHPUnit
```

**Database**: Default is SQLite at `database/database.sqlite` (auto-created on setup)

### Seeders

Run `php artisan db:seed` to populate:
- 6 roles with permissions (see `DatabaseSeeder.php`)
- Sample users (one per role): `admin@batua.com`, `operator@batua.com`, etc.
- Test data for all major entities (30+ seeders available)

### Frontend Build

```bash
npm run dev   # Vite dev server with hot reload
npm run build # Production build
```

**Note**: Vite ignores `storage/framework/views/**` to prevent compile loops

## Conventions & Patterns

### 1. Naming Conventions

- **Models**: `DataPenduduk`, `DataUsaha` (prefixed with `Data`)
- **Enums**: Suffixed with `Enum` (e.g., `JenisKelaminEnum`)
- **Controllers**: Namespaced by role: `Admin\DashboardController`, `Operator\DashboardController`
- **Routes**: Named with role prefix: `admin.dashboard`, `operator.penduduk.index`

### 2. Form Handling

- Use `old('field', $default)` for form repopulation
- Validate with FormRequest classes (create when needed)
- Return with `->withErrors()` for validation failures

### 3. Blade Templates

- **NO generic layouts** - each role dashboard is in `resources/views/pages/{role}/dashboard.blade.php`
- Use `@props` for component definitions
- Leverage Alpine.js for interactive features (already included)

### 4. Eloquent Relationships

- Define relationships in models explicitly (e.g., `DataPenduduk->keluarga()`)
- Use `BelongsTo`, `HasMany`, `HasOne` consistently
- Leverage eager loading to prevent N+1 queries

### 5. Security

- Passwords are auto-hashed via `'password' => 'hashed'` cast in User model
- Session timeout is handled via middleware
- Role permissions stored as JSON in roles table
- Admin bypasses all role checks (see `CheckRole` middleware)

## Common Tasks & Examples

### Adding a New CRUD Module

1. Create model with factory/seeder: `php artisan make:model DataX -mfs`
2. Define enum if needed in `app/Enum/`
3. Add route group with role middleware in `routes/web.php`
4. Create controller under appropriate role namespace
5. Build views using `<x-ui.*>` components
6. Add factory data following existing patterns (use Faker)

### Implementing Document Type

1. Add entry to `jenis_surat` reference table
2. Create template in `template_surat` with field mappings
3. Follow UC-SRT workflow: registration → verification → numbering → signing
4. Generate PDF using template engine (implement when needed)
5. Archive with audit log entry

### Adding Role-Specific Feature

1. Check role constant in `Role.php`
2. Add permission to role seeder in `DatabaseSeeder.php`
3. Create route with `middleware('role:...')`
4. Implement controller under role-specific namespace
5. Build view in `resources/views/{role}/`

## Architecture Decisions & "Why"

**Single NIK Source of Truth**: All services reference `DataPenduduk` by NIK to ensure data consistency and prevent duplication

**Role-Based Dashboards**: Separate controllers per role prevent complex conditional logic and improve maintainability

**Template-Driven Letters**: Allows non-technical staff to modify document formats without code changes

**Soft Deletes**: Required for audit compliance - never hard-delete citizen or transaction data

**SQLite Default**: Simplifies deployment for small kelurahan offices without dedicated DB servers (can switch to MySQL/PostgreSQL in `.env`)

## Key Files Reference

- **Controllers**: `app/Http/Controllers/`
  - Admin: `Admin/{Dashboard,User,Role,Penduduk,Keluarga,Wilayah,Penandatangan,Pegawai}Controller.php`
  - Role Dashboards: `{Operator,Verifikator,Penandatangan,RtRw,Warga}/DashboardController.php`
- **Models**: `app/Models/{User,Role,DataPenduduk,DataKeluarga,DataRtRw,Penandatanganan,PegawaiStaff}.php`
- **Enums**: `app/Enums/{JenisKelaminEnum,StatusAktifEnum,JabatanRtRwEnum}.php`
- **Middleware**: `app/Http/Middleware/CheckRole.php`
- **Routes**: `routes/web.php` (46 admin routes + role-specific groups)
- **Tests**: `tests/Feature/Admin/*Test.php` (9 files, 108 tests)
- **Seeders**: `database/seeders/DatabaseSeeder.php` (39 seeders)
- **Factories**: `database/factories/*.php` (33 factories, all fixed)
- **UI Components**: `resources/views/components/ui/*.blade.php`
- **Views**: `resources/views/pages/{admin,operator,verifikator,penandatangan,rtrw,warga}/`
- **Concept Doc**: `.github/konsep aplikasi.instructions.md`
- **Component Guide**: `.github/gunakan komponen ui ini.instructions.md`

## Current Route Structure

```php
// Admin routes (middleware: role:admin)
/admin/dashboard
/admin/users (CRUD + toggle-active)
/admin/roles (index only)
/admin/penduduk (CRUD)
/admin/keluarga (CRUD)
/admin/wilayah (CRUD - RT/RW management)
/admin/penandatangan (CRUD)
/admin/pegawai (CRUD)
/admin/audit-log

// Role-specific dashboards
/operator/dashboard
/verifikator/dashboard
/penandatangan/dashboard
/rtrw/dashboard
/warga/dashboard

// Shared routes (multi-role access)
/kependudukan/* (admin, operator, rt_rw)
/persuratan/* (admin, operator, verifikator, penandatangan, warga)
/usaha/* (admin, operator)
/laporan/* (admin, operator, verifikator)
```

- User/Role logic: `app/Models/{User,Role}.php`, `app/Http/Middleware/CheckRole.php`
- Routing: `routes/web.php` (organized by role sections with visual separators)
- UI Components: `resources/views/components/ui/*.blade.php`
- Seeders: `database/seeders/DatabaseSeeder.php` (roles & sample users)
- App concept: `.github/konsep aplikasi.instructions.md` (detailed use cases UC-*)
- Component guide: `.github/gunakan komponen ui ini.instructions.md`

## Role Capabilities & Workflows

### 1. ADMIN (Administrator Sistem)
**Login**: `admin@batua.com` / `password`  
**Access**: Full system access — bypasses all role checks

**Capabilities**:
- ✅ **User Management**: CRUD users, assign roles, toggle active status
- ✅ **Role Management**: View roles and permissions (read-only to prevent accidental changes)
- ✅ **Master Data**: Manage wilayah (kecamatan, kelurahan, RW, RT), jenis surat, template, referensi
- ✅ **Kependudukan**: Full CRUD penduduk & keluarga data
- ✅ **Persuratan**: View all documents, override statuses if needed
- ✅ **Pegawai & Penandatangan**: Manage staff and authorized signers
- ✅ **Usaha (PK5/UMKM)**: Full CRUD business registrations
- ✅ **Audit Log**: View all system activities and modifications
- ✅ **Laporan**: Access all reports, export data

**Typical Workflows**:
1. Initial system setup: create wilayah structure, configure document templates
2. User account management: create operator/verifikator accounts, assign territories
3. System maintenance: backup data, audit user activities, troubleshoot issues
4. Data correction: fix errors made by operators (last resort)

**Important**: Admin should rarely perform data entry — delegate to operators. Admin focuses on configuration, oversight, and troubleshooting.

---

### 2. OPERATOR (Operator Kelurahan)
**Login**: `operator@batua.com` / `password`  
**Access**: Data entry, document processing, limited editing

**Capabilities**:
- ✅ **Kependudukan**: Create/update penduduk & keluarga, input mutasi (pindah/datang/lahir/mati)
- ✅ **Persuratan - Registration**: 
  - Receive citizen requests (walk-in or online via warga portal)
  - Create new surat (draft status)
  - Input pemohon data, attach requirements
  - Validate completeness before forwarding to verifikator
- ✅ **Ekspedisi**: Register incoming/outgoing mail, assign to handlers
- ✅ **Usaha**: Register new UMKM/PK5, update business data
- ✅ **Data Umum**: Input faskes, sekolah, tempat ibadah, petugas kebersihan, kendaraan
- ✅ **View Reports**: Read-only access to kependudukan & persuratan statistics

**Typical Workflows**:
1. **Surat Registration** (UC-SRT-01):
   - Warga datang dengan berkas
   - Operator verifikasi kelengkapan dokumen
   - Input data pemohon & perihal surat
   - Upload scan berkas (arsip_path)
   - Status: `draft` → kirim ke verifikator
   
2. **Penduduk Entry**:
   - Input NIK (validasi unique + format)
   - Lengkapi data biodata, pilih RT/RW
   - Assign to keluarga (existing/new)
   - Auto-update jumlah_anggota_keluarga
   
3. **Daily Tasks**: Process 10-30 surat requests, update penduduk data as needed

**Important**: Operator CANNOT approve/sign documents — only prepare & forward. Always attach digital scan of physical documents.

---

### 3. VERIFIKATOR (Kasi/Seklur)
**Login**: `verifikator@batua.com` / `password`  
**Access**: Document verification, data validation, approval/rejection

**Capabilities**:
- ✅ **Persuratan - Verification** (UC-SRT-02):
  - View queue of draft surat from operators
  - Verify data accuracy vs. kependudukan database
  - Check document completeness & requirements
  - Approve (→ status `proses`) or Reject (→ status `draft` with notes)
- ✅ **Data Validation**: Cross-check penduduk data, flag inconsistencies
- ✅ **Correction Authority**: Request operator to fix errors before approval
- ✅ **Reports**: View verification metrics, rejection reasons, processing time

**Typical Workflows**:
1. **Surat Verification**:
   - Open surat with status `draft`
   - Verify NIK exists in database & data matches
   - Check required attachments (KK scan, KTP, etc.)
   - Decision:
     - ✅ Approve → status `proses`, forward to auto-numbering
     - ❌ Reject → add notes, return to operator
   
2. **Quality Control**: Review 50-100 documents daily, maintain <5% rejection rate

**Important**: Verifikator ensures data integrity — never approve incomplete/inaccurate documents. Be the guardian between operator input and official signing.

---

### 4. PENANDATANGAN (Lurah/Pejabat Berwenang)
**Login**: `penandatangan@batua.com` / `password`  
**Access**: Digital signing, document finalization

**Capabilities**:
- ✅ **Persuratan - Signing** (UC-SRT-04):
  - View queue of verified surat (status `proses`)
  - Review surat content & perihal
  - Digital sign (→ status `signed`) or Decline (→ status `reject`)
- ✅ **Dashboard Overview**: See pending signatures count, daily signing stats
- ✅ **Priority Handling**: Filter by sifat (Sangat Segera > Segera > Biasa)

**Typical Workflows**:
1. **Batch Signing**:
   - Login sekali per hari (pagi/sore)
   - Review 20-50 pending surat
   - Prioritize by urgency (Sangat Segera first)
   - Bulk sign valid documents
   - Reject if ada keraguan → return to verifikator with reason
   
2. **Signature Authority**: Only sign documents that have been verified. Trust the verification process.

**Important**: Penandatangan should NEVER need to verify data accuracy — that's verifikator's job. Focus on content appropriateness and signature authority.

---

### 5. RT/RW (Ketua RT atau RW)
**Login**: `rtrw@batua.com` / `password`  
**Access**: Neighborhood-level data, surat pengantar, monitoring

**Capabilities**:
- ✅ **Kependudukan - View Only**: See penduduk & keluarga in assigned RT/RW
- ✅ **Surat Pengantar**: Issue RT/RW endorsement letters (required for many kelurahan services)
- ✅ **Monitoring**: Track warga registrations, document requests from their area
- ✅ **Laporan Wilayah**: View RT/RW-specific statistics (population, KK count, etc.)
- ✅ **Pengaduan**: Receive & forward citizen complaints to kelurahan

**Typical Workflows**:
1. **Surat Pengantar**:
   - Warga request pengantar from RT/RW
   - RT/RW verify warga domicile (actually lives there)
   - Issue pengantar with RT/RW stamp
   - Warga brings pengantar to kelurahan for official surat
   
2. **Data Updates**: Inform kelurahan of new residents, deaths, moves (for operator to process)

**Important**: RT/RW acts as first-level filter — know your citizens. Only endorse requests from actual residents.

---

### 6. WARGA (Citizen Portal)
**Login**: `warga@batua.com` / `password`  
**Access**: Self-service document requests, tracking, downloads

**Capabilities**:
- ✅ **Permohonan Surat**: Submit online document requests (alternative to walk-in)
  - Fill form with required data
  - Upload scanned requirements (KTP, KK, etc.)
  - Submit → operators receive notification
- ✅ **Tracking**: Real-time status of submitted requests
  - Draft → Proses → Signed → Ready for Pickup
  - See rejection reasons if any
- ✅ **Download**: Download signed PDF documents (once status = `signed`)
- ✅ **Riwayat**: View all past requests and documents

**Typical Workflows**:
1. **Request Surat Online**:
   - Login with NIK-based account
   - Choose surat type (SKTM, Domisili, Usaha, etc.)
   - Fill form, auto-populate from database
   - Upload KTP & KK scan
   - Submit → wait for notification
   
2. **Track Progress**: Check dashboard for status updates, download when ready

**Important**: Warga portal is OPTIONAL — most citizens still prefer walk-in service. Use as convenience, not replacement.

---

## Role Interaction Flow Example

**Scenario**: Warga needs Surat Keterangan Usaha (SKU)

1. **Warga** (optional): Submit online via portal OR walk to kelurahan
2. **RT/RW**: Issue surat pengantar (proof of domicile)
3. **Operator**: 
   - Receive request + pengantar
   - Verify completeness (KTP, KK, NPWP, pengantar RT)
   - Create surat record, status = `draft`
   - Upload scanned docs
4. **Verifikator**:
   - Verify NIK matches database
   - Check business address in RT/RW jurisdiction
   - Approve → status = `proses`
   - System auto-generates nomor surat: `517/001/BTU/II/2026`
5. **Penandatangan**:
   - Review & digital sign
   - Status = `signed`
6. **Operator**: Print & archive, notify warga via SMS/WhatsApp
7. **Warga**: Pick up physical surat OR download PDF

**Timeline**: 1-3 days (depends on verifikator & penandatangan availability)

---

## Critical Notes

⚠️ **Never hard-code role names** - use `Role::ADMIN`, `Role::OPERATOR`, etc.

⚠️ **Always check `is_active` status** when implementing user-related features

⚠️ **Use UI components** - don't write raw HTML for form elements (consistency requirement)

⚠️ **Maintain audit trail** - log who did what when for all data modifications

⚠️ **NIK is sacred** - validate uniqueness and format before allowing data entry
