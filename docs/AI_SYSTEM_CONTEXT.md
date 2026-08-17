# AI System Context

## Quick System Overview

**System Name:** ESA School Management and E-Learning System
**Framework:** Laravel 12.5
**PHP Version:** 8.2+
**Database:** SQLite (dev), MySQL/PostgreSQL (prod)
**Purpose:** Comprehensive school management with e-learning capabilities

---

## System at a Glance

### What This System Does
- Manages student registration and academic records
- Handles course delivery and assessment
- Processes payments and financial operations
- Supports live online classes and resources
- Generates report cards and transcripts
- Manages multiple school branches
- Integrates with national examination board (NESA)

### Key Statistics
- **Database Tables:** 65+
- **Routes:** 200+
- **User Roles:** 6 distinct types
- **Core Modules:** 8 major systems
- **Controllers:** 60+
- **Models:** 70+

---

## User Roles and Capabilities

### 1. Administrators (admin guard)
- **Access:** Full system administration
- **Can Manage:** Students, teachers, staff, courses, branches, fees, exams, report cards
- **Cannot:** Be restricted by other roles (highest privilege)
- **Dashboard:** `/admin/dashboard`

### 2. Staff (staff guard)
- **Sub-roles:** DOS (Director of Studies), Accountant, Secretary
- **DOS Can:** Manage courses, live classes, approve report cards, oversee academics
- **Accountant Can:** Monitor finances, payment schedules, expenses
- **Secretary Can:** Handle student registration, client management, token distribution
- **Dashboard:** `/staff/dashboard`

### 3. Teachers (teacher guard)
- **Can Manage:** Question banks, live classes, resources, daily reports
- **Can Access:** Assigned courses and students
- **Cannot:** Access financial data or system configuration
- **Dashboard:** `/teacher/dashboard`

### 4. Students (student guard)
- **Can Access:** Their courses, resources, exams, grades, payments
- **Can Take:** Exams, participate in live classes
- **Cannot:** Modify system data or access other students' information
- **Dashboard:** Student profile and course pages

### 5. Clients (client guard)
- **Can Purchase:** Subscriptions, tokens, exam access
- **Can Access:** Purchased content and exams
- **Cannot:** Access internal school operations
- **Dashboard:** `/public-subscriptions/dashboard`

### 6. Public Visitors (no authentication)
- **Can View:** Public announcements, fee schedules, public exams, babyeyi content
- **Cannot:** Access authenticated features
- **Limitations:** Read-only access to public content

---

## Core System Modules

### 1. User Management Module
**Purpose:** Handle all user types and authentication
**Key Tables:** `users`, `admins`, `teachers`, `staff`, `students`, `clients`
**Key Features:** Multi-guard authentication, profile management, password reset

### 2. Academic Structure Module
**Purpose:** Organize educational content
**Key Tables:** `academic_years`, `terms`, `branches`, `courses`, `lessons`, `timetables`
**Key Features:** Multi-branch support, hierarchical organization, scheduling

### 3. Assessment Module
**Purpose:** Create and manage exams and assessments
**Key Tables:** `exams`, `questions`, `exam_attempts`, `marks`, `grades`
**Key Features:** Question banks, anti-cheating, automated grading, report cards

### 4. Financial Module
**Purpose:** Handle all monetary operations
**Key Tables:** `payments`, `subscriptions`, `tokens`, `accounts`, `journal_entries`
**Key Features:** Flutterwave integration, double-entry accounting, token system

### 5. Content Management Module
**Purpose:** Manage educational resources
**Key Tables:** `resources`, `blogs`, `announcements`
**Key Features:** Access control, pricing, watermarking, public/private content

### 6. Live Learning Module
**Purpose:** Enable real-time online instruction
**Key Tables:** `live_classes`, `live_class_participants`, `live_class_signals`
**Key Features:** WebRTC video, real-time signaling, attendance tracking

### 7. Report Card Module
**Purpose:** Generate academic performance reports
**Key Tables:** `report_cards`, `report_card_subjects`, `school_informations`
**Key Features:** PDF generation, approval workflow, subject-wise marks

### 8. NESA Integration Module
**Purpose:** Comply with national examination requirements
**Key Tables:** `students` (NESA fields)
**Key Features:** Student registration, document management, status tracking

---

## Database Architecture Highlights

### Design Principles
- **Normalization:** 3NF compliance
- **Relationships:** Proper foreign keys and constraints
- **Indexing:** Strategic indexes for performance
- **No Soft Deletes:** Hard deletes with cascade operations
- **JSON Fields:** Flexible data storage for evolving requirements

### Critical Tables
- **students:** Central to most operations (courses, payments, exams, etc.)
- **courses:** Academic content organization
- **exams:** Assessment system core
- **payments:** Financial transactions
- **live_classes:** Real-time learning
- **report_cards:** Academic reporting

