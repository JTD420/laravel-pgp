<?php

namespace JTD420\PGP;

use Illuminate\Support\Facades\Artisan;
use JTD420\PGP\Commands\AddUserModelChanges;
use JTD420\PGP\Commands\AddUserNameColumn;
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
            ->name('laravel-PGP')
            ->publishesServiceProvider('PGPAppServiceProvider')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations(
                'create_pgp_keys_table',
                'create_pgp_messages_table',
                'create_pgp_message_recipients_table',
                'create_pgp_replies_table',
                'create_pgp_reply_recipients_table',
                //'create_pgp_replies_table',
            )
            ->hasCommands(AddUserModelChanges::class, AddUserNameColumn::class)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->startWith(function (InstallCommand $command) {
                        $command->call('add-user-model-changes');
                        $command->call('add-username-column');
                        Artisan::call('migrate', ['--path' => 'database/migrations/2014_10_12_000000_create_users_table.php']);
                        Artisan::call('migrate', ['--path' => 'database/migrations/add_username_to_users_table.php']);
                    })
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToPublishViewFiles()
                    ->copyAndRegisterServiceProviderInApp()
                    ->copyAndRegisterGravatarProviderInApp()
                    ->askToStarRepoOnGitHub('JTD420/Laravel-PGP');
            });
    }
}
