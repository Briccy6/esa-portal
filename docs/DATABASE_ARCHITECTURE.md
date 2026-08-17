# Database Architecture

## Overview

The ESA School Management and E-Learning System uses a comprehensive relational database schema designed to support multi-branch educational operations, digital learning, financial management, and assessment systems.

**Database Statistics:**
- **Total Tables:** 65+
- **Migration Files:** 68
- **Primary Storage Engine:** SQLite (development), MySQL/PostgreSQL (production)
- **Character Set:** UTF-8
- **Naming Convention:** Laravel snake_case

---

## Database Design Principles

### 1. Normalization
The database follows Third Normal Form (3NF) principles:
- **Atomic Values:** All columns contain atomic values
- **No Repeating Groups:** Related data separated into related tables
- **No Transitive Dependencies:** Non-key attributes depend only on the primary key

### 2. Relationship Patterns
- **One-to-Many:** Parent-child relationships with foreign keys
- **Many-to-Many:** Pivot tables with composite primary keys
- **Self-Referencing:** Hierarchical data structures
- **Polymorphic:** Flexible relationships using `*_type` and `*_id` columns

### 3. Data Integrity
- **Foreign Key Constraints:** Enforce referential integrity
- **Unique Constraints:** Prevent duplicate data
- **Cascade Operations:** Automated data cleanup on deletions
- **Default Values:** Ensure data consistency

### 4. Performance Optimization
- **Strategic Indexing:** Composite indexes for common query patterns
- **Data Types:** Appropriate types for storage efficiency
- **JSON Fields:** Flexible storage for evolving requirements

---

## Database Schema Organization

### Category 1: User Management (6 tables)
### Category 2: Academic Structure (8 tables)
### Category 3: Assessment System (12 tables)
### Category 4: Financial System (11 tables)
### Category 5: Content Management (5 tables)
### Category 6: Live Learning (8 tables)
### Category 7: Report Cards (4 tables)
### Category 8: System Tables (3 tables)
### Category 9: Authentication (4 tables)

---

## Category 1: User Management

### users
**Purpose:** General user accounts (appears to be for students/clients)

**Key Relationships:**
- Belongs to: `courses`, `academic_years`
- Many-to-Many: `courses` (via `course_user`)

**Important Fields:**
- `phone` (unique), `email` (unique), `reg_number` (unique)
- `course_id`, `academic_year_id`
- `passport_photo`, `id_document`, `result_slip`

### admins
**Purpose:** System administrators with full platform access

**Key Relationships:**
- Approves: `live_class_requests`, `report_cards`

**Important Fields:**
- `email` (unique)
- `remember_token`

### teachers
**Purpose:** Teaching staff members

**Key Relationships:**
- Belongs to: `branches`
- Many-to-Many: `courses`, `branches`

**Important Fields:**
- `teacher_id_number` (unique), `id_number` (unique), `email` (unique)
- `department` (ENUM), `employment_type` (ENUM)
- `branch_id`, `photo`, `salary`

### staff
**Purpose:** Non-teaching staff members

**Key Relationships:**
- Belongs to: `branches`
- Many-to-Many: `branches`

**Important Fields:**
- `email` (unique), `role`, `branch_id`
- `qr_code`, `photo`, `password`

### students
**Purpose:** Student enrollment and profile management

**Key Relationships:**
- Belongs to: `branches`, `courses`, `academic_years`
- Has one: `student_card`, `token`
- Has many: `marks`, `payments`, `student_transactions`, `student_subscriptions`

**Important Fields:**
- `email` (unique), `reg_number` (unique, format: ESAPCB89762025-2026)
- `branch_id`, `course_id`, `academic_year_id`
- `payed_registration_fees`, `discount`
- NESA fields: `nesa_registered`, `nesa_sdms_code`, `nesa_reg_number`

**Indexes:**
- `nesa_registered`, `nesa_sdms_code`, `nesa_reg_number`

### clients
**Purpose:** External users for subscriptions and exam access

