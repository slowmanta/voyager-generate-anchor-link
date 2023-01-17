<?php

namespace NamMT\VoyagerGenerateAnchorLink\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use NamMT\VoyagerGenerateAnchorLink\VoyagerGenerateAnchorLinkServiceProvider;
use Symfony\Component\Console\Input\InputOption;
use NamMT\VoyagerGenerateAnchorLink\Seed;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'voyager-generate-anchor-link:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Voyager Generate Anchor Link';

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Foundation\Composer
     */
    protected $composer;

    /**
     * Seed Folder name.
     *
     * @var string
     */
    protected $seedFolder;

    public function __construct(Composer $composer)
    {
        parent::__construct();

        $this->composer = $composer;
        $this->composer->setWorkingPath(base_path());

        $this->seedFolder = Seed::getFolderName();
    }

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production', null],
            ['with-dummy', null, InputOption::VALUE_NONE, 'Install with dummy data', null],
        ];
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        if (file_exists(getcwd().'/composer.phar')) {
            return '"'.PHP_BINARY.'" '.getcwd().'/composer.phar';
        }

        return 'composer';
    }

    public function fire(Filesystem $filesystem)
    {
        return $this->handle($filesystem);
    }

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     *
     * @return void
     */
    public function handle(Filesystem $filesystem)
    {
        $publishablePath = dirname(__DIR__).'/../publishable';
        // Publish only relevant resources on install
        $tags = ['seeds'];

        $this->call('vendor:publish', ['--provider' => VoyagerGenerateAnchorLinkServiceProvider::class, '--tag' => $tags]);

        // Publish only relevant resources on install
        $this->info('Migrating the database tables into your application');
        $this->call('migrate');

        $this->addNamespaceIfNeeded(
            collect($filesystem->files("{$publishablePath}/database/seeds/")),
            $filesystem
        );
        $this->info('Dumping the autoloaded files and reloading all new files');
        $this->composer->dumpAutoloads();
        require_once base_path('vendor/autoload.php');

        $this->info('Seeding data into the database');
        $this->call('db:seed', ['--class' => 'VoyagerGenerateAnchorLinkSeeder']);

        $this->info('Successfully installed Voyager Generate Anchor Link! Enjoy');
    }

    private function addNamespaceIfNeeded($seeds, Filesystem $filesystem)
    {
        if ($this->seedFolder != 'seeders') {
            return;
        }

        $seeds->each(function ($file) use ($filesystem) {
            $path = database_path('seeders').'/'.$file->getFilename();
            $stub = str_replace(
                ["<?php\n\nuse", "<?php".PHP_EOL.PHP_EOL."use"],
                "<?php".PHP_EOL.PHP_EOL."namespace Database\\Seeders;".PHP_EOL.PHP_EOL."use",
                $filesystem->get($path)
            );

            $filesystem->put($path, $stub);
        });
    }
}
