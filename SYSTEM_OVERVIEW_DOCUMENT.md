# ESA School Management and E-Learning System – Overview Document

## Contact Information

- Owner: Hirwa Alain Brian
- Contact Number: 0794900225

---

## 1. What this system is

This project is a multi-user school management system built for running an educational institution with both traditional school operations and digital learning features. It supports:

- Student registration and profile management
- Academic management such as courses, lessons, units, and timetables
- Exams and assessment handling
- Attendance and reporting
- Fees, payments, and financial tracking
- Teacher and staff portals
- Public-facing pages for exams, fees, and educational content
- Digital learning features such as live classes, resources, student subscriptions, and tokens

In simple terms, this system is designed to help a school manage students, staff, finance, exams, and online learning from one platform.

---

## 2. Main purpose of the system

The system is meant to:

- Organize school administration in a centralized place
- Allow staff to manage academic and operational tasks
- Give students access to their academic information and learning content
- Support online learning and digital assessments
- Enable public access to selected school information such as exam listings, fee schedules, and public educational content
- Support financial tracking for school fees, resources, subscriptions, and other payments

---

## 3. Who uses the system

The platform has several user types. Each user type has a different role and access level.

### 3.1 Public Visitors
These users do not need an account.

They can:
- View public announcements
- Browse public exams
- View fee information and course details
- Access public educational content such as Babyeyi materials
- Download public content where allowed

### 3.2 Students
Students are authenticated users who belong to the school.

They can:
- Log in through the student portal
- View their courses and academic records
- See grades and marks
- View invoices and payment status
- Access purchased learning resources
- Pay for resources or related services
- Take exams (where permitted)
- View their own attendance and academic progress

### 3.3 Teachers
Teachers are authenticated educators.

They can:
- Access a dedicated teacher dashboard
- View their assigned courses and lessons
- Manage question banks
- Create and manage educational content
- Manage live classes and participation requests
- View the students attached to their teaching areas

### 3.4 Staff
Staff users are internal administration users. The code supports several staff roles.

#### a) DOS
The DOS role is mainly for school operational oversight.

Typical responsibilities include:
- Monitoring school-wide academic and operational data
- Reviewing attendance-related information
- Viewing school dashboards and monitoring progress

#### b) Accountant
The Accountant role focuses on school finance and financial monitoring.

Typical responsibilities include:
- Reviewing financial performance by branch
- Tracking expenses and income summaries
- Monitoring financial dashboards for assigned branches

#### c) Secretary
The Secretary role focuses on student registration and student support processes.

Typical responsibilities include:
- Assisting with student registration and follow-up
- Monitoring students who need attention
- Reviewing pending registrations and low-token or low-balance student records

### 3.5 Administrators (Admin)
Administrators are the highest-level users.

They can:
- Manage students, teachers, and staff
- Create and manage academic years, branches, courses, terms, and fees
- Configure lessons, timetables, report card settings, and announcements
- Manage attendance, exams, payments, and financial reporting
- Manage clients, subscriptions, tokens, and public content
- Control the overall system configuration and major business operations

### 3.6 Clients
Clients are external users who may access parts of the platform related to exams, subscriptions, or token-based services.

They can:
- Use the public/external portal
- Buy or manage subscriptions or tokens
- Access exam-related features depending on their account status

---

## 4. Core modules of the system

### 4.1 Student Management
This is one of the core parts of the system.

The system manages:
- Student personal details
- Registration data
- Branch and course assignment
- Academic year information
- NESA registration-related data
- Student academic records and financial status

This module is central because many other modules depend on student records.

### 4.2 Course and Academic Management
The system allows the school to organize learning by course.

It supports:
- Courses
- Lessons
- Units
- Terms
- Academic years
- Branch-based academic grouping

This makes it possible to structure school learning content and assign teachers and resources properly.

### 4.3 Timetable and Lesson Planning
The platform includes timetable-related operations.

It supports:
- Viewing timetables
- Filtering timetables by teacher, course, branch, or day
- Exporting or printing timetable data
- Managing lesson sessions

