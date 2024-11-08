<?php

namespace Thvvger\RequestValidator\Services;

use Symfony\Component\Filesystem\Filesystem;

class FileGenerator
{
    protected Filesystem $filesystem;
    protected string $requestDir;

    public function __construct(string $projectDir)
    {
        $this->filesystem = new Filesystem();
        $this->requestDir = $projectDir . '/src/Request';
    }

    public function generateClass(
        string $className,
        string $hasFile,
        string $namespace = 'App\\Request'
    ): void {
        $stubPath = __DIR__ . '/../Resources/stubs/ExampleStub.php.stub';

        $content = file_get_contents($stubPath);

        $baseClass = $hasFile ? 'FileRequestValidator' : 'RequestValidator';

        // Replace placeholders with actual values
        $content = str_replace(
            ['{{ ClassName }}', '{{ BaseClass }}', '{{ Namespace }}'],
            [$className, $baseClass, $namespace],
            $content
        );

        // Define target file path within the src/Request directory
        $targetFile = $this->requestDir . '/' . $className . '.php';
        $this->filesystem->dumpFile($targetFile, $content);
    }

}