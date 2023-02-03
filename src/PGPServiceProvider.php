<?php

namespace JTD420\PGP;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use JTD420\PGP\Commands\PGPCommand;

class PGPServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-pgp')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-pgp_table')
            ->hasCommand(PGPCommand::class);
    }
}
