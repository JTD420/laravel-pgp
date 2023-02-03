<?php

namespace JTD420\PGP\Commands;

use Illuminate\Console\Command;

class PGPCommand extends Command
{
    public $signature = 'laravel-pgp';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
