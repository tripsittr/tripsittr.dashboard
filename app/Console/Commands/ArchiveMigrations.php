<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ArchiveMigrations extends Command {
    protected $signature = 'migrations:archive';
    protected $description = 'Move all migrations to the Archive folder inside database/migrations';

    public function handle() {
        $migrationPath = database_path('migrations');
        $archivePath = database_path('migrations/Archive');

        if (!File::exists($archivePath)) {
            File::makeDirectory($archivePath, 0755, true);
            $this->info('Created Archive directory.');
        }

        $migrations = File::files($migrationPath);

        foreach ($migrations as $migration) {
            $migrationName = $migration->getFilename();

            // Skip already archived migrations
            if (str_contains($migration->getPathname(), 'Archive')) {
                continue;
            }

            if (!File::move($migration->getPathname(), "$archivePath/$migrationName")) {
                $this->error("Failed to move $migrationName");
            } else {
                $this->info("Moved: $migrationName");
            }
        }

        $this->info('All migrations have been archived successfully.');
    }
}
