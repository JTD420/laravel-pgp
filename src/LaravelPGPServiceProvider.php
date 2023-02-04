<?php

namespace JTD420\PGP;

use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\Commands\InstallCommand;

class LaravelPGPServiceProvider extends PackageServiceProvider
{
public function configurePackage(Package $package): void
{
$package
->name('laravel-pgp')
->hasConfigFile()
->hasViews()
->hasTranslations()
->hasAssets()
->publishesServiceProvider('LaravelPGPServiceProvider')
->hasRoute('web')
->hasMigration('create_PGP_tables')
->hasInstallCommand(function(InstallCommand $command) {
$command
->publishConfigFile()
->publishMigrations()
->askToRunMigrations()
->copyAndRegisterServiceProviderInApp()
->askToStarRepoOnGitHub('JTD420/Laravel-PGP');
});
}
}