**Key Relationships:**
- Has many: `client_subscriptions`, `client_transactions`, `client_tokens`

**Important Fields:**
- `email` (unique), `phone`
- `status`, `token_balance`

---

## Category 2: Academic Structure

### academic_years
**Purpose:** Define academic year periods

**Key Relationships:**
- Has many: `terms`, `courses`, `students`, `exams`

**Important Fields:**
- `year`, `starting_date`, `expiry_date`
- `is_ongoing`

### terms
**Purpose:** Academic terms within years (Term 1, 2, 3)

**Key Relationships:**
- Belongs to: `academic_years`
- Many-to-Many: `lessons`

**Important Fields:**
- `name`, `academic_year_id`
- `start_date`, `end_date`, `is_active`

**Constraints:**
- Unique: `[name, academic_year_id]`

### branches
**Purpose:** Physical/online school locations

**Key Relationships:**
- Has many: `students`, `teachers`, `staff`
- Many-to-Many: `courses`, `teachers`, `staff`

**Important Fields:**
- `name`, `location`, `branch_type`
- ('online', 'physical', 'hybrid')

### courses
**Purpose:** Academic courses/programs offered

**Key Relationships:**
- Belongs to: `academic_years`
- Many-to-Many: `users`, `teachers`, `branches`, `exams`

**Important Fields:**
- `course_name` (unique), `abbreviation`, `course_type`
- `academic_year_id`
- Fee fields: `registration_fee`, `tuition_fee`, `practical_fee`, `trip_fee`, `discount_fee`

### lessons
**Purpose:** Individual subjects/lessons within courses

**Key Relationships:**
- Belongs to: `teachers`, `courses`
- Has many: `lesson_units`, `marks` (via report_card_subjects)
- Many-to-Many: `terms`, `exams`

**Important Fields:**
- `teacher_id`, `course_id`, `title`
- `max_test_marks`, `max_exam_marks`, `lesson_weight`

### lesson_units
**Purpose:** Units/topics within lessons

**Key Relationships:**
- Belongs to: `lessons`, `terms`

**Important Fields:**
- `lesson_id`, `term_id`, `unit_name`
- `hours`, `course_level`

### timetables
**Purpose:** Schedule definitions

**Key Relationships:**
- Has many: `timetable_sessions`

**Important Fields:**
- `title`, `image`, `valid_from`, `valid_until`

**Indexes:**
- `[valid_from, valid_until]`

### timetable_sessions
**Purpose:** Individual scheduled sessions

**Key Relationships:**
- Belongs to: `timetables`, `lessons`

**Important Fields:**
- `timetable_id`, `lesson_id`, `day_of_week`
- `start_time`, `end_time`, `classroom`

**Indexes:**
- `[timetable_id, day_of_week, start_time]`

---

## Category 3: Assessment System

### exams
**Purpose:** Examination definitions

**Key Relationships:**
- Belongs to: `teachers`, `branches`, `academic_years`, `terms`
- Many-to-Many: `courses`, `lessons`
- Has many: `questions`, `marks`, `exam_attempts`

**Important Fields:**
- `title`, `subject` (nullable), `exam_type`
- `teacher_id`, `branch_id`, `academic_year_id`, `term_id`
- `class_date`, `exam_weight`, `total_marks`
- `exam_password` (unique), `is_active`

### questions
**Purpose:** Exam questions

**Key Relationships:**
- Belongs to: `exams`, `question_banks`
- Has many: `student_answers`, `exam_analytics`

**Important Fields:**
- `exam_id` (nullable), `question_bank_id` (nullable)
- `question_type`, `text`
- Options: `option_a`, `option_b`, `option_c`, `option_d`
- `correct_answer`, `correct_answers` (JSON)
- `marks`, `weight`, `difficulty_level`

### question_banks
**Purpose:** Reusable question repositories

**Key Relationships:**
- Belongs to: `teachers`, `courses`, `academic_years`
- Has many: `questions`

**Important Fields:**
- `name`, `description`, `teacher_id`
- `course_id`, `academic_year_id`, `is_active`

