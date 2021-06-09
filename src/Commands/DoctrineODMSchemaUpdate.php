<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\Commands;


use Illuminate\Console\Command;
use LaravelDoctrineODM\Facades\DocumentManager;
use Symfony\Component\ClassLoader\ClassMapGenerator;
use Exception;
use BadMethodCallException;
use Doctrine\ODM\MongoDB\SchemaManager;
use MongoDB\Driver\WriteConcern;
use Symfony\Component\Process\Process;

class DoctrineODMSchemaUpdate extends Command
{
    protected $name = 'odm:schema:update';

    protected $description = 'Command for update schema from entities';

    public function handle() : void
    {
        if ($this->hasOption('class')){
            $class = $this->option('class');
        }else{
            $class = null;
        }
        $sm = DocumentManager::getSchemaManager();
        $maxTimeInMS = $this->ask('Insert maximum execution time in ms: ');
        if (!is_numeric($maxTimeInMS)){
            throw new \Exception('Max execution time must be number.');
        }

        $maxTimeInMS = (int)$maxTimeInMS;

        try {
            if (is_string($class)){
                $this->processDocumentIndex($sm, $class, $maxTimeInMS, null);
                $this->info("Successfully updated {$class} entity.");
            }else{
                $this->processIndex($sm, $maxTimeInMS, null);
                $this->info('Successfully updated all entities.');
            }
        }catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }


    protected function processDocumentIndex(SchemaManager $sm, string $document, ?int $maxTimeMs, ?WriteConcern $writeConcern) : void
    {
        $sm->updateDocumentIndexes($document, $maxTimeMs, $writeConcern);
    }

    protected function processIndex(SchemaManager $sm, ?int $maxTimeMs, ?WriteConcern $writeConcern) : void
    {
        $sm->updateIndexes($maxTimeMs, $writeConcern);
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