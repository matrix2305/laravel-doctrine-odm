<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\Commands;


use Doctrine\ODM\MongoDB\DocumentManager;
use Illuminate\Console\Command;
use Doctrine\Common\Cache\VoidCache;

class DoctrineODMSchemaValidate extends Command
{
    protected $name = 'odm:schema:validate';

    protected $description = 'Validates if document mapping stays the same after serializing into cache.';

    public function handle() : void
    {
        $dm = app('DocumentManager');
        assert($dm instanceof DocumentManager);
        $metadataFactory = $dm->getMetadataFactory();
        $metadataFactory->setCacheDriver(new VoidCache());

        $errors = 0;
        foreach ($metadataFactory->getAllMetadata() as $meta){
            if ($meta == unserialize(serialize($meta))){
                continue;
            }
            $errors++;
            $this->error("{$meta->getName()} has mapping issues.");
        }

        if ($errors){
            $this->error("{$errors} document(s) have mapping issues.");
        }else{
            $this->info('All document are OK!');
        }
    }
}