### marks
**Purpose:** Student examination marks

**Key Relationships:**
- Belongs to: `students`, `courses`, `branches`, `exams`, `grades`

**Important Fields:**
- `student_id`, `course_id`, `branch_id`, `exam_id`
- `value` (the mark/score), `grade_id`, `status`

### grades
**Purpose:** Grade definitions (A, B, C, D, F)

**Important Fields:**
- `name` (unique), `min_score`, `max_score`
- `description`, `color`, `gpa_points`, `is_passing`

### exam_attempts
**Purpose:** Track student exam attempts

**Key Relationships:**
- Belongs to: `exams`, `students`
- Has many: `exam_sessions`, `student_answers`

**Important Fields:**
- `exam_id`, `student_id`, `attempt_number`
- `started_at`, `finished_at`, `duration_minutes`
- `total_score`, `percentage`, `status`
- `ip_address`, `user_agent`, `violations` (JSON)

**Constraints:**
- Unique: `[exam_id, student_id, attempt_number]`

### exam_sessions
**Purpose:** Active exam session management

**Key Relationships:**
- Belongs to: `exams`, `students`, `exam_attempts`
- Has many: `anti_cheating_logs`

**Important Fields:**
- `exam_id`, `student_id`, `attempt_id`
- `session_start`, `last_activity`, `time_remaining_seconds`
- Anti-cheating: `is_fullscreen`, `tab_switches`, `fullscreen_violations`
- `question_order` (JSON), `answer_orders` (JSON)

### student_answers
**Purpose:** Student responses to questions

**Key Relationships:**
- Belongs to: `exam_attempts`, `questions`

**Important Fields:**
- `attempt_id`, `question_id`
- `answer`, `selected_options` (JSON)
- `score`, `is_correct`, `feedback`
- `needs_manual_grading`, `answered_at`

**Constraints:**
- Unique: `[attempt_id, question_id]`

### exam_settings
**Purpose:** Configuration for exam behavior

**Key Relationships:**
- Belongs to: `exams`

**Important Fields:**
- `exam_id`, `duration_minutes`
- `start_time`, `end_time`
- Randomization: `randomize_questions`, `randomize_answers`
- Display: `questions_per_page`, `show_results_immediately`
- Security: `require_fullscreen`, `disable_right_click`, `max_tab_switches`

### anti_cheating_logs
**Purpose:** Security violation tracking

**Key Relationships:**
- Belongs to: `exam_sessions`, `students`

**Important Fields:**
- `exam_session_id`, `student_id`
- `violation_type` (ENUM), `description`
- `occurred_at`, `metadata` (JSON)

### exam_analytics
**Purpose:** Question performance metrics

**Key Relationships:**
- Belongs to: `exams`, `questions`

**Important Fields:**
- `exam_id`, `question_id`
- `total_attempts`, `correct_attempts`, `success_rate`
- `average_time_seconds`, `difficulty_score`
- Option counts: `option_a_count`, `option_b_count`, etc.

### Pivot Tables
- **course_exam:** Links courses to exams
- **lesson_exam:** Links lessons to exams

---

## Category 4: Financial System

### payments
**Purpose:** General payment records

**Key Relationships:**
- Belongs to: `students`

**Important Fields:**
- `student_id`, `amount`, `payment_method`
- `transaction_id`, `status`, `description`

**Indexes:**
- `[student_id, status]`

### subscriptions
**Purpose:** Subscription plans/tiers

**Key Relationships:**
- Has many: `student_subscriptions`

**Important Fields:**
- `name`, `description`, `tokens_included`
- `price`, `tier`, `duration_days`
- `is_active`, `is_recommended`, `features` (JSON)

**Indexes:**
- `[tier, is_active]`

**Default Plans:**
- Basic: 25 tokens, 12,500 RWF
- Standard: 75 tokens, 37,500 RWF (recommended)
- Premium: 200 tokens, 100,000 RWF

### student_subscriptions
**Purpose:** Student subscription purchases

**Key Relationships:**
- Belongs to: `students`, `subscriptions`

