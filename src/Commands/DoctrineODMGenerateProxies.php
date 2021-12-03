<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\Commands;


use Doctrine\ODM\MongoDB\DocumentManager;
use Illuminate\Console\Command;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Tools\Console\MetadataFilter;
use Doctrine\ODM\MongoDB\ConfigurationException;
use InvalidArgumentException;

class DoctrineODMGenerateProxies extends Command
{
    protected $name = 'odm:generate:proxies';

    protected $description = 'Generates proxy classes for document classes.';

    public function handle() : void
    {
        if ($this->hasOption('filter')){
            $filter = $this->option('filter');
            assert(is_array($filter));
        }else{
            $filter = null;
        }
        $dm = app('DocumentManager');
        assert($dm instanceof DocumentManager);

        /** @var ClassMetadata[] $metadatas */
        $metadatas = array_filter($dm->getMetadataFactory()->getAllMetadata(), static function (ClassMetadata $classMetadata): bool {
            return ! $classMetadata->isEmbeddedDocument && ! $classMetadata->isMappedSuperclass && ! $classMetadata->isQueryResultDocument;
        });

        $metadatas = MetadataFilter::filter($metadatas, $filter);
        $destPath  = $dm->getConfiguration()->getProxyDir();

        if (! is_string($destPath)) {
            throw ConfigurationException::proxyDirMissing();
        }

        if (! is_dir($destPath)) {
            if (!mkdir($destPath, 0775, true) && !is_dir($destPath)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $destPath));
            }
        }

        $destPath = realpath($destPath);
        assert($destPath !== false);

        if (! file_exists($destPath)) {
            throw new InvalidArgumentException(
                sprintf("Proxies destination directory %s does not exist.", $destPath)
            );
        }

        if (! is_writable($destPath)) {
            throw new InvalidArgumentException(
                sprintf("Proxies destination directory %s does not have write permissions.", $destPath)
            );
        }

        if (count($metadatas)) {
            foreach ($metadatas as $metadata) {
                $this->line(
                    sprintf('Processing document %s"', $metadata->name) . PHP_EOL
                );
            }

            // Generating Proxies
            $dm->getProxyFactory()->generateProxyClasses($metadatas);

            // Outputting information message
            $this->info(PHP_EOL . sprintf('Proxy classes generated to %s', $destPath) . PHP_EOL);
        } else {
            $this->warn('No Metadata Classes to process.' . PHP_EOL);
        }

    }
}