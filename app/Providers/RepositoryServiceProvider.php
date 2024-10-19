<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Whether the provider loads lazily or not.
     *
     * @var bool
     */
    protected $defer = true;
    /**
     * Register services.
     */
    public function register(): void
    {
        // You can bind repositories manually here or dynamically
        $this->bindRepositoriesDynamically();

    }



    /**
     * Dynamically bind repository interfaces to their implementations.
     */
    protected function bindRepositoriesDynamically(): void
    {
        // Path to the Contracts (interfaces) and Eloquent (implementations) directories
        $contractsPath = app_path('Repositories/Contracts');
        $eloquentNamespace = 'App\\Repositories\\Eloquent\\';

        // Get all interface files in the Contracts folder
        $files = File::allFiles($contractsPath);

        foreach ($files as $file) {
            // Extract the interface name without the "php" extension
            $interfaceName = Str::before($file->getFilename(), '.php');

            // Assuming the interface names end with "Interface"
            if (Str::endsWith($interfaceName, 'Interface')) {
                // Generate the fully qualified interface name
                $relativePath = str_replace('/', '\\', $file->getRelativePath());
                $interfaceClass = "App\\Repositories\\Contracts\\" . $relativePath . "\\" . $interfaceName;

                // Extract the folder name (e.g., 'Country') and assume implementation is under this subfolder in Eloquent
                $folderName = Str::before($interfaceName, 'RepositoryInterface');
                $repositoryClass = $eloquentNamespace . $relativePath . '\\' . $folderName . 'Repository';

                // Check if the repository implementation class exists before binding
                if (class_exists($repositoryClass)) {
                    // Bind the interface to the implementation
                    $this->app->bind($interfaceClass, $repositoryClass);
                }
            }
        }
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        // Path to the Contracts (interfaces) directory
        $contractsPath = app_path('Repositories/Contracts');
        $interfaces = [];

        // Get all interface files in the Contracts folder
        $files = File::allFiles($contractsPath);

        foreach ($files as $file) {
            // Extract the interface name without the ".php" extension
            $interfaceName = Str::before($file->getFilename(), '.php');

            // Check if the interface ends with "Interface"
            if (Str::endsWith($interfaceName, 'Interface')) {
                // Build the relative path for the interface's namespace (handle subdirectories)
                $relativePath = str_replace('/', '\\', $file->getRelativePath());

                // Generate the fully qualified interface name, including subfolder path
                $interfaceClass = "App\\Repositories\\Contracts\\" . $relativePath . "\\" . $interfaceName;

                // Add the interface to the list
                $interfaces[] = $interfaceClass;
            }
        }

        // Return the list of interfaces provided by this service provider
        return $interfaces;
    }


}