**Important Fields:**
- `student_id`, `subscription_id`, `paid_amount`
- `payment_reference`, `payment_status`
- `starts_at`, `ends_at`, `is_active`

**Indexes:**
- `[student_id, is_active]`, `[payment_status]`

### tokens
**Purpose:** Student token balances

**Key Relationships:**
- Belongs to: `students`
- Has many: `token_transactions`

**Important Fields:**
- `student_id`, `balance`, `total_earned`, `total_spent`
- `last_earned_at`, `last_spent_at`

**Indexes:**
- `[student_id, balance]`

### token_transactions
**Purpose:** Token credit/debit history

**Key Relationships:**
- Belongs to: `tokens`
- Polymorphic to: `exams`, `subscriptions`, etc.

**Important Fields:**
- `token_id`, `amount`, `type` (ENUM: credit/debit)
- `description`, `source`, `balance_after`
- `referenceable_id`, `referenceable_type` (polymorphic)

**Indexes:**
- `[token_id, type]`, `[source]`, `[created_at]`

### expenses
**Purpose:** School expense tracking

**Important Fields:**
- `expense_date`, `expense_type`, `amount`
- `description`, `paid_by`, `paid_to`
- `payment_method`

### accounts (Double-Entry Accounting)
**Purpose:** Chart of accounts for financial management

**Key Relationships:**
- Has many: `journal_items`

**Important Fields:**
- `account_code` (unique), `account_name`
- `account_type` (ENUM), `normal_balance` (ENUM)
- `is_active`, `is_cash_account`, `cash_flow_category`
- `parent_account_id`, `level`

**Indexes:**
- `[account_type]`, `[is_active]`, `[is_cash_account]`, `[cash_flow_category]`

### journal_entries
**Purpose:** Accounting journal entries

**Key Relationships:**
- Has many: `journal_items`

**Important Fields:**
- `entry_date`, `reference_number` (unique)
- `description`, `total_amount`, `status`
- `created_by`, `posted_by`, `posted_at`

**Indexes:**
- `[entry_date]`, `[status]`, `[reference_number]`, `[created_by]`

### journal_items
**Purpose:** Individual journal entry line items

**Key Relationships:**
- Belongs to: `journal_entries`, `accounts`

**Important Fields:**
- `journal_entry_id`, `account_id`
- `debit_amount`, `credit_amount`
- `reference_type`, `reference_id`

**Indexes:**
- `[journal_entry_id]`, `[account_id]`, `[reference_type, reference_id]`

### student_transactions
**Purpose:** Flutterwave payment transactions

**Key Relationships:**
- Belongs to: `students`

**Important Fields:**
- `student_id`, `tx_ref` (unique), `transaction_id`
- `type`, `amount`, `currency`, `status`
- `meta` (JSON), `paid_at`

### payment_records
**Purpose:** Payment token generation

**Key Relationships:**
- Belongs to: `students`

**Important Fields:**
- `student_id`, `type`, `token` (unique)
- `expires_at`, `is_paid`

### course_term_fees
**Purpose:** Course-specific fee structures by term

**Key Relationships:**
- Belongs to: `courses`, `terms`

**Important Fields:**
- `course_id`, `term_id`, `amount`
- `description`, `is_active`

---

## Category 5: Content Management

### resources
**Purpose:** Educational resources (documents, videos)

**Key Relationships:**
- Belongs to: `teachers`, `courses`, `branches`, `lessons`

**Important Fields:**
- `teacher_id`, `course_id`, `branch_id`, `lesson_id`
- `youtube_link`, `document_path`
- `price`, `is_public`, `is_free`
- `watermark_path`, `watermark_applied`

### blogs
**Purpose:** Blog/content management

**Key Relationships:**
- Has many: `blog_images`, `blog_documents`
- Many-to-Many: `blogs` (related)

**Important Fields:**
- `title`, `slug` (unique), `content`
- `status` (ENUM), `featured`, `view_count`
- `published_at`, `meta_title`, `meta_description`

