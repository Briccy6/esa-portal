# ESA School Management and E-Learning System - System Overview

## Contact Information

- **Owner:** Hirwa Alain Brian
- **Contact Number:** 0794900225
- **System Name:** ESA (Excellent Servant Academy)
- **Location:** Kigali, NdamaGE House, 2nd Floor

---

## 1. What This System Is

This is a comprehensive **Laravel-based school management and e-learning platform** designed to manage educational institution operations with both traditional school administration and digital learning capabilities.

### Core Capabilities

- **Student Management:** Registration, profiles, enrollment, academic tracking
- **Academic Management:** Courses, lessons, units, timetables, terms, academic years
- **Assessment System:** Exams, questions, marks, grades, report cards
- **Financial Management:** Fees, payments, subscriptions, tokens, accounting
- **Content Management:** Educational resources, blogs, announcements
- **Live Learning:** Online classes, WebRTC integration, attendance tracking
- **Multi-Branch Support:** Physical and online branch management
- **Public Access:** Fee schedules, exam listings, educational content
- **NESA Integration:** National examination board compliance

### Target Users

- **Students:** Enrolled learners accessing courses, resources, and assessments
- **Teachers:** Educators managing content, live classes, and assessments
- **Staff:** Administrative personnel (DOS, Accountant, Secretary)
- **Administrators:** System managers with full platform control
- **Clients:** External users purchasing subscriptions and exam access
- **Public Visitors:** Visitors accessing public information and content

---

## 2. Main Purpose of the System

The system is designed to:

1. **Centralize School Administration** - Manage all school operations from a single platform
2. **Enable Digital Learning** - Support online classes, resources, and assessments
3. **Streamline Financial Operations** - Track fees, payments, subscriptions, and accounting
4. **Facilitate Assessment Management** - Create, administer, and grade exams and report cards
5. **Support Multi-Branch Operations** - Manage multiple physical and online locations
6. **Provide Public Information** - Share fee schedules, course information, and educational content
7. **Ensure Compliance** - Meet NESA (National Examination and School Inspection) requirements
8. **Enable Remote Learning** - Support students with online resources and live classes

---

## 3. Technology Stack

### Backend
- **Framework:** Laravel 12.5
- **PHP Version:** 8.2+
- **Database:** SQLite (development), MySQL/PostgreSQL (production)
- **Authentication:** Laravel Authentication with multiple guards
- **Queue System:** Laravel Queues for background jobs

### Frontend
- **Build Tool:** Vite 6.2.4
- **CSS Framework:** Tailwind CSS 3.1.0
- **JavaScript:** Alpine.js 3.4.2
- **React:** 19.2.4 (for specific components)
- **Icons:** React Icons 5.5.0
- **Animations:** GSAP 3.14.2, Framer Motion 12.38.0

### Key Packages
- **PDF Generation:** DomPDF 3.1, TCPDF 6.11
- **Excel:** PhpSpreadsheet 1.30, Maatwebsite Excel 3.1
- **Payment:** Flutterwave v3 1.1
- **Image Processing:** Intervention Image 3.11
- **QR Codes:** Simple-QRCode 4.2
- **Real-time:** Laravel Livewire 4.2
- **API:** Laravel Sanctum 4.3
- **Social Auth:** Laravel Socialite 5.24

---

## 4. System Architecture Overview

