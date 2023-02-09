<?php

namespace JTD420\PGP;

use JTD420\PGP\Commands\InstallCommand;

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
            ->name('PGP')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_pgp_keys_table')
            ->hasMigration('create_pgp_messages_table')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->copyAndRegisterServiceProviderInApp()
                    ->askToStarRepoOnGitHub('JTD420/Laravel-PGP');
            });
    }
}
