<?php

namespace JTD420\PGP\Commands;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use JTD420\PGP\Console\InstallsPGPStack;
use JTD420\PGP\Package;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    use InstallsPGPStack;

    protected Package $package;

    public ?Closure $startWith = null;

    protected bool $shouldPublishConfigFile = false;

    protected bool $askToPublishViewFiles = false;

    protected bool $shouldPublishMigrations = false;

    protected bool $askToRunMigrations = false;

    protected bool $copyServiceProviderInApp = false;

    protected ?string $starRepo = null;

    /**
     * The available stacks.
     * For now just blade but keeping this to allow for customisation at a later stage.
     *
     * @var array<int, string>
     */
    protected $stacks = ['blade'];

    public ?Closure $endWith = null;

    public $hidden = true;

    public function __construct(Package $package)
    {
        $this->signature = $package->shortName().':install {stack : The development stack that should be installed (blade)}
                            {--dark : Indicate that dark mode support should be installed}
                            {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

        $this->description = 'Install '.$package->name;

        $this->package = $package;

        parent::__construct();
    }

    public function handle()
    {
        if ($this->startWith) {
            ($this->startWith)($this);
        }

        if ($this->argument('stack') === 'blade') {
            $this->requireComposerPackages('singpolyma/openpgp-php:^0.6.0');
            $this->installPGPStack();
        } else {
            $this->components->error('Invalid stack. Currently supported stacks are [blade].');
            exit();
        }

        if ($this->shouldPublishConfigFile) {
            $this->comment('Publishing config file...');

            $this->callSilently('vendor:publish', [
                '--tag' => "{$this->package->shortName()}-config",
            ]);
        }

        if ($this->askToPublishViewFiles) {
            if ($this->confirm('Would you like to publish the view files for customization purposes?')) {
                $this->comment('Publishing view files...');
                $this->callSilently('vendor:publish', [
                    '--tag' => "{$this->package->shortName()}-views",
                ]);
            }
        }

        if ($this->shouldPublishMigrations) {
            $this->comment('Publishing migration...');

            $this->callSilently('vendor:publish', [
                '--tag' => "{$this->package->shortName()}-migrations",
            ]);
        }

        if ($this->askToRunMigrations) {
            if ($this->confirm('Would you like to run the migrations now?')) {
                $this->comment('Running migrations...');

                $this->call('migrate');
            }
        }

        if ($this->copyServiceProviderInApp) {
            $this->comment('Publishing service provider...');

            $this->copyServiceProviderInApp();
        }

        if ($this->starRepo) {
            if ($this->confirm('Would you like to star our repo on GitHub?')) {
                $repoUrl = "https://github.com/{$this->starRepo}";

                if (PHP_OS_FAMILY == 'Darwin') {
                    exec("open {$repoUrl}");
                }
                if (PHP_OS_FAMILY == 'Windows') {
                    exec("start {$repoUrl}");
                }
                if (PHP_OS_FAMILY == 'Linux') {
                    exec("xdg-open {$repoUrl}");
                }
            }
        }

        $this->info("{$this->package->shortName()} has been installed!");

        if ($this->endWith) {
            ($this->endWith)($this);
        }

        return 1;
    }

    /**
     * Interact with the user to prompt them when the stack argument is missing.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if ($this->argument('stack') === null && $this->option('blade')) {
            $input->setArgument('stack', 'blade');
        }

        if ($this->argument('stack')) {
            return;
        }

        $input->setArgument('stack', $this->components->choice('Which stack would you like to install?', $this->stacks));

        $input->setOption('dark', $this->components->confirm('Would you like to install dark mode support?'));

        //$input->setOption('pest', $this->components->confirm('Would you prefer Pest tests instead of PHPUnit?'));
    }

    public function publishConfigFile(): self
    {
        $this->shouldPublishConfigFile = true;

        return $this;
    }

    public function publishMigrations(): self
    {
        $this->shouldPublishMigrations = true;

        return $this;
    }

    public function askToPublishViewFiles(): self
    {
        $this->askToPublishViewFiles = true;

        return $this;
    }

    public function askToRunMigrations(): self
    {
        $this->askToRunMigrations = true;

        return $this;
    }

    public function copyAndRegisterServiceProviderInApp(): self
    {
        $this->copyServiceProviderInApp = true;

        return $this;
    }

    public function askToStarRepoOnGitHub($vendorSlashRepoName): self
    {
        $this->starRepo = $vendorSlashRepoName;

        return $this;
    }

    public function startWith($callable): self
    {
        $this->startWith = $callable;

        return $this;
    }

    public function endWith($callable): self
    {
        $this->endWith = $callable;

        return $this;
    }

    protected function copyServiceProviderInApp(): self
    {
        $providerName = $this->package->publishableProviderName;

        if (! $providerName) {
            return $this;
        }

        $this->callSilent('vendor:publish', ['--tag' => $this->package->shortName().'-provider']);

        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());

        $appConfig = file_get_contents(config_path('app.php'));

        $class = '\\Providers\\'.$providerName.'::class';

        if (Str::contains($appConfig, $namespace.$class)) {
            return $this;
        }

        file_put_contents(config_path('app.php'), str_replace(
            "Illuminate\\View\ViewServiceProvider::class,",
            "Illuminate\\View\ViewServiceProvider::class,".PHP_EOL."        {$namespace}\Providers\\".$providerName.'::class,',
            $appConfig
        ));

        file_put_contents(app_path('Providers/'.$providerName.'.php'), str_replace(
            "namespace App\Providers;",
            "namespace {$namespace}\Providers;",
            file_get_contents(app_path('Providers/'.$providerName.'.php'))
        ));

        return $this;
    }

    /**
     * Replace a given string within a given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $path
     * @return void
     */
    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    /**
     * Installs the given Composer Packages into the application.
     *
     * @param  mixed  $packages
     * @return bool
     */
    protected function requireComposerPackages($packages)
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'require'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require'],
            is_array($packages) ? $packages : func_get_args()
        );

        return ! (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }

    /**
     * Run the given commands.
     *
     * @param  array  $commands
     * @return void
     */
    protected function runCommands($commands)
    {
        $process = Process::fromShellCommandline(implode(' && ', $commands), null, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $this->output->writeln('  <bg=yellow;fg=black> WARN </> '.$e->getMessage().PHP_EOL);
            }
        }

        $process->run(function ($type, $line) {
            $this->output->write('    '.$line);
        });
    }

    /**
     * Remove Tailwind dark classes from the given files.
     *
     * @param  \Symfony\Component\Finder\Finder  $finder
     * @return void
     */
    protected function removeDarkClasses(Finder $finder)
    {
        foreach ($finder as $file) {
            file_put_contents($file->getPathname(), preg_replace('/\sdark:[^\s"\']+/', '', $file->getContents()));
        }
    }
}