**Indexes:**
- `[status, featured]`, `[published_at]`

### blog_images
**Purpose:** Blog post images

**Key Relationships:**
- Belongs to: `blogs`

**Important Fields:**
- `blog_id`, `image_path`, `caption`
- `alt_text`, `sort_order`, `featured`

**Indexes:**
- `[blog_id, sort_order]`, `[featured]`

### blog_documents
**Purpose:** Blog post documents

**Key Relationships:**
- Belongs to: `blogs`

**Important Fields:**
- `blog_id`, `document_path`, `document_name`
- `file_size`, `mime_type`

**Indexes:**
- `[blog_id]`

### announcements
**Purpose:** System announcements

**Important Fields:**
- `title`, `audience` (ENUM), `content`
- `attachment`, `sender`

**Indexes:**
- `[audience, created_at]`

---

## Category 6: Live Learning

### live_classes
**Purpose:** Online live class sessions

**Key Relationships:**
- Belongs to: `teachers`
- Many-to-Many: `branches`, `courses`, `lessons`

**Important Fields:**
- `title`, `description`, `meet_link`
- `start_time`, `end_time`, `teacher_id`
- `access_type`, `public_fee`, `currency`
- Access control: `max_participants`, `auto_approve_online_students`
- Features: `recording_enabled`, `chat_enabled`, `screen_share_enabled`

### live_class_signals
**Purpose:** WebRTC signaling for live classes

**Important Fields:**
- `live_class_id`, `from_peer`, `to_peer`
- `type` (ENUM), `payload` (JSON)

**Indexes:**
- `[live_class_id, to_peer, created_at]`

### live_class_participants
**Purpose:** Track live class attendance

**Key Relationships:**
- Belongs to: `live_classes`

**Important Fields:**
- `live_class_id`, `user_id`, `user_type`
- `email`, `name`, `joined_at`, `left_at`
- `is_active`, `device_info` (JSON), `ip_address`

**Indexes:**
- `[live_class_id, user_type, is_active]`, `[email, is_active]`

### live_class_requests
**Purpose:** Public access requests for live classes

**Key Relationships:**
- Belongs to: `live_classes`, `admins`

**Important Fields:**
- `live_class_id`, `user_id`, `user_type`
- `email`, `name`, `phone`, `reason`
- `status`, `approved_by`, `approved_at`

**Indexes:**
- `[live_class_id, status]`

### live_class_payments
**Purpose:** Payments for public live class access

**Key Relationships:**
- Belongs to: `live_classes`

**Important Fields:**
- `live_class_id`, `user_type`, `email`, `name`
- `amount`, `currency`, `transaction_id` (unique)
- `status`, `payment_gateway`, `gateway_response` (JSON)

### teacher_dailies
**Purpose:** Daily teacher activity logs

**Key Relationships:**
- Belongs to: `teachers`, `courses`, `lessons`, `lesson_units`

**Important Fields:**
- `teacher_id`, `course_id`, `lesson_id`, `unit_id`
- `activity`, `hours_studied`, `total_students`
- `absences`, `quiz_link`, `marking_guide`
- `total_marks`, `challenges`, `comment`, `date`

### Pivot Tables
- **live_class_branches:** Links live classes to branches
- **live_class_courses:** Links live classes to courses
- **live_class_lessons:** Links live classes to lessons

---

## Category 7: Report Cards

### school_informations
**Purpose:** School details for report cards

**Important Fields:**
- `school_name`, `address`, `phone`, `email`
- `logo_path`, `logo` (BINARY), `motto`
- `vision`, `mission`, `principal_name`
- `principal_signature`, `stamp_signature`
- `is_active`

### report_cards
**Purpose:** Student report card records

**Key Relationships:**
- Belongs to: `students`, `academic_years`, `terms`, `courses`, `admins`
- Has many: `report_card_subjects`

**Important Fields:**
- `student_id`, `academic_year_id`, `term_id`, `course_id`
- `total_marks`, `average_marks`, `class_position`
- `grade`, `remarks`, `status`
- `approved_by`, `approved_at`

