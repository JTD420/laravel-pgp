<?php

namespace JTD420\PGP\Console;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

trait InstallsPGPStack
{
    /**
     * Install the Default Blade PGP stack.
     *
     * @return void
     */
    protected function installPGPStack()
    {
        // Controllers...
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/app/Http/Controllers', app_path('Http/Controllers'));

        // Requests...
        //(new Filesystem)->ensureDirectoryExists(app_path('Http/Requests'));
        //(new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/app/Http/Requests', app_path('Http/Requests'));

        // Views...
        //(new Filesystem)->ensureDirectoryExists(resource_path('views'));
        //(new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/resources/views', resource_path('views'));

        if (! $this->option('dark')) {
            $this->removeDarkClasses((new Finder)
                ->in(resource_path('views'))
                ->name('*.blade.php')
                ->notName('welcome.blade.php')
            );
        }

        // Components...
        //(new Filesystem)->ensureDirectoryExists(app_path('View/Components'));
        //(new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/app/View/Components', app_path('View/Components'));

        // Tests...
        //$this->installTests();

        // Append Include of PGP Routes to Existing routes/web.php File...
        $existingRoutes = file_get_contents(base_path('routes/web.php')); // Read the contents of the existing routes/web.php file into a string
        $stubRoutes = file_get_contents(__DIR__.'/../../stubs/default/routes/web.php'); // Read the contents of your stub file into a string
        $combinedRoutes = $existingRoutes.PHP_EOL.$stubRoutes; // Concatenate the contents of the two files
        file_put_contents(base_path('routes/web.php'), $combinedRoutes); // Write the combined contents back to the routes/web.php file

        // Routes...
        if (! file_exists(base_path('routes/pgp.php'))) {
            copy(__DIR__.'/../../stubs/default/routes/pgp.php', base_path('routes/pgp.php'));
        }

        //copy(__DIR__.'/../../stubs/default/routes/auth.php', base_path('routes/auth.php'));

        // "Dashboard" Route...
        //$this->replaceInFile('/home', '/dashboard', resource_path('views/welcome.blade.php'));
        //$this->replaceInFile('Home', 'Dashboard', resource_path('views/welcome.blade.php'));
        //$this->replaceInFile('/home', '/dashboard', app_path('Providers/RouteServiceProvider.php'));

        $this->components->info('Installing dependencies.');

        if (file_exists(base_path('composer.json'))) {
            $this->runCommands(['php ./vendor/jtd420/laravel-pgp/src/add-classmap.php']);
        }
//      elseif (file_exists(base_path('yarn.lock'))) {
//            $this->runCommands(['yarn install', 'yarn run build']);
//        } else {
//            $this->runCommands(['npm install', 'npm run build']);
//        }

        $this->line('');
        $this->components->info('PGP scaffolding installed successfully.');
    }
}
