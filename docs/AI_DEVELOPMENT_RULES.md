# AI Development Rules

## Overview

This document defines the mandatory rules and guidelines that AI agents must follow when working on the ESA School Management and E-Learning System. These rules are designed to prevent system breakage, maintain data integrity, and ensure consistent development practices.

---

## 1. FUNDAMENTAL PRINCIPLES

### 1.1 Never Invent Functionality
- **RULE:** Do not add features that are not requested or implied by the task
- **RATIONALE:** Unplanned features can break existing workflows and confuse users
- **ENFORCEMENT:** Cross-reference requirements with existing functionality before implementation

### 1.2 Never Remove Existing Functionality Without Approval
- **RULE:** Do not delete or disable existing features without explicit user approval
- **RATIONALE:** Users may depend on existing functionality
- **ENFORCEMENT:** Comment out code instead of deleting, document removals

### 1.3 Never Break Existing Workflows
- **RULE:** Preserve all existing user journeys and business processes
- **RATIONALE:** Workflow breaks cause immediate user frustration
- **ENFORCEMENT:** Test affected workflows before and after changes

### 1.4 Never Assume Database Structure
- **RULE:** Always verify table structure before making database changes
- **RATIONALE:** Assumptions lead to data loss or corruption
- **ENFORCEMENT:** Read migration files and current schema before changes

### 1.5 Never Bypass Security Mechanisms
- **RULE:** Do not disable authentication, authorization, or validation
- **RATIONALE:** Security bypasses create vulnerabilities
- **ENFORCEMENT:** Security review for all changes

---

## 2. DATABASE DEVELOPMENT RULES

### 2.1 Database Changes
- **RULE:** Never modify existing migration files
- **RULE:** Always create new migrations for schema changes
- **RULE:** Test migration rollback before deployment
- **RULE:** Include foreign key constraints in new migrations
- **RULE:** Add indexes for new query patterns
- **RULE:** Use appropriate data types (DECIMAL for money, etc.)

### 2.2 Table Creation
- **RULE:** Check existing tables before creating new ones
- **RULE:** Follow naming conventions (snake_case, plural)
- **RULE:** Include `created_at` and `updated_at` timestamps
- **RULE:** Define relationships explicitly with foreign keys
- **RULE:** Add unique constraints for natural keys

### 2.3 Query Writing
- **RULE:** Use Eloquent ORM instead of raw SQL when possible
- **RULE:** Use query scopes for reusable query logic
- **RULE:** Eager load relationships to prevent N+1 queries
- **RULE:** Use transactions for multi-table operations
- **RULE:** Validate data before database operations

### 2.4 Relationship Management
- **RULE:** Define relationships in Eloquent models
- **RULE:** Use appropriate relationship types (hasOne, hasMany, belongsTo, etc.)
- **RULE:** Consider cascade delete implications
- **RULE:** Use pivot tables for many-to-many relationships
- **RULE:** Document polymorphic relationships clearly

---

## 3. AUTHENTICATION AND AUTHORIZATION RULES

### 3.1 Authentication
- **RULE:** Never hardcode credentials
- **RULE:** Use Laravel's authentication guards correctly
- **RULE:** Do not bypass session validation
- **RULE:** Implement proper password hashing (bcrypt)
- **RULE:** Use environment variables for sensitive data

### 3.2 Authorization
- **RULE:** Always check user roles before granting access
- **RULE:** Use middleware for route protection
- **RULE:** Do not rely solely on frontend authorization
- **RULE:** Implement server-side permission checks
- **RULE:** Test authorization with different user roles

### 3.3 Guard Usage
- **RULE:** Use correct guard for each user type (admin, staff, teacher, student, client)
- **RULE:** Do not mix guards in route groups
- **RULE:** Configure guards in config/auth.php
- **RULE:** Test authentication for each guard separately

---

## 4. FRONTEND DEVELOPMENT RULES

### 4.1 Blade Templates
- **RULE:** Follow existing Blade template structure
- **RULE:** Use component layouts for consistency
- **RULE:** Escape all user-generated content ({{ }})
- **RULE:** Use directives for control structures (@if, @foreach, etc.)
- **RULE:** Organize templates by role and feature

### 4.2 JavaScript and Alpine.js
- **RULE:** Use Alpine.js for simple interactivity
- **RULE:** Follow existing Alpine.js patterns
- **RULE:** Do not mix Alpine.js with React in same component
- **RULE:** Use data attributes for configuration
- **RULE:** Handle errors gracefully in JavaScript

### 4.3 Styling with Tailwind CSS
- **RULE:** Use existing Tailwind utility classes
- **RULE:** Follow the established design system
- **RULE:** Do not add custom CSS without approval
- **RULE:** Use responsive classes appropriately
- **RULE:** Maintain consistency with existing UI