**Constraints:**
- Unique: `[student_id, academic_year_id, term_id, course_id]`

### report_card_subjects
**Purpose:** Subject-wise marks in report cards

**Key Relationships:**
- Belongs to: `report_cards`, `lessons`
- Has many: `report_card_test_marks`

**Important Fields:**
- `report_card_id`, `lesson_id`, `subject_name`
- `exam_mark`, `test_total`, `test_average`
- `total_mark`, `grade`, `remarks`

**Constraints:**
- Unique: `[report_card_id, lesson_id]`

### report_card_test_marks
**Purpose:** Individual test marks for report cards

**Key Relationships:**
- Belongs to: `report_card_subjects`, `exams`

**Important Fields:**
- `report_card_subject_id`, `exam_id`, `test_name`
- `original_mark`, `max_mark`, `normalized_mark`
- `weight`

### student_cards
**Purpose:** Student ID card generation

**Key Relationships:**
- Belongs to: `students`

**Important Fields:**
- `student_id` (unique), `reg_number`
- `course`, `branch`, `passport_photo`
- `academic_year`, `card_generated_by`, `generated_at`

---

## Category 8: System Tables

### cache, cache_locks
**Purpose:** Laravel cache system

**Important Fields:**
- `key` (Primary Key), `value`, `expiration`

### jobs, job_batches, failed_jobs
**Purpose:** Laravel queue system

**Important Fields:**
- Queue management, job tracking, error handling

### locks
**Purpose:** Distributed locking

**Important Fields:**
- `key` (Primary Key), `value`, `owner`, `expiration`

---

## Category 9: Authentication

### password_resets
**Purpose:** User password reset tokens

**Important Fields:**
- `email` (indexed), `token`, `created_at`

### admin_password_resets
**Purpose:** Admin password reset tokens

**Important Fields:**
- `email` (indexed), `token`, `created_at`

### email_verifications
**Purpose:** Email verification codes

**Important Fields:**
- `email` (unique), `code`, `expires_at`
- `verified`, `created_at`, `updated_at`

### sessions
**Purpose:** User session management

**Important Fields:**
- `id` (Primary Key), `user_id` (indexed)
- `ip_address`, `user_agent`, `payload`
- `last_activity` (indexed)

---

## Database Relationship Map

### User Hierarchy
```
Admins (System administrators)
├── Teachers (Teaching staff)
│   └── Assigned to branches, courses, lessons
├── Staff (Non-teaching staff)
│   └── Assigned to branches
└── Students (Learners)
    └── Enrolled in branches, courses, academic years
```

### Academic Structure
```
Academic Years
└── Terms
    └── Courses
        └── Lessons
            └── Lesson Units
```

### Assessment Flow
```
Question Banks → Questions → Exams → Exam Attempts → Student Answers
                    ↓              ↓
                Exam Settings  Exam Sessions
                    ↓              ↓
              Anti-Cheating Logs  Exam Analytics
                    ↓
                Marks → Grades → Report Cards
```

### Financial Flow
```
Subscriptions → Student Subscriptions → Tokens → Token Transactions
Payments → Student Transactions → Journal Entries → Journal Items
Accounts ← Journal Items
Expenses
```

### Content Structure
```
Teachers → Resources (linked to courses, branches, lessons)
Blogs → Blog Images + Blog Documents + Related Blogs
Announcements
```

### Live Class Ecosystem
```
Live Classes
├── Pivots: branches, courses, lessons
├── Participants (attendance tracking)
├── Requests (public access)
├── Payments (public access fees)
└── Signals (WebRTC signaling)
```

---

## Database Constraints and Indexes

### Unique Constraints
- User identifiers: emails, phone numbers, registration numbers
- Academic identifiers: course names, exam passwords
- Financial identifiers: transaction references, payment tokens
- Content identifiers: blog slugs, account codes

### Foreign Key Cascade Rules
- **Cascade Delete:** Most related data deleted when parent is deleted
- **Set Null:** Some relationships preserve data with null references
- **Restrict:** Critical relationships prevent deletion

