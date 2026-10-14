<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Description('Generate Wayfinder types and normalize route URLs for Vite')]
#[Signature('vetpet:wayfinder-for-vite')]
class WayfinderForViteCommand extends Command
{
    public function handle(): int
    {
        $this->call('wayfinder:generate', [
            '--with-form' => true,
            '--no-interaction' => true,
        ]);

        $this->call('vetpet:normalize-wayfinder-urls');

        return self::SUCCESS;
    }
}
