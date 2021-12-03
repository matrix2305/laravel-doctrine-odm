<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\Commands;


use Illuminate\Console\Command;
use BadMethodCallException;
use Doctrine\ODM\MongoDB\SchemaManager;
use LaravelDoctrineODM\Facades\DocumentManager;
use MongoDB\Driver\WriteConcern;
use Exception;

class DoctrineODMSchemaShard extends Command
{
    protected $name = 'odm:schema:shard';

    protected $description = 'Enable sharding for selected documents';

    public function handle() : void
    {
        if ($this->hasOption('class')){
            $class = $this->option('class');
        }else{
            $class = null;
        }
        $sm = DocumentManager::getSchemaManager();

        try {
            if (is_string($class)){
                $this->processDocumentIndex($sm, $class, null, null);
                $this->info("Enabled sharding for {$class}.");
            }else{
                $this->processIndex($sm, null, null);
                $this->info('Enabled sharding for all classes.');
            }
        }catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    protected function processDocumentIndex(SchemaManager $sm, string $document, ?int $maxTimeMs = null, ?WriteConcern $writeConcern = null) : void
    {
        $sm->ensureDocumentSharding($document, $writeConcern);
    }

    protected function processIndex(SchemaManager $sm, ?int $maxTimeMs = null, ?WriteConcern $writeConcern = null) : void
    {
        $sm->ensureSharding($writeConcern);
    }

    /**
     * @throws BadMethodCallException
     */
    protected function processDocumentCollection(SchemaManager $sm, string $document, ?int $maxTimeMs, ?WriteConcern $writeConcern) : void
    {
        throw new BadMethodCallException('Cannot update a document collection');
    }

    /**
     * @throws BadMethodCallException
     */
    protected function processCollection(SchemaManager $sm, ?int $maxTimeMs, ?WriteConcern $writeConcern) : void
    {
        throw new BadMethodCallException('Cannot update a collection');
    }

    /**
     * @throws BadMethodCallException
     */
    protected function processDocumentDb(SchemaManager $sm, string $document, ?int $maxTimeMs, ?WriteConcern $writeConcern) : void
    {
        throw new BadMethodCallException('Cannot update a document database');
    }

    /**
     * @throws BadMethodCallException
     */
    protected function processDb(SchemaManager $sm, ?int $maxTimeMs, ?WriteConcern $writeConcern) : void
    {
        throw new BadMethodCallException('Cannot update a database');
    }
}