<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\Commands;


use Doctrine\ODM\MongoDB\Tools\Console\MetadataFilter;
use Illuminate\Console\Command;
use InvalidArgumentException;
use RuntimeException;

class DoctrineODMGeneratePersistentCollections extends Command
{
    protected $name = 'odm:generate:persistent-collections';

    protected $description = 'Generates persistent collection classes for custom collections.';

    public function handle() : void
    {
        $filter = $this->hasOption('filter') ? $this->option('filter') : null;
        $dm = app('DocumentManager');
        $metadatas = $dm->getMetadataFactory()->getAllMetadata();
        $metadatas = MetadataFilter::filter($metadatas, $filter);
        $destPath  = $this->hasOption('dest-path') ? $this->option('dest-path') : null;

        // Process destination directory
        if ($destPath === null) {
            $destPath = $dm->getConfiguration()->getPersistentCollectionDir();
        }

        if (!is_dir($destPath) && !mkdir($destPath, 0775, true) && !is_dir($destPath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $destPath));
        }

        $destPath = realpath($destPath);
        assert($destPath !== false);


        if (! file_exists($destPath)) {
            throw new InvalidArgumentException(
                sprintf("Persistent collections destination directory %s does not exist.", $destPath)
            );
        }

        if (! is_writable($destPath)) {
            throw new InvalidArgumentException(
                sprintf("Persistent collections destination directory %s does not have write permissions.", $destPath)
            );
        }

        if (count($metadatas)){
            $generated           = [];
            $collectionGenerator = $dm->getConfiguration()->getPersistentCollectionGenerator();
            foreach ($metadatas as $metadata){
                $this->line(sprintf('Processing document %s"', $metadata->name) . PHP_EOL);
                foreach ($metadata->getAssociationNames() as $fieldName){
                    $mapping = $metadata->getFieldMapping($fieldName);
                    if (empty($mapping['collectionClass']) || isset($generated[$mapping['collectionClass']])) {
                        continue;
                    }

                    $generated[$mapping['collectionClass']] = true;
                    $this->line(sprintf('Generating class for %s"', $mapping['collectionClass']) . PHP_EOL);
                    $collectionGenerator->generateClass($mapping['collectionClass'], $destPath);
                }
            }
            $this->info(PHP_EOL . sprintf('Persistent collections classes generated to %s', $destPath) . PHP_EOL);
        }else{
            $this->warn('No Metadata Classes to process.' . PHP_EOL);
        }
    }
}