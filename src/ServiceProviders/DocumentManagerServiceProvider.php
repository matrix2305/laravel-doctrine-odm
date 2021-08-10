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
use LaravelDoctrineODM\Auth\DoctrineUserProvider;

class DocumentManagerServiceProvider extends ServiceProvider
{
    public function boot() : void
    {
        $this->extendAuthManager();
    }

    public function register() : void
    {
        $this->app->singleton('DocumentManager', function ($app) {
            if ($app->runningInConsole()){
                $doctrineConfig = include(base_path().'/config/doctrine.php');
                $databaseConfig = include(base_path().'/config/database.php');
            }else{
                $doctrineConfig = config('doctrine');
                $databaseConfig = config('database');
            }
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
        $this->app->singleton('SoftDeleteManager', function ($app){
            $softDeleteConf = new \Doctrine\ODM\MongoDB\SoftDelete\Configuration();
            $softDeleteConf->setDeletedFieldName(config('laravel_dm.soft_deletes.field_name'));
            return new SoftDeleteManager($app->make(DocumentManager::class), $softDeleteConf, new EventManager());
        });

        $this->app->alias('DocumentManager', DocumentManager::class);
        $this->app->alias('SoftDeleteManager', SoftDeleteManager::class);
    }

    public function provides() : array
    {
        return ['SoftDeleteManager', 'DocumentManager'];
    }

    protected function extendAuthManager() : void
    {
        if ($this->app->bound('auth')) {
            $this->app->make('auth')->provider('doctrine', function ($app, $config) {
                $entity = $config['model'];

                $dm = $this->app->make(DocumentManager::class);

                return new DoctrineUserProvider(
                    $app['hash'],
                    $dm,
                    $entity
                );
            });
        }
    }
}