### Multi-Tier Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Presentation Layer                        │
│  (Blade Templates, React Components, Alpine.js)             │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                    Application Layer                         │
│  (Laravel Controllers, Middleware, Services)                │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                    Business Logic Layer                     │
│  (Eloquent Models, Business Rules, Validation)              │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                    Data Access Layer                        │
│  (Eloquent ORM, Query Builder, Database)                    │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                    Data Storage Layer                        │
│  (SQLite/MySQL/PostgreSQL, File Storage)                   │
└─────────────────────────────────────────────────────────────┘
```

### Authentication Architecture

The system uses **multiple authentication guards** to separate different user types:

- **admin** - System administrators
- **staff** - Non-teaching staff (DOS, Accountant, Secretary)
- **teacher** - Teaching staff
- **student** - Enrolled students
- **client** - External users

Each guard has:
- Separate user provider (Eloquent model)
- Separate password reset broker
- Session-based authentication
- Independent middleware protection

---

## 5. Core System Modules

### 5.1 User Management Module
**Purpose:** Manage all system users and their authentication

**Components:**
- User registration and profiles
- Multi-role authentication
- Password management
- Email verification
- Session management

**Key Tables:**
- `users`, `admins`, `teachers`, `staff`, `students`, `clients`
- `password_resets`, `admin_password_resets`
- `email_verifications`, `sessions`

### 5.2 Academic Structure Module
**Purpose:** Organize educational content and delivery

**Components:**
- Academic year management
- Term management
- Branch/location management
- Course/program management
- Lesson and unit management
- Timetable scheduling

**Key Tables:**
- `academic_years`, `terms`, `branches`, `courses`
- `lessons`, `lesson_units`, `timetables`, `timetable_sessions`
- Pivot tables for relationships

### 5.3 Assessment Module
**Purpose:** Create, administer, and grade assessments

**Components:**
- Exam creation and management
- Question bank management
- Exam taking with anti-cheating
- Automated and manual grading
- Report card generation
- Grade management

**Key Tables:**
- `exams`, `questions`, `question_banks`
- `exam_attempts`, `exam_sessions`, `student_answers`
- `marks`, `grades`, `exam_settings`
- `exam_analytics`, `anti_cheating_logs`
- `report_cards`, `report_card_subjects`, `report_card_test_marks`

### 5.4 Financial Module
**Purpose:** Manage all financial operations

**Components:**
- Fee management and scheduling
- Payment processing (Flutterwave)
- Subscription management
- Token-based purchasing
- Double-entry accounting
- Expense tracking
- Financial reporting

**Key Tables:**
- `payments`, `student_transactions`, `payment_records`
- `subscriptions`, `student_subscriptions`
- `tokens`, `token_transactions`
- `accounts`, `journal_entries`, `journal_items`
- `expenses`, `course_term_fees`

### 5.5 Content Management Module
**Purpose:** Manage educational content and resources

**Components:**
- Resource upload and management
- Access control and pricing
- Blog/content management
- Document watermarking
- Public vs private content

**Key Tables:**
- `resources`, `blogs`, `blog_images`, `blog_documents`
- `announcements`

### 5.6 Live Learning Module
**Purpose:** Enable real-time online instruction

**Components:**
- Live class scheduling
- WebRTC video/audio
- Real-time signaling
- Attendance tracking
- Access control and approval
- Recording capabilities

**Key Tables:**
- `live_classes`, `live_class_participants`
- `live_class_requests`, `live_class_payments`
- `live_class_signals`

### 5.7 Report Card Module
**Purpose:** Generate academic performance reports

**Components:**
- Report card generation
- Subject-wise marks
- Test mark normalization
- Approval workflow
- PDF generation
- School information management

**Key Tables:**
- `report_cards`, `report_card_subjects`, `report_card_test_marks`
- `school_informations`

### 5.8 NESA Integration Module
**Purpose:** Comply with national examination requirements

**Components:**
- Student NESA registration
- Document management
- Status tracking
- Reporting
- Email notifications

**Key Tables:**
- `students` (NESA fields)
- `nesa_registrations` (integrated in students table)

---

## 6. User Roles and Access Levels

### 6.1 Public Visitors
**Access:** No authentication required

**Capabilities:**
- View public announcements
- Browse public exams
- View fee schedules
- Access public educational content (Babyeyi)
- Download public resources

### 6.2 Students
**Authentication:** student guard

**Capabilities:**
- Profile management
- Course enrollment and viewing
- Resource access and download
- Exam taking
- Grade viewing
- Payment processing
- Live class participation
- Report card access
- Transcript viewing

### 6.3 Teachers
**Authentication:** teacher guard

**Capabilities:**
- Dashboard access
- Question bank management
- Live class management
- Resource creation and management
- Daily activity logging
- Marks recording
- Course/lesson assignment

### 6.4 Staff (Non-Teaching)
**Authentication:** staff guard

**Sub-roles:**
- **DOS (Director of Studies):** Academic oversight, course management, report card approval
- **Accountant:** Financial monitoring, payment schedules, expense tracking
- **Secretary:** Student registration, client management, token management

**Capabilities:**
- Student management (limited)
- Lesson management
- Payment processing
- Subscription management
- Role-specific dashboard and features

### 6.5 Administrators
**Authentication:** admin guard

**Capabilities:**
- Full system administration
- User management (all roles)
- Academic structure management
- Financial management and reporting
- Content management
- System configuration
- Report card approval
- Client management

### 6.6 Clients
**Authentication:** client guard

**Capabilities:**
- Subscription purchase
- Token purchase
- Dashboard access
- Exam access (based on subscription)
- Resource access (based on tokens)

---

## 7. Key Business Workflows

### 7.1 Student Registration Workflow
```
Public Registration Form
    ↓
Staff Review and Processing
    ↓
Branch and Course Assignment
    ↓
Academic Year Assignment
    ↓
Registration Fee Payment
    ↓
Account Creation
    ↓
Student Portal Access
```

### 7.2 Exam Creation and Taking Workflow
```
Teacher/Admin Creates Exam
    ↓
Add Questions (from bank or new)
    ↓
Configure Exam Settings
    ↓
Set Access Control (public/private)
    ↓
Student Payment (if required)
    ↓
