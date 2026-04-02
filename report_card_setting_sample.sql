-- SQL Command to Create Sample Report Card Setting
-- Run this command in your database to create a sample report card availability setting

INSERT INTO report_card_settings (
    academic_year, 
    term, 
    report_card_available_at, 
    description, 
    is_active, 
    created_at, 
    updated_at
) VALUES (
    '2025-2026', 
    'Term 1', 
    '2026-04-10 09:00:00',  -- Set this to your desired release date/time
    'Term 1 report cards will be available after final examinations are completed and marked by teachers.', 
    1,  -- 1 means active, 0 means inactive
    NOW(), 
    NOW()
);

-- Alternative: Create a setting for immediate testing (available now)
-- Uncomment and run this if you want to test immediately

-- INSERT INTO report_card_settings (
--     academic_year, 
--     term, 
--     report_card_available_at, 
--     description, 
--     is_active, 
--     created_at, 
--     updated_at
-- ) VALUES (
--     '2025-2026', 
--     'Term 1', 
--     NOW() - INTERVAL 1 HOUR,  -- Available 1 hour ago
--     'Test setting - report cards are now available for testing purposes.', 
--     1, 
--     NOW(), 
--     NOW()
-- );

-- Check if the setting was created successfully
-- SELECT * FROM report_card_settings ORDER BY created_at DESC LIMIT 1;