### 4.4 React Components
- **RULE:** Use React only for complex interactive components
- **RULE:** Follow existing React patterns in the codebase
- **RULE:** Use functional components with hooks
- **RULE:** Handle state appropriately (useState, useEffect)
- **RULE:** Integrate with Laravel APIs correctly

---

## 5. BACKEND DEVELOPMENT RULES

### 5.1 Controller Development
- **RULE:** Follow single responsibility principle
- **RULE:** Keep controllers thin (move logic to services)
- **RULE:** Use route model binding where appropriate
- **RULE:** Validate requests using Form Request classes
- **RULE:** Return consistent response formats

### 5.2 Service Layer
- **RULE:** Create service classes for complex business logic
- **RULE:** Keep services independent of HTTP concerns
- **RULE:** Use dependency injection for services
- **RULE:** Handle exceptions appropriately
- **RULE:** Log important service operations

### 5.3 Model Development
- **RULE:** Define relationships in models
- **RULE:** Use accessors and mutators for data transformation
- **RULE:** Implement model scopes for common queries
- **RULE:** Use model events for lifecycle hooks
- **RULE:** Fillable/guard mass assignment security

### 5.4 API Development
- **RULE:** Use consistent API response structure
- **RULE:** Implement proper HTTP status codes
- **RULE:** Use API resources for data transformation
- **RULE:** Implement rate limiting for public APIs
- **RULE:** Document API endpoints

---

## 6. ROLE-SPECIFIC RULES

### 6.1 Admin Changes
- **RULE:** Admin changes affect all users - test thoroughly
- **RULE:** Academic year changes affect data visibility
- **RULE:** Permission changes require testing with all roles
- **RULE:** System configuration changes need environment consideration

### 6.2 Student-Related Changes
- **RULE:** Student data is critical - backup before changes
- **RULE:** Registration changes affect financial calculations
- **RULE:** Academic performance changes need grade recalculation
- **RULE:** NESA integration requires compliance verification

### 6.3 Teacher-Related Changes
- **RULE:** Teacher assignments affect course delivery
- **RULE:** Question bank changes affect ongoing exams
- **RULE:** Live class changes need scheduling consideration
- **RULE:** Daily report changes affect attendance tracking

### 6.4 Financial Changes
- **RULE:** Financial calculations must be exact
- **RULE:** Payment changes require Flutterwave testing
- **RULE:** Accounting changes must maintain double-entry balance
- **RULE:** Token changes affect user access

---

## 7. TESTING RULES

### 7.1 Before Implementation
- **RULE:** Identify affected modules and user roles
- **RULE:** Review existing tests for related functionality
- **RULE:** Plan test cases for new functionality
- **RULE:** Consider edge cases and error conditions

### 7.2 During Implementation
- **RULE:** Write tests alongside code (TDD preferred)
- **RULE:** Test both success and failure scenarios
- **RULE:** Test with different user roles
- **RULE:** Test database transactions

### 7.3 After Implementation
- **RULE:** Run existing test suite
- **RULE:** Test new functionality manually
- **RULE:** Test affected user workflows
- **RULE:** Performance test for database queries
- **RULE:** Security test for authorization

---

## 8. INTEGRATION RULES

### 8.1 Flutterwave Integration
- **RULE:** Never hardcode API keys
- **RULE:** Test payment callbacks thoroughly
- **RULE:** Handle payment failures gracefully
- **RULE:** Verify transaction references are unique
- **RULE:** Log all payment interactions

### 8.2 Email Integration
- **RULE:** Use Markdown emails for consistency
- **RULE:** Test email rendering in multiple clients
- **RULE:** Handle email delivery failures
- **RULE:** Queue email sending for performance
- **RULE:** Include unsubscribe options where appropriate

### 8.3 File Storage Integration
- **RULE:** Validate file types and sizes
- **RULE:** Use secure file naming
- **RULE:** Implement file access controls
- **RULE:** Handle storage failures gracefully
- **RULE:** Clean up temporary files

### 8.4 External API Integration
- **RULE:** Cache API responses where appropriate
- **RULE:** Handle API failures gracefully
- **RULE:** Implement rate limiting
- **RULE:** Use environment variables for API keys
- **RULE**. Log API interactions

---

## 9. SECURITY RULES

### 9.1 Data Security
- **RULE:** Never log sensitive data (passwords, tokens)
- **RULE:** Encrypt sensitive data at rest
- **RULE:** Use HTTPS in production
- **RULE:** Implement CSRF protection
- **RULE:** Validate and sanitize all user input

### 9.2 Access Control
- **RULE:** Implement principle of least privilege
- **RULE:** Use signed URLs for temporary access
- **RULE:** Invalidate sessions on logout
- **RULE:** Implement rate limiting
- **RULE:** Monitor for suspicious activity

