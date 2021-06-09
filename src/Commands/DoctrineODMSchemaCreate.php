<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\Commands;

use BadMethodCallException;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\SchemaManager;
use LaravelDoctrineODM\Facades\DocumentManager;
use MongoDB\Driver\WriteConcern;
use Throwable;
use Illuminate\Console\Command;

class DoctrineODMSchemaCreate extends Command
{
    protected $name = 'odm:schema:create';
    protected $description = 'Create databases, collections and indexes for your documents';

    private array $createOrder = ['collection', 'index'];

    public function handle() : void
    {
        $create = [];
        foreach ($this->createOrder as $option){
            if ($this->hasOption($option)){
                $create[] = $option;
            }
        }
        $create = empty($create) ? $this->createOrder : $create;
        $class = $this->hasOption('class')? $this->option('class') : null;
        $background = $this->hasOption('background');

        $maxTimeInMS = $this->ask('Insert maximum execution time in ms: ');
        if (!is_numeric($maxTimeInMS)){
            throw new \Exception('Max execution time must be number.');
        }

        $maxTimeInMS = (int)$maxTimeInMS;

        $sm = DocumentManager::getSchemaManager();

        foreach ($create as $option){
            try {
                if (is_string($class)){
                    $this->{'processDocument' . ucfirst($option)}($sm, $class, $maxTimeInMS, null, $background);
                } else {
                    $this->{'process' . ucfirst($option)}($sm, $maxTimeInMS, null, $background);
                }
                $this->info(sprintf(
                    'Created %s%s for %s',
                    $option,
                    is_string($class) ? ($option === 'index' ? '(es)' : '') : ($option === 'index' ? 'es' : 's'),
                    is_string($class) ? $class : 'all classes'
                ));
            }catch (Throwable $exception){
                $this->error($exception->getMessage());
            }
        }
    }

    protected function processDocumentCollection(SchemaManager $sm, string $document, ?int $maxTimeMs, ?WriteConcern $writeConcern)
    {
        $sm->createDocumentCollection($document, $maxTimeMs, $writeConcern);
    }

    protected function processCollection(SchemaManager $sm, ?int $maxTimeMs, ?WriteConcern $writeConcern)
    {
        $sm->createCollections($maxTimeMs, $writeConcern);
    }

    protected function processDocumentDb(SchemaManager $sm, string $document, ?int $maxTimeMs, ?WriteConcern $writeConcern)
    {
        throw new BadMethodCallException('A database is created automatically by MongoDB (>= 3.0).');
    }

    protected function processDb(SchemaManager $sm, ?int $maxTimeMs, ?WriteConcern $writeConcern)
    {
        throw new BadMethodCallException('A database is created automatically by MongoDB (>= 3.0).');
    }

    protected function processDocumentIndex(SchemaManager $sm, string $document, ?int $maxTimeMs, ?WriteConcern $writeConcern, bool $background = false)
    {
        $sm->ensureDocumentIndexes($document, $maxTimeMs, $writeConcern, $background);
    }

    protected function processIndex(SchemaManager $sm, ?int $maxTimeMs, ?WriteConcern $writeConcern, bool $background = false)
    {
        $sm->ensureIndexes($maxTimeMs, $writeConcern, $background);
    }

    protected function processDocumentProxy(SchemaManager $sm, string $document)
    {
        $classMetadata = $this->getMetadataFactory()->getMetadataFor($document);
        assert($classMetadata instanceof ClassMetadata);

        $this->getDocumentManager()->getProxyFactory()->generateProxyClasses([$classMetadata]);
    }

    protected function processProxy(SchemaManager $sm)
    {
        /** @var ClassMetadata[] $metadatas */
        $metadatas = $this->getMetadataFactory()->getAllMetadata();
        $this->getDocumentManager()->getProxyFactory()->generateProxyClasses($metadatas);
    }
}