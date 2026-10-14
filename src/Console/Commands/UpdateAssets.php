<?php

namespace ProjectSaturnStudios\Themes\Starlight\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class UpdateAssets extends Command
{
    protected $signature = 'starlight:update-assets';

    protected $description = 'Copy (overwrite) the Starlight theme resources into the application';

    public function handle(Filesystem $files): int
    {
        $source = dirname(__DIR__, 3).'/resources';
        $relative = trim((string) config('starlight.assets_path', 'resources/themes/starlight'), '/');
        $target = base_path($relative);

        // The target is wiped before the copy, so it must be a real subdirectory of the app.
        if ($relative === '' || $relative === '.' || str_contains($relative, '..') || str_starts_with($relative, '/')) {
            $this->error("starlight.assets_path must name a directory inside the application; got \"$relative\".");

            return self::INVALID;
        }

        $files->deleteDirectory($target);
        $files->ensureDirectoryExists($target);

        if (! $files->copyDirectory($source, $target)) {
            $this->error("Could not copy theme assets to $target.");

            return self::FAILURE;
        }

        $this->info('Starlight theme assets copied to '.str_replace(base_path().'/', '', $target).'.');

        return self::SUCCESS;
    }
}
