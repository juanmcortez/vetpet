<?php

namespace App\Console\Commands;

use App\Support\Vetpet;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

#[Description('Rewrite Wayfinder route URLs to path-only (actions keep host keys for multi-surface routes)')]
#[Signature('vetpet:normalize-wayfinder-urls')]
class NormalizeWayfinderUrlsCommand extends Command
{
    public function handle(Filesystem $files): int
    {
        // Only named routes: controller actions keyed by URI break when the same path exists on multiple subdomains.
        $directories = [
            resource_path('js/routes'),
        ];

        $hosts = array_values(Vetpet::domainMap());

        $updated = 0;

        foreach ($directories as $directory) {
            if (! $files->isDirectory($directory)) {
                continue;
            }

            foreach ($files->allFiles($directory) as $file) {
                if ($file->getExtension() !== 'ts') {
                    continue;
                }

                $contents = $file->getContents();
                $normalized = $this->normalizeContents($contents, $hosts);

                if ($normalized !== $contents) {
                    $files->put($file->getPathname(), $normalized);
                    $updated++;
                }
            }
        }

        if ($updated > 0) {
            $this->components->info("Normalized Wayfinder URLs in {$updated} file(s).");
        }

        return self::SUCCESS;
    }

    /**
     * @param  list<string>  $hosts
     */
    private function normalizeContents(string $contents, array $hosts): string
    {
        foreach ($hosts as $host) {
            $contents = preg_replace(
                '/([\'"])(?:https?:)?\/\/'.preg_quote($host, '/').'(?::\d+)?/',
                '$1',
                $contents,
            ) ?? $contents;
        }

        return $contents;
    }
}