### Key Relationships
- Students belong to branches, courses, and academic years
- Teachers teach multiple courses and branches
- Exams link to courses, terms, and teachers
- Payments connect to students and subscriptions
- Live classes involve teachers, students, and content

---

## Technology Stack

### Backend
- **Framework:** Laravel 12.5
- **PHP:** 8.2+
- **Database:** SQLite (dev), MySQL/PostgreSQL (prod)
- **Authentication:** Laravel multi-guard system
- **Queue:** Laravel queues for background jobs

### Frontend
- **Build Tool:** Vite 6.2.4
- **CSS:** Tailwind CSS 3.1.0
- **JavaScript:** Alpine.js 3.4.2, React 19.2.4
- **Icons:** React Icons 5.5.0
- **Animations:** GSAP 3.14.2, Framer Motion 12.38.0

### Key Integrations
- **Payments:** Flutterwave v3
- **PDF:** DomPDF 3.1, TCPDF 6.11
- **Excel:** PhpSpreadsheet 1.30
- **Images:** Intervention Image 3.11
- **QR Codes:** Simple-QRCode 4.2
- **Real-time:** Laravel Livewire 4.2

---

## Authentication System

### Multi-Guard Architecture
- **admin:** Administrator authentication
- **staff:** Staff authentication (DOS, Accountant, Secretary)
- **teacher:** Teacher authentication
- **student:** Student authentication
- **client:** Client authentication
- **web:** Default web authentication

### Authentication Flow
1. User selects role-specific login
2. Credentials validated against role-specific table
3. Session created with role-specific guard
4. User redirected to role-specific dashboard
5. Middleware protects routes based on guard

### Password Reset
- Separate password reset flows per user type
- Email-based reset codes
- Role-specific reset tables

---

## Authorization System

### Route Protection
- Middleware-based route protection
- Guard-specific middleware (`auth:admin`, `auth:staff`, etc.)
- Role-specific route groups
- Academic year context for admin/staff

### Frontend Authorization
- UI elements hidden based on user role
- Role-specific navigation menus
- Dashboard customization per role

### Backend Authorization
- Controller-level permission checks
- Service-layer authorization
- Database-level constraints (foreign keys)

---

## Key Business Workflows

### Student Registration
1. Staff creates student record
2. Student assigned to branch and course
3. Academic year linked
4. Registration fee processed
5. Student portal access granted

### Exam Taking
1. Teacher/admin creates exam
2. Questions added (from bank or new)
3. Exam configured with settings
4. Student pays (if required)
5. Student takes exam with anti-cheating
6. Automatic/manual grading
7. Results released
8. Report card integration

### Payment Processing
1. Student initiates payment
2. Payment token generated
3. Flutterwave integration processes payment
4. Callback confirms payment
5. Transaction status updated
6. Access granted (if successful)

### Live Class
1. Teacher schedules live class
2. Access control configured
3. Students/public request access
4. Teacher approves requests
5. WebRTC connection established
6. Real-time communication
7. Attendance tracked
8. Class ends (recording if enabled)

---

## Security Architecture

### Authentication Security
- Session-based authentication
- Bcrypt password hashing
- Separate password reset flows
- Email verification (table exists, integration unclear)

### Authorization Security
- Middleware-protected routes
- Role-based access control
- Academic year isolation
- Public/private content separation

### Payment Security
- Flutterwave secure payment gateway
- Unique transaction references
- Callback verification
- Token-based access control

### Exam Security
- Anti-cheating measures (tab switching, fullscreen monitoring)
- Session tracking
- Time limits
- Question randomization
- Violation logging

---

## Important File Locations

### Configuration
- `config/auth.php` - Authentication guards and providers
- `config/database.php` - Database configuration
- `config/app.php` - Application configuration
- `.env` - Environment variables (not in version control)

### Routes
- `routes/web.php` - Main web routes (200+ routes)
- `routes/auth.php` - Authentication routes
- `routes/api.php` - API routes

### Controllers
- `app/Http/Controllers/` - Main controllers
- `app/Http/Controllers/Admin/` - Admin controllers
- `app/Http/Controllers/Staff/` - Staff controllers
- `app/Http/Controllers/Teacher/` - Teacher controllers

### Models
- `app/Models/` - Eloquent models (70+ models)

### Views
- `resources/views/` - Blade templates
- `resources/views/admin/` - Admin views
- `resources/views/staff/` - Staff views
- `resources/views/teacher/` - Teacher views

### Database
- `database/migrations/` - Migration files (68 files)
- `database/seeders/` - Database seeders

---

## Critical Rules for AI Development

### Must Always Do
1. Read relevant documentation before making changes
2. Test with all affected user roles
3. Backup data before database changes
4. Follow existing code patterns
5. Use version control
6. Test rollback procedures
7. Document changes

