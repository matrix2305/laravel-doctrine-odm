<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\ServiceProviders;


use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\Persistence\ObjectManager;
use Illuminate\Support\ServiceProvider;
use MongoDB\Client;
use Doctrine\ODM\MongoDB\SoftDelete\UnitOfWork;
use Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteManager;
use Doctrine\Common\EventManager;

class DocumentManagerServiceProvider extends ServiceProvider
{
    public function boot() : void
    {

    }

    public function register() : void
    {
        $this->app->singleton('DocumentManager', function () {
            $doctrineConfig = config('doctrine');
            $databaseConfig = config('database');
            $connectionConfig = $databaseConfig['connections'][$doctrineConfig['connection']];
            $dmConfig = new Configuration();
            $dmConfig->setProxyDir($doctrineConfig['doctrine_dm']['proxies']['path']);
            $dmConfig->setProxyNamespace($doctrineConfig['doctrine_dm']['proxies']['namespace']);
            $dmConfig->setHydratorDir($doctrineConfig['doctrine_dm']['hydrators']['path']);
            $dmConfig->setHydratorNamespace($doctrineConfig['doctrine_dm']['hydrators']['namespace']);
            $dmConfig->setMetadataDriverImpl(AnnotationDriver::create($doctrineConfig['doctrine_dm']['paths']));
            $dmConfig->setPersistentCollectionDir($doctrineConfig['doctrine_dm']['persistent-collections']['path']);
            $dmConfig->setPersistentCollectionNamespace($doctrineConfig['doctrine_dm']['persistent-collections']['namespace']);
            $dmConfig->setDefaultDB($connectionConfig['database']);

            $client = new Client($connectionConfig['dsn'], [
                'username' => $connectionConfig['username'],
                'password' => $connectionConfig['password'],
                'port' => $connectionConfig['port'],
            ], ['typeMap' => DocumentManager::CLIENT_TYPEMAP]);

            return DocumentManager::create($client, $dmConfig);
        });
        $this->app->singltone('SoftDeleteManager', function (){
            $softDeleteConf = new \Doctrine\ODM\MongoDB\SoftDelete\Configuration();
            $softDeleteConf->setDeletedFieldName(config('laravel_dm.soft_deletes.field_name'));
            return new SoftDeleteManager($this->app->make(DocumentManager::class), $softDeleteConf, new EventManager());
        });

        $this->app->bind(DocumentManager::class, function (){
            return $this->app->make('DocumentManager');
        });

        $this->app->bind(SoftDeleteManager::class, function (){
            return $this->app->make('SoftDeleteManager');
        });
    }

    public function provides() : array
    {
        return ['SoftDeleteManager', 'DocumentManager'];
    }
}