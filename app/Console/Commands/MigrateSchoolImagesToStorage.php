<?php

namespace App\Console\Commands;

use App\Models\SchoolInformation;
use App\Services\SchoolImageService;
use Illuminate\Console\Command;

class MigrateSchoolImagesToStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:school-images-to-storage
                            {--dry-run : Show what would be done without making changes}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Migrate school logo and signature images from database BLOB to file storage';

    protected SchoolImageService $imageService;

    public function __construct(SchoolImageService $imageService)
    {
        parent::__construct();
        $this->imageService = $imageService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info('Starting migration of school images from BLOB to storage...');
        
        if ($dryRun) {
            $this->warn('Running in DRY RUN mode - no changes will be made');
        }

        $schoolInfos = SchoolInformation::whereRaw(
            'images_migrated_to_storage = 0 AND (logo IS NOT NULL OR principal_signature_data IS NOT NULL OR stamp_signature_data IS NOT NULL)'
        )->get();

        if ($schoolInfos->isEmpty()) {
            $this->info('No school images to migrate.');
            return self::SUCCESS;
        }

        $this->info("Found {$schoolInfos->count()} school record(s) to migrate.");

        $progressBar = $this->output->createProgressBar($schoolInfos->count());
        $progressBar->start();

        $successCount = 0;
        $failureCount = 0;

        foreach ($schoolInfos as $schoolInfo) {
            try {
                $migrated = false;

                // Migrate logo
                if (!empty($schoolInfo->logo) && empty($schoolInfo->logo_file_path)) {
                    $this->migrateLogoImage($schoolInfo, $dryRun);
                    $migrated = true;
                }

                // Migrate principal signature
                if (!empty($schoolInfo->principal_signature_data) && empty($schoolInfo->principal_signature_file_path)) {
                    $this->migratePrincipalSignature($schoolInfo, $dryRun);
                    $migrated = true;
                }

                // Migrate stamp signature
                if (!empty($schoolInfo->stamp_signature_data) && empty($schoolInfo->stamp_signature_file_path)) {
                    $this->migrateStampSignature($schoolInfo, $dryRun);
                    $migrated = true;
                }

                // Mark as migrated if we migrated something
                if ($migrated && !$dryRun) {
                    $schoolInfo->images_migrated_to_storage = true;
                    $schoolInfo->save();
                    $successCount++;
                }
            } catch (\Exception $e) {
                $this->error("Failed to migrate school ID {$schoolInfo->id}: " . $e->getMessage());
                $failureCount++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("Migration completed!");
        $this->info("Successfully migrated: {$successCount}");
        if ($failureCount > 0) {
            $this->warn("Failed: {$failureCount}");
        }

        return self::SUCCESS;
    }

    private function migrateLogoImage(SchoolInformation $schoolInfo, bool $dryRun): void
    {
        $imageData = $schoolInfo->logo;
        $type = $this->imageService->detectImageType($imageData);

        $filePath = $this->imageService->storeBinaryImage($imageData, 'logo', $type['extension']);

        if ($filePath) {
            if (!$dryRun) {
                $schoolInfo->logo_file_path = $filePath;
                $schoolInfo->logo = null; // Clear BLOB data after storing file
            }
            $this->line("  ✓ Logo migrated for school ID {$schoolInfo->id}");
        } else {
            throw new \Exception("Failed to store logo image for school ID {$schoolInfo->id}");
        }
    }

    private function migratePrincipalSignature(SchoolInformation $schoolInfo, bool $dryRun): void
    {
        $imageData = $schoolInfo->principal_signature_data;
        $type = $this->imageService->detectImageType($imageData);

        $filePath = $this->imageService->storeBinaryImage($imageData, 'principal_signature', $type['extension']);

        if ($filePath) {
            if (!$dryRun) {
                $schoolInfo->principal_signature_file_path = $filePath;
                $schoolInfo->principal_signature_data = null; // Clear BLOB data after storing file
            }
            $this->line("  ✓ Principal signature migrated for school ID {$schoolInfo->id}");
        } else {
            throw new \Exception("Failed to store principal signature for school ID {$schoolInfo->id}");
        }
    }

    private function migrateStampSignature(SchoolInformation $schoolInfo, bool $dryRun): void
    {
        $imageData = $schoolInfo->stamp_signature_data;
        $type = $this->imageService->detectImageType($imageData);

        $filePath = $this->imageService->storeBinaryImage($imageData, 'stamp_signature', $type['extension']);

        if ($filePath) {
            if (!$dryRun) {
                $schoolInfo->stamp_signature_file_path = $filePath;
                $schoolInfo->stamp_signature_data = null; // Clear BLOB data after storing file
            }
            $this->line("  ✓ Stamp signature migrated for school ID {$schoolInfo->id}");
        } else {
            throw new \Exception("Failed to store stamp signature for school ID {$schoolInfo->id}");
        }
    }
}