### 9.3 Payment Security
- **RULE:** Never store full payment card data
- **RULE:** Use secure payment gateways
- **RULE:** Verify payment callbacks
- **RULE:** Implement payment fraud detection
- **RULE:** Log all payment transactions

---

## 10. PERFORMANCE RULES

### 10.1 Database Performance
- **RULE:** Use database indexes appropriately
- **RULE:** Avoid N+1 query problems
- **RULE:** Use query caching for expensive operations
- **RULE:** Optimize complex queries
- **RULE:** Monitor query performance

### 10.2 Application Performance
- **RULE:** Use lazy loading where appropriate
- **RULE:** Implement caching for expensive operations
- **RULE:** Optimize asset loading
- **RULE:** Use queue jobs for long-running tasks
- **RULE:** Monitor application response times

### 10.3 Frontend Performance
- **RULE:** Optimize images and assets
- **RULE:** Minimize JavaScript bundle size
- **RULE:** Use code splitting for large applications
- **RULE:** Implement lazy loading for components
- **RULE:** Monitor frontend performance

---

## 11. CODE QUALITY RULES

### 11.1 Code Style
- **RULE:** Follow PSR-12 coding standards
- **RULE:** Use Laravel Pint for code formatting
- **RULE:** Write descriptive variable and function names
- **RULE:** Add comments for complex logic
- **RULE:** Keep functions focused and small

### 11.2 Error Handling
- **RULE:** Use try-catch blocks appropriately
- **RULE:** Log errors with context
- **RULE:** Provide user-friendly error messages
- **RULE:** Handle exceptions at appropriate levels
- **RULE:** Never expose stack traces to users

### 11.3 Logging
- **RULE:** Log important system events
- **RULE:** Use appropriate log levels (debug, info, warning, error)
- **RULE:** Include context in log messages
- **RULE:** Monitor logs regularly
- **RULE:** Implement log rotation

---

## 12. DOCUMENTATION RULES

### 12.1 Code Documentation
- **RULE:** Document complex business logic
- **RULE:** Add PHPDoc blocks for functions and classes
- **RULE:** Document non-obvious algorithm choices
- **RULE:** Keep documentation up to date
- **RULE:** Document breaking changes

### 12.2 API Documentation
- **RULE:** Document all API endpoints
- **RULE:** Include request/response examples
- **RULE:** Document authentication requirements
- **RULE:** Document error responses
- **RULE:** Keep API documentation current

### 12.3 System Documentation
- **RULE:** Update system documentation for major changes
- **RULE:** Document configuration changes
- **RULE:** Document deployment procedures
- **RULE:** Document troubleshooting steps
- **RULE:** Maintain change logs

---

## 13. DEPLOYMENT RULES

### 13.1 Pre-Deployment
- **RULE:** Run all tests
- **RULE:** Check for deprecated features
- **RULE:** Review database migrations
- **RULE:** Test in staging environment
- **RULE:** Create deployment rollback plan

### 13.2 Deployment
- **RULE:** Use zero-downtime deployment if possible
- **RULE:** Run database migrations carefully
- **RULE:** Clear application cache
- **RULE:** Monitor deployment logs
- **RULE:** Verify deployment success

### 13.3 Post-Deployment
- **RULE:** Monitor system performance
- **RULE:** Check error logs
- **RULE:** Verify critical functionality
- **RULE:** Monitor user feedback
- **RULE:** Prepare for quick rollback if needed

---

## 14. CHANGE MANAGEMENT RULES

### 14.1 Impact Analysis
- **RULE:** Always perform impact analysis before changes
- **RULE:** Identify affected user roles
- **RULE:** Identify affected database tables
- **RULE:** Identify affected routes and controllers
- **RULE:** Identify affected business workflows

### 14.2 Change Planning
- **RULE:** Plan changes before implementation
- **RULE:** Consider backward compatibility
- **RULE:** Plan data migration if needed
- **RULE:** Communicate changes to stakeholders
- **RULE:** Schedule changes appropriately

### 14.3 Change Execution
- **RULE:** Implement changes incrementally
- **RULE:** Test each increment
- **RULE:** Commit changes frequently
- **RULE:** Document changes in commit messages
- **RULE**. Monitor for issues

---

## 15. ROLLBACK RULES

### 15.1 When to Rollback
- **RULE:** Rollback immediately if critical functionality breaks
- **RULE:** Rollback if data corruption occurs
- **RULE:** Rollback if security vulnerabilities are introduced
- **RULE:** Rollback if performance degrades significantly
- **RULE:** Rollback if user experience is severely impacted

### 15.2 Rollback Procedure
- **RULE:** Have rollback plan ready before deployment
- **RULE:** Test rollback procedure
- **RULE:** Communicate rollback to stakeholders
- **RULE:** Document rollback reasons
- **RULE:** Analyze rollback causes

