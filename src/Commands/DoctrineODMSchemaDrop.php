<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\Commands;


use Illuminate\Console\Command;
use Doctrine\ODM\MongoDB\SchemaManager;
use LaravelDoctrineODM\Facades\DocumentManager;
use MongoDB\Driver\WriteConcern;


class DoctrineODMSchemaDrop extends Command
{
    protected $name = 'odm:schema:drop';

    protected $description = 'Drop databases, collections and indexes for your documents. For drop databases that is option "db". For drop Collection that is option "collection". For drop indexes that is option "index"';

    private array $dropOver = ['index', 'collection', 'db'];

    public function handle() : void
    {
        $drop = [];
        foreach ($this->dropOver as $option){
            if ($this->hasOption($option)){
                $drop[] = $option;
            }
        }
        $drop = empty($drop) ? $this->dropOver : $drop;

        $class = $this->hasOption('class')? $this->option('class') : null;
        $sm = DocumentManager::getSchemaManager();

        foreach ($drop as $option){
            try {
                if (is_string($class)){
                    $this->{'processDocument'. ucfirst($option)}($sm, $class, 1000, null);
                }else{
                    $this->{'process'. ucfirst($option)}($sm, 1000, null);
                }

                $this->info(sprintf(
                    'Dropped %s%s for %s',
                    $option,
                    is_string($class) ? ($option === 'index' ? '(es)' : '') : ($option === 'index' ? 'es' : 's'),
                    is_string($class) ? $class : 'all classes'
                ));
            }catch (\Throwable $exception){
                $this->error($exception->getMessage());
            }
        }

    }

    protected function processDocumentCollection(SchemaManager $sm, string $document, ?int $maxTimeMs, ?WriteConcern $writeConcern) : void
    {
        $sm->dropDocumentCollection($document, $maxTimeMs, $writeConcern);
    }

    protected function processCollection(SchemaManager $sm, ?int $maxTimeMs, ?WriteConcern $writeConcern) : void
    {
        $sm->dropCollections($maxTimeMs, $writeConcern);
    }

    protected function processDocumentDb(SchemaManager $sm, string $document, ?int $maxTimeMs, ?WriteConcern $writeConcern) : void
    {
        $sm->dropDocumentDatabase($document, $maxTimeMs, $writeConcern);
    }

    protected function processDb(SchemaManager $sm, ?int $maxTimeMs, ?WriteConcern $writeConcern) : void
    {
        $sm->dropDatabases($maxTimeMs, $writeConcern);
    }

    protected function processDocumentIndex(SchemaManager $sm, string $document, ?int $maxTimeMs, ?WriteConcern $writeConcern) : void
    {
        $sm->deleteDocumentIndexes($document, $maxTimeMs, $writeConcern);
    }

    protected function processIndex(SchemaManager $sm, ?int $maxTimeMs, ?WriteConcern $writeConcern) : void
    {
        $sm->deleteIndexes($maxTimeMs, $writeConcern);
    }
}