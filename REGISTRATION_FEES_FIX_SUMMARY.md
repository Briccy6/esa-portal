# Registration Fees & Term-Based Payment Fix - Complete Implementation

## Problem Statement
The system was charging registration fees for every term, but the requirement is:
1. Registration fees should only be charged **once per academic year**, not per term
2. Existing students should NOT pay registration again in different terms
3. New students joining mid-year (e.g., Term 2) still need to pay registration, but NOT for terms before they enrolled
4. The payment button was not passing `term_id` to the backend, so the system couldn't track which term's fees were being paid
