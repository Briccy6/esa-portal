<?php

echo "=== ESA DATABASE SETUP SCRIPT ===\n\n";

echo "This script will help you set up the database on Hostinger.\n";
echo "Please follow these steps:\n\n";

echo "1. First, connect to your Hostinger SSH account\n";
echo "2. Navigate to your project directory\n";
echo "3. Run the following commands:\n\n";

echo "# Check if .env is configured correctly\n";
echo "cat .env | grep DB_\n\n";

echo "# Run migrations (this will create all tables)\n";
echo "php artisan migrate --force\n\n";

echo "# Seed the database with initial data\n";
echo "php artisan db:seed --force\n\n";

echo "# If you get any errors during migration, try this:\n";
echo "php artisan migrate:fresh --seed --force\n\n";

echo "# Verify tables were created\n";
echo "php artisan tinker\n";
echo ">>> \\Schema::hasTable('courses')  # Should return true\n";
echo ">>> \\App\\Models\\Course::count()  # Should show number of courses\n";
echo ">>> \\App\\Models\\Branch::count()  # Should show number of branches\n";
echo ">>> exit\n\n";

echo "=== AFTER RUNNING MIGRATIONS ===\n\n";

echo "If the tables exist but courses are not showing when you select a branch,\n";
echo "you need to assign courses to branches. Run this in tinker:\n\n";

echo "php artisan tinker\n";
echo ">>> \$branch = \\App\\Models\\Branch::first();\n";
echo ">>> \$courses = \\App\\Models\\Course::all();\n";
echo ">>> \$branch->courses()->attach(\$courses);\n";
echo ">>> exit\n\n";

echo "=== TROUBLESHOOTING ===\n\n";

echo "If you still see issues:\n";
echo "1. Check database connection: php artisan tinker --execute=\"DB::connection()->getPdo()\"\n";
echo "2. Check migrations status: php artisan migrate:status\n";
echo "3. Clear cache: php artisan cache:clear && php artisan config:clear\n";
echo "4. Check logs: tail -f storage/logs/laravel.log\n\n";

echo "=== JAVASCRIPT FIX APPLIED ===\n\n";

echo "I've already fixed the JavaScript issue in the exam creation form:\n";
echo "- Now it shows all courses when no branch is selected\n";
echo "- Falls back to all courses if branch has no courses assigned\n";
echo "- Falls back to all courses if API call fails\n\n";

echo "This should resolve the issue where courses disappear when selecting a branch.\n\n";

echo "=== NEXT STEPS ===\n\n";

echo "1. SSH into your Hostinger account\n";
echo "2. Navigate to your project\n";
echo "3. Run: php artisan migrate --force\n";
echo "4. Run: php artisan db:seed --force\n";
echo "5. Test the exam creation form\n\n";

echo "If you need help with any of these steps, let me know!\n";