### 4.4 Fees and Payments
This is a very important part of the system.

It supports:
- School fee tracking
- Registration fee information
- Student payments
- Payment status monitoring
- Accounting reports and financial summaries
- Student payment transcripts and fee-related records

The system also supports different payment types such as student fees, resources, subscriptions, live class access, and exam purchases.

### 4.5 Exams and Assessments
The system includes a full exam workflow.

It supports:
- Public exam listings
- Student exam access
- Exam taking and submission
- Exam payment flow where necessary
- Exam token or subscription-based access
- Result handling and score visibility

### 4.6 Attendance
Attendance is tracked within the school system.

This allows staff and administrators to:
- Record or review attendance
- Monitor recent attendance patterns
- View attendance summaries for management review

### 4.7 Resources and Learning Content
The system includes content management for educational materials.

It supports:
- Student access to learning resources
- Resource download and payment flow
- Teacher-managed content areas
- Content organization by course or lesson

### 4.8 Live Classes and Digital Learning
The platform also supports live-class functionality.

It allows:
- Teachers to manage live classes
- Students to join or request access
- Teachers to approve or reject participation requests
- Real-time class flow and management

### 4.9 Reports and Documents
The system produces and manages several important documents and reports.

Examples include:
- Report card settings and generation support
- Payment transcripts
- Financial statements and accounting reports
- Student and staff data exports

---

## 5. How the main business flows work

### 5.1 Student registration flow
A student can be registered into the system through the school workflow.

The process usually involves:
1. Capturing student personal and academic details
2. Assigning the student to a branch and course
3. Linking the student to an academic year
4. Processing registration-related fees or approvals
5. Giving the student access to the student portal

### 5.2 Fee and payment flow
After registration, the student may need to pay fees or purchase services.

Typical flow:
1. Student or visitor sees fee schedules or course fee information
2. Payment is initiated
3. Payment status is updated in the transaction system
4. Access to features or services is granted based on the payment status

### 5.3 Teacher workflow
A teacher can:
1. Log in to the teacher portal
2. View assigned courses and lessons
3. Create or manage exam content and class resources
4. Run live classes and manage participation
5. Support student learning through the academic modules

### 5.4 Admin workflow
An administrator can:
1. Manage the school structure and academic setup
2. Control staff and teacher access
3. Monitor financial data and school records
4. Review exams, reports, and student information
5. Oversee the operations of the entire system

---

## 6. Owner and contact information

This system is owned by:
- Owner Name: Hirwa Alain Brian
- Contact Number: 0794900225

These details can be included in the document footer or shared as the primary contact information for anyone who needs to reach the website owner.

---

## 7. Important technical design note

The system uses multiple authentication guards rather than a single simple user model. In other words, the application is built to handle different user groups separately:

- Admin guard
- Staff guard
- Student guard
- Teacher guard
- Client guard

This design allows each user type to have its own login flow and portal experience while keeping the platform unified.

---

## 7. What makes this system different from a basic school system

This project is more advanced than a standard school record system because it combines:

- School administration
- Finance and accounting
- Student portals
- Teacher portals
- Public-facing educational content
- Digital exams and subscription-based features
- Live class and content delivery capabilities

This means the system is not only a record keeper; it is also a full digital learning and school operations platform.

---

## 8. Simple summary for a new person

If someone new wants to understand this system quickly, the easiest way is:

- Think of it as a school management system with a strong digital learning layer.
- Admins run the school operations.
- Staff support operations, finance, registrations, and monitoring.
- Teachers teach and manage classes.
- Students access learning and academic information.
- Public visitors can view selected educational and fee-related information.

---

## 9. Recommended way to explain the system in one sentence

This system is an integrated school administration and e-learning platform that helps manage students, teachers, finances, exams, attendance, resources, and digital learning experiences in one place.

---

## 10. Final note

This document gives a practical overview of the system as implemented in the current codebase. It is intended to help a new person or stakeholder understand what the platform does, who uses it, and how the major parts fit together.
