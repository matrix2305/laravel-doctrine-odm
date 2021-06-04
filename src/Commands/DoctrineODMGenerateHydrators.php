<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\Commands;


use Doctrine\ODM\MongoDB\Tools\Console\MetadataFilter;
use Illuminate\Console\Command;
use RuntimeException;
use InvalidArgumentException;

class DoctrineODMGenerateHydrators extends Command
{
    protected $name = 'odm:generate:hydrators';

    protected $description = 'Generates hydrator classes for document classes.';

    public function handle() : void
    {
        $filter = $this->hasOption('filter') ? $this->option('filter') : null;

        $dm = app('DocumentManager');


        $metadatas = $dm->getMetadataFactory()->getAllMetadata();
        $metadatas = MetadataFilter::filter($metadatas, $filter);
        $destPath  = $this->hasOption('dest-path') ? $this->option('dest-path') : null;

        // Process destination directory
        if ($destPath === null) {
            $destPath = $dm->getConfiguration()->getHydratorDir();
        }
        if (!is_dir($destPath) && !mkdir($destPath, 0775, true) && !is_dir($destPath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $destPath));
        }

        $destPath = realpath($destPath);
        assert($destPath !== false);

        if (! file_exists($destPath)) {
            throw new InvalidArgumentException(
                sprintf("Hydrators destination directory %s does not exist.", $destPath)
            );
        }

        if (! is_writable($destPath)) {
            throw new InvalidArgumentException(
                sprintf("Hydrators destination directory %s does not have write permissions.", $destPath)
            );
        }

        if (count($metadatas)) {
            foreach ($metadatas as $metadata) {
                $this->line(sprintf('Processing document %s', $metadata->name) . PHP_EOL);
            }

            // Generating Hydrators
            $dm->getHydratorFactory()->generateHydratorClasses($metadatas, $destPath);

            // Outputting information message
            $this->info(PHP_EOL . sprintf('Hydrator classes generated to %s', $destPath) . PHP_EOL);
        } else {
            $this->warn('No Metadata Classes to process.' . PHP_EOL);
        }
    }
}