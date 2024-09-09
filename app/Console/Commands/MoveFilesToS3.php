<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MoveStorageToS3 extends Command
{
    protected $signature = 'move:storage-to-s3';

    protected $description = 'Move storage files and folders to S3';

    public function handle()
    {
        // Get all files and directories in the local storage directory
        $items = Storage::disk('public')->allFiles();

        // Move each item to S3
        foreach ($items as $item) {
            // Check if the file already exists in S3
            if (!Storage::disk('s3')->exists($item)) {
                // Copy the file to S3
                Storage::disk('s3')->put($item, Storage::disk('public')->get($item));

                $this->info("File '{$item}' moved to S3.");
            } else {
                $this->info("File '{$item}' already exists in S3. Skipping...");
            }
        }

        $this->info('All files and folders moved to S3 successfully.');
    }
}