### Composite Indexes
- Performance optimization for common query patterns
- Multi-column indexes for complex queries
- Covering indexes for frequently accessed data

---

## Data Types Usage

### Common Data Types
- **BIGINT:** Primary keys, foreign keys, large IDs
- **VARCHAR:** Strings, emails, names, codes
- **TEXT/LONGTEXT:** Descriptions, content, JSON payloads
- **DECIMAL:** Financial amounts (various precision)
- **INTEGER/TINYINT:** Counts, IDs, enums
- **BOOLEAN:** Flags and status indicators
- **DATE/DATETIME/TIMESTAMP:** Dates and times
- **ENUM:** Restricted value sets
- **JSON:** Flexible data storage
- **BINARY:** Binary data (logos, signatures)

### Financial Precision
- **DECIMAL(8,2):** Standard amounts (tokens, subscriptions)
- **DECIMAL(10,2):** Larger amounts (fees, payments)
- **DECIMAL(12,2):** Very large amounts (expenses)
- **DECIMAL(15,2):** Accounting precision

---

## Database Performance Considerations

### Strengths
1. **Comprehensive Indexing:** Strategic indexes for performance
2. **Proper Relationships:** Well-defined foreign key relationships
3. **Data Type Optimization:** Appropriate types for storage efficiency
4. **Flexible JSON:** Schema evolution without migrations

### Areas for Improvement
1. **Missing Composite Indexes:** Some query patterns may need optimization
2. **Large JSON Fields:** May impact performance at scale
3. **No Partitioning:** Large tables may benefit from partitioning
4. **Query Optimization:** Some complex queries may need refinement

### Scaling Considerations
1. **Read Replicas:** Offload reporting queries
2. **Database Sharding:** Multi-tenant data separation
3. **Connection Pooling:** Manage database connections efficiently
4. **Query Caching:** Cache frequently accessed data

---

## Database Security

### Access Control
- **Application-Level:** Laravel handles all database access
- **No Direct Database Access:** Application-only database access
- **Environment-Specific Credentials:** Separate credentials per environment

### Data Protection
- **Password Hashing:** Laravel bcrypt for user passwords
- **Sensitive Data:** PII stored in appropriate columns
- **Financial Data:** Double-entry system ensures integrity

### Backup Strategy
- **Migration-Based:** Schema versioned in migrations
- **Seed Data:** Reference data in seeders
- **No Built-in Backup:** Application-level backup strategy needed

---

## Database Maintenance

### Migration Strategy
- **Chronological Order:** Migrations run in timestamp order
- **Reversible:** Most migrations include rollback logic
- **Version Control:** All schema changes in version control

### Data Seeding
- **Default Data:** Subscription plans, grades, etc.
- **Test Data:** Faker for development/testing
- **Reference Data:** Static data for system operation

### Monitoring
- **Query Performance:** Monitor slow queries
- **Index Usage:** Track index effectiveness
- **Table Sizes:** Monitor table growth
- **Connection Pool:** Monitor connection usage

---

## Database Development Guidelines

### When Adding Tables
1. Create migration with appropriate timestamp
2. Define relationships with foreign keys
3. Add indexes for common query patterns
4. Include timestamps (`created_at`, `updated_at`)
5. Create corresponding Eloquent model

### When Modifying Tables
1. Create new migration (never edit existing migrations)
2. Use change operations for schema modifications
3. Consider data migration for existing records
4. Test rollback capability
5. Update model relationships

### When Adding Indexes
1. Analyze query patterns first
2. Use composite indexes for multi-column queries
3. Consider index maintenance overhead
4. Monitor index usage after deployment
5. Remove unused indexes

### When Working with JSON
1. Use for flexible, evolving data
2. Consider separate tables for frequently accessed data
3. Use JSON indexes for query performance
4. Document JSON structure in comments
5. Validate JSON data at application level

---

*This document provides a comprehensive overview of the database architecture. For detailed table documentation, see DATABASE_TABLE_REFERENCE.md.*