### Must Never Do
1. Modify existing migration files
2. Hardcode credentials
3. Bypass authentication/authorization
4. Delete existing functionality without approval
5. Assume database structure
6. Ignore error messages
7. Deploy without testing

---

## Known Limitations

### Technical Debt
- No soft deletes (hard deletes only)
- Inconsistent naming conventions
- Missing audit logging
- Some nullable foreign keys
- Large JSON fields may impact performance

### Security Considerations
- Public exam access needs review
- WebRTC signaling lacks authentication
- Development routes need production protection
- One-time login needs security review

### Performance Considerations
- No read replicas
- Missing composite indexes
- Large table scans for report cards
- JSON field usage optimization needed

---

## Change Impact Guidelines

### High-Impact Changes
- Database schema changes
- Authentication/authorization changes
- Payment integration changes
- Core workflow modifications

### Medium-Impact Changes
- New feature additions
- UI/UX changes
- Controller modifications
- Route additions

### Low-Impact Changes
- Bug fixes
- UI improvements
- Performance optimizations
- Documentation updates

---

## Development Workflow

### Before Changes
1. Read AI_QUICK_START.md
2. Read AI_SYSTEM_CONTEXT.md (this file)
3. Read AI_DEVELOPMENT_RULES.md
4. Identify task type and read relevant docs
5. Understand change impact
6. Plan implementation

### During Implementation
1. Follow existing patterns
2. Make smallest safe changes
3. Test incrementally
4. Handle errors gracefully
5. Log important operations

### After Implementation
1. Run tests
2. Test with all affected roles
3. Verify database operations
4. Check for regressions
5. Update documentation

---

## System Status

**Overall Health:** ✅ Production-Ready
**Last Major Update:** August 2026
**Current Version:** Laravel 12.5
**Database Version:** Latest migration August 12, 2026

**Functionality Status:**
- User Management: ✅ Complete
- Academic Structure: ✅ Complete
- Assessment System: ✅ Complete
- Financial System: ✅ Complete
- Content Management: ✅ Complete
- Live Learning: ✅ Complete
- Report Cards: ✅ Complete
- NESA Integration: ✅ Complete

---

## Quick Decision Tree

### What type of task are you working on?

**Bug Fix?**
→ Read KNOWN_ISSUES.md → Read feature docs → Implement fix → Test affected roles

**New Feature?**
→ Read FEATURE docs → Read USER_ROLES docs → Read BUSINESS_RULES → Plan implementation → Test thoroughly

**Database Change?**
→ Read DATABASE docs → Read CHANGE_IMPACT_GUIDE → Create migration → Test rollback → Deploy carefully

**Security Change?**
→ Read RLS_AND_SECURITY docs → Read AUTH docs → Test with all roles → Security review

**UI Change?**
→ Read FRONTEND docs → Read ROUTES docs → Follow design system → Test responsiveness

**Performance Issue?**
→ Read DATABASE docs → Read TECHNICAL_DEBT → Analyze queries → Optimize carefully

---

## Documentation References

### Essential Reading
- AI_QUICK_START.md - Reading order guide
- AI_DEVELOPMENT_RULES.md - Mandatory development rules
- AI_SYSTEM_CONTEXT.md - This file

### System Documentation
- SYSTEM_OVERVIEW.md - Comprehensive system introduction
- ARCHITECTURE.md - Detailed technical architecture
- USER_ROLES_AND_PERMISSIONS.md - Complete role documentation

### Database Documentation
- DATABASE_ARCHITECTURE.md - Database design overview
- DATABASE_TABLE_REFERENCE.md - Complete table documentation
- RLS_AND_SECURITY.md - Security policies

### Change Management
- CHANGE_IMPACT_GUIDE.md - Impact analysis
- KNOWN_ISSUES.md - Known problems
- TECHNICAL_DEBT.md - Architectural debt

---

## Contact and Support

**System Owner:** Hirwa Alain Brian
**Contact:** 0794900225
**Location:** Kigali, NdamaGE House, 2nd Floor

**For AI Development Issues:**
- Refer to documentation first
- Check AI_DEVELOPMENT_RULES.md
- Review CHANGE_IMPACT_GUIDE.md
- Ask clarifying questions when uncertain

---

## Final Notes

This context document provides a quick reference for AI developers working on the ESA School Management and E-Learning System. For detailed information, refer to the specific documentation files listed above.

**Remember:**
- The system is production-ready and fully functional
- Follow existing patterns and conventions
- Test thoroughly with all affected user roles
- Never break existing functionality
- Document your changes

**Success depends on:**
- Understanding the system before changing it
- Following the established rules
- Testing comprehensively
- Communicating clearly
- Learning from each change

---

*This document serves as the quick reference for AI development. For comprehensive information, read the full documentation suite as outlined in AI_QUICK_START.md.*