---

## 16. COLLABORATION RULES

### 16.1 Code Review
- **RULE:** All changes should be reviewed
- **RULE:** Review for security issues
- **RULE:** Review for performance issues
- **RULE:** Review for code quality
- **RULE:** Review for documentation completeness

### 16.2 Communication
- **RULE:** Communicate breaking changes early
- **RULE:** Document decisions clearly
- **RULE:** Ask questions when uncertain
- **RULE**: Share knowledge with team
- **RULE**: Provide context for changes

---

## 17. CRITICAL RULES SUMMARY

### Must Always Do
1. ✅ Read existing documentation before making changes
2. ✅ Test changes with all affected user roles
3. ✅ Backup data before database changes
4. ✅ Use version control for all changes
5. ✅ Follow existing code patterns and conventions
6. ✅ Test rollback procedures
7. ✅ Document changes clearly

### Must Never Do
1. ❌ Modify existing migration files
2. ❌ Hardcode credentials or sensitive data
3. ❌ Bypass authentication or authorization
4. ❌ Delete existing functionality without approval
5. ❌ Assume database structure
6. ❌ Ignore error messages
7. ❌ Deploy without testing

---

## 18. SPECIFIC TECHNOLOGY RULES

### 18.1 Laravel Specific
- **RULE:** Use Laravel's built-in features when available
- **RULE:** Follow Laravel conventions and best practices
- **RULE:** Use Laravel's service container for dependency injection
- **RULE:** Use Laravel's event system for decoupling
- **RULE:** Use Laravel's queue system for background jobs

### 18.2 Database Specific
- **RULE:** Use Eloquent ORM instead of raw SQL
- **RULE:** Use query builder for complex queries
- **RULE:** Use database transactions for multi-table operations
- **RULE:** Use database indexes for performance
- **RULE:** Use foreign key constraints for data integrity

### 18.3 Frontend Specific
- **RULE:** Use Tailwind CSS for styling
- **RULE:** Use Alpine.js for simple interactivity
- **RULE:** Use React for complex components
- **RULE:** Use Blade templates for server-side rendering
- **RULE:** Use Vite for asset compilation

---

## 19. ENVIRONMENT-SPECIFIC RULES

### 19.1 Development Environment
- **RULE:** Use SQLite for local development
- **RULE:** Enable debug mode in development
- **RULE:** Use log driver for emails in development
- **RULE:** Use detailed error reporting
- **RULE:** Use seed data for testing

### 19.2 Production Environment
- **RULE:** Use MySQL or PostgreSQL for production
- **RULE:** Disable debug mode in production
- **RULE:** Use real email service in production
- **RULE:** Use generic error messages
- **RULE:** Use environment-specific configuration

---

## 20. COMPLIANCE CHECKLIST

Before committing any changes, verify:

- [ ] Documentation has been read and understood
- [ ] Existing functionality has been tested
- [ ] New functionality has been tested
- [ ] All affected user roles have been tested
- [ ] Database changes are in new migrations
- [ ] Security implications have been considered
- [ ] Performance implications have been considered
- [ ] Error handling has been implemented
- [ ] Logging has been added where appropriate
- [ ] Documentation has been updated
- [ ] Code follows existing patterns
- [ ] Tests have been written/updated
- [ ] Rollback plan exists
- [ ] Peer review has been completed

---

## 21. VIOLATION CONSEQUENCES

Violating these rules may result in:

- **System Instability:** Unpredictable behavior and crashes
- **Data Loss:** Critical information may be lost
- **Security Vulnerabilities:** System may be compromised
- **User Dissatisfaction:** Poor user experience
- **Increased Technical Debt:** Harder to maintain
- **Deployment Failures:** Changes cannot be deployed
- **Rollback Requirements:** Emergency rollbacks needed

---

## 22. CONTINUOUS IMPROVEMENT

These rules should be:

- **Reviewed Regularly:** Update rules as system evolves
- **Improved Based on Experience:** Learn from past mistakes
- **Customized for Team:** Adapt to team workflow
- **Enforced Consistently:** Apply rules uniformly
- **Documented Clearly:** Keep rules accessible

---

## 23. FINAL RESPONSIBILITY

**AI Agents are responsible for:**

- Understanding these rules before making changes
- Asking clarifying questions when uncertain
- Following these rules consistently
- Reporting potential rule violations
- Suggesting rule improvements

**Human Developers are responsible for:**

- Providing clear requirements
- Reviewing AI agent changes
- Enforcing these rules
- Updating rules as needed
- Training AI agents on system specifics

---

*These rules are designed to ensure the reliability, security, and maintainability of the ESA School Management and E-Learning System. All AI agents must follow these rules unless explicitly instructed otherwise by authorized human developers.*