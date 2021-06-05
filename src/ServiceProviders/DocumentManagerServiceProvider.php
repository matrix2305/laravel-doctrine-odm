<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\ServiceProviders;


use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\Persistence\ObjectManager;
use Illuminate\Support\ServiceProvider;
use MongoDB\Client;

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
        $this->app->bind(ObjectManager::class, function (){
            return $this->app->make('DocumentManager');
        });
    }
}