#!/bin/bash

================================
 ESA eLearning Deployment
================================

[1/4] Pulling latest GitHub code...
GIT_SSH_COMMAND="ssh -i /home/u244769603/.ssh/esa_github_deploy -o IdentitiesOnly=yes"
cd /home/u244769603/elearning || exit 1
git fetch origin
git checkout master
git pull origin master

[2/4] Installing Composer dependencies...
composer install --no-dev --optimize-autoloader

[3/4] Clearing Laravel caches...
php artisan optimize:clear

[4/4] Optimizing Laravel...
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verify Vite manifest exists
if [ ! -f "public/build/manifest.json" ]; then
    echo "ERROR: Vite manifest not found at public/build/manifest.json"
    echo "Deployment aborted. Please ensure production assets are built and committed."
    exit 1
fi

echo "✓ Vite manifest verified"

================================
 DEPLOYMENT SUCCESSFUL
================================