Student Takes Exam
    ↓
Anti-Cheating Monitoring
    ↓
Automatic/Manual Grading
    ↓
Results Release
    ↓
Report Card Integration
```

### 7.3 Payment Processing Workflow
```
Student Initiates Payment
    ↓
Generate Payment Token
    ↓
Flutterwave Integration
    ↓
Payment Processing
    ↓
Callback Handling
    ↓
Update Transaction Status
    ↓
Grant Access (if successful)
    ↓
Send Confirmation
```

### 7.4 Live Class Workflow
```
Teacher Schedules Live Class
    ↓
Configure Access (students/public)
    ↓
Set Pricing (if public)
    ↓
Students/Public Request Access
    ↓
Teacher Approves Requests
    ↓
Class Starts (WebRTC)
    ↓
Real-time Communication
    ↓
Attendance Tracking
    ↓
Class Ends
    ↓
Recording (if enabled)
```

### 7.5 Report Card Generation Workflow
```
Term/Period Completion
    ↓
Collect Marks from Exams/Tests
    ↓
Normalize Marks to 100%
    ↓
Calculate Averages and Grades
    ↓
Generate Report Card
    ↓
Admin/DOS Review
    ↓
Approval Process
    ↓
PDF Generation
    ↓
Distribution to Students
```

---

## 8. Database Architecture

### Database Characteristics
- **68 Migration Files** defining the complete schema
- **65+ Tables** covering all system functionality
- **Proper Normalization** with appropriate foreign keys
- **Comprehensive Indexing** for performance
- **No Soft Deletes** (hard deletes with cascade)
- **No Database Triggers** (business logic in application)

### Key Database Features
- **Multi-User Authentication:** Separate tables for each user type
- **Academic Structure:** Hierarchical year → term → course → lesson → unit
- **Assessment System:** Complex exam-taking with anti-cheating
- **Financial System:** Double-entry accounting with journal entries
- **Content Management:** Blob storage for documents and images
- **Real-time Data:** JSON fields for flexible data storage

### Database Relationships
- **Users:** Belong to branches, courses, academic years
- **Teachers:** Many-to-many with courses and branches
- **Students:** Enrolled in branches, courses, academic years
- **Exams:** Linked to teachers, branches, courses, terms
- **Resources:** Associated with teachers, courses, branches, lessons
- **Live Classes:** Connected to teachers, branches, courses, lessons

---

## 9. Security Architecture

### Authentication Security
- **Multiple Guards:** Isolated authentication per user type
- **Session-Based:** Secure session management
- **Password Hashing:** Laravel's bcrypt hashing
- **Password Reset:** Separate reset flows per user type
- **Email Verification:** Optional verification system

### Authorization Security
- **Middleware Protection:** Route-level access control
- **Role-Based Access:** Different permissions per role
- **Academic Year Context:** Admin/staff year isolation
- **Public/Private Content:** Access control on resources and exams

### Payment Security
- **Flutterwave Integration:** Secure payment gateway
- **Transaction References:** Unique payment tracking
- **Callback Verification:** Secure payment confirmation
- **Token-Based Access:** Controlled resource access

### Exam Security
- **Anti-Cheating Measures:** Tab switching, fullscreen monitoring
- **Session Tracking:** Detailed exam session logging
- **Time Limits:** Enforced exam duration
- **Question Randomization:** Optional question/answer shuffling
- **Violation Logging:** Comprehensive security event tracking

---

## 10. Integration Points

### External Services
- **Flutterwave:** Payment processing
- **Google Meet:** Video conferencing integration
- **Email Services:** SMTP configuration for notifications
- **File Storage:** Local and cloud storage options

### Internal Integrations
- **PDF Generation:** DomPDF, TCPDF for document generation
- **Excel Processing:** PhpSpreadsheet for imports/exports
- **QR Code Generation:** Simple-QRCode for student cards
- **Image Processing:** Intervention Image for photo handling
- **Real-time:** Livewire for dynamic UI components

---

## 11. Current System State

### Implemented Features
- ✅ Multi-user authentication system
- ✅ Complete academic structure management
- ✅ Comprehensive exam system with anti-cheating
- ✅ Payment processing via Flutterwave
- ✅ Subscription and token system
- ✅ Resource management with access control
- ✅ Live class system with WebRTC
- ✅ Report card generation
- ✅ Double-entry accounting
- ✅ Blog/content management
- ✅ Timetable management
- ✅ NESA integration
- ✅ Multi-branch support

### Partially Implemented Features
- ⚠️ Email verification (table exists, integration unclear)
- ⚠️ Some public exam features (access control needs review)
- ⚠️ Live class recording (configuration exists, implementation unclear)

### Placeholder/Unused Features
- ❓ Several test files and scripts in root directory
- ❓ Disabled migration files (`.disabled` extension)
- ❓ Development routes that should be production-protected

---

## 12. System Limitations and Known Issues

### Technical Debt
- **No Soft Deletes:** Hard deletes make audit trails difficult
- **Inconsistent Naming:** Mix of singular/plural table names
- **Missing Audit Logging:** No systematic change tracking
- **No Database Constraints:** Some business rules not enforced at DB level
- **Large JSON Fields:** May impact performance at scale

### Security Considerations
- **Public Exam Access:** Full exam system accessible without authentication
- **WebRTC Signaling:** No authentication on real-time signaling routes
- **Development Routes:** Some dev routes should be production-protected
- **One-Time Login:** Signed URL route needs security review

### Performance Considerations
- **No Read Replicas:** All queries hit primary database
- **Missing Database Indexes:** Some query patterns may need optimization
- **Large Table Scans:** Report card queries may be slow at scale
- **JSON Field Usage:** May impact query performance

---

## 13. Development Environment

### Local Development
- **Database:** SQLite (`database/database.sqlite`)
- **Asset Compilation:** Vite dev server
- **Queue Processing:** Laravel queue worker
- **Mail:** Log driver (development)

### Production Considerations
- **Database:** MySQL or PostgreSQL recommended
- **File Storage:** Cloud storage (S3, etc.) for scalability
- **Queue:** Redis or database queue for production
- **Cache:** Redis for session and cache storage
- **Mail:** SMTP or email service provider

---

## 14. Documentation Files

This system includes the following AI-focused documentation:

- **AI_SYSTEM_CONTEXT.md** - Quick reference for AI developers
- **AI_QUICK_START.md** - Recommended reading order for AI agents
- **SYSTEM_OVERVIEW.md** - This file
- **ARCHITECTURE.md** - Detailed system architecture
- **USER_ROLES_AND_PERMISSIONS.md** - Complete role documentation
- **AUTHENTICATION_AND_AUTHORIZATION.md** - Security implementation
- **DATABASE_ARCHITECTURE.md** - Database design and relationships
- **DATABASE_TABLE_REFERENCE.md** - Complete table documentation
- **RLS_AND_SECURITY.md** - Security policies and rules
- **FEATURES.md** - Feature inventory and documentation
- **USER_WORKFLOWS.md** - End-to-end business workflows
- **ROUTES_AND_PAGES.md** - Complete route inventory
- **FRONTEND_ARCHITECTURE.md** - Frontend technology and patterns
- **BACKEND_ARCHITECTURE.md** - Backend services and integrations
- **BUSINESS_RULES.md** - Business logic and constraints
- **INTEGRATIONS.md** - External service integrations
- **AI_DEVELOPMENT_RULES.md** - Guidelines for AI developers
- **DEVELOPMENT_GUIDELINES.md** - Coding standards and conventions
- **KNOWN_ISSUES.md** - Documented bugs and problems
- **TECHNICAL_DEBT.md** - Architectural debt and improvements
- **CHANGE_IMPACT_GUIDE.md** - Impact analysis for changes
- **SYSTEM_MAP.md** - Visual system architecture
- **ROLE_WORKFLOWS.md** - Role-specific user journeys
- **GLOSSARY.md** - System terminology
- **SYSTEM_AUDIT_REPORT.md** - Complete audit findings

---

## 15. Next Steps for AI Development

Before making any changes to this system, AI agents should:

1. **Read AI_QUICK_START.md** - Understand the recommended documentation reading order
2. **Read AI_SYSTEM_CONTEXT.md** - Get the quick system overview
3. **Review AI_DEVELOPMENT_RULES.md** - Understand development guidelines
4. **Consult CHANGE_IMPACT_GUIDE.md** - Understand the impact of proposed changes
5. **Check KNOWN_ISSUES.md** - Be aware of existing problems
6. **Review TECHNICAL_DEBT.md** - Understand architectural considerations

---

## 16. System Statistics

- **Total Database Tables:** 65+
- **Total Migration Files:** 68
- **Total Routes:** 200+
- **Authentication Guards:** 6 (web, admin, staff, teacher, student, client)
- **User Roles:** 6 (Public, Student, Teacher, Staff, Admin, Client)
- **Core Modules:** 8
- **Controllers:** 60+
- **Models:** 70+
- **Middleware:** 11

---

## 17. System Status

**Overall System Health:** ✅ Production-Ready

**Last Major Update:** August 2026

**Current Version:** Laravel 12.5

**Database Version:** Based on migration timestamps (latest: August 12, 2026)

**Status:** The system is fully functional with comprehensive features for school management and e-learning delivery. Some technical debt exists but does not impact core functionality.

---

*This document serves as the foundation for understanding the ESA School Management and E-Learning System. For detailed technical information, refer to the specific documentation files listed in Section 14.*