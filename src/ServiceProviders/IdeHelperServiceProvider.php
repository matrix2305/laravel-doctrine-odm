<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\ServiceProviders;


use Illuminate\Support\ServiceProvider;
use Exception;
use LaravelDoctrineODM\Commands\DoctrineODMGenerateHydrators;
use LaravelDoctrineODM\Commands\DoctrineODMGeneratePersistentCollections;
use LaravelDoctrineODM\Commands\DoctrineODMGenerateProxies;
use LaravelDoctrineODM\Commands\DoctrineODMQuery;
use LaravelDoctrineODM\Commands\DoctrineODMSchemaCreate;
use LaravelDoctrineODM\Commands\DoctrineODMSchemaDrop;
use LaravelDoctrineODM\Commands\DoctrineODMSchemaShard;
use LaravelDoctrineODM\Commands\DoctrineODMSchemaUpdate;
use LaravelDoctrineODM\Commands\DoctrineODMSchemaValidate;

class IdeHelperServiceProvider extends ServiceProvider
{
    public function boot() : void
    {
        if ($this->app->runningInConsole()){
            $this->bootForConsole();
        }
    }

    public function bootForConsole() : void
    {
        $this->publishes([
            __DIR__.'/../../config/database.php' => config_path('database.php')
        ], 'database.config');

        $this->publishes([
           __DIR__.'/../../config/doctrine.php' => config_path('doctrine.php')
        ], 'doctrine.config');
    }

    public function register() : void
    {
        $this->app->singleton('command.odm-schema-update', function (){
            return new DoctrineODMSchemaUpdate();
        });
        $this->app->singleton('command.odm-schema-validate', function (){
            return new DoctrineODMSchemaValidate();
        });
        $this->app->singleton('command.odm-schema-shard', function (){
            return new DoctrineODMSchemaShard();
        });
        $this->app->singleton('command.odm-schema-drop', function (){
            return new DoctrineODMSchemaDrop();
        });
        $this->app->singleton('command.odm-schema-create', function (){
            return new DoctrineODMSchemaCreate();
        });
        $this->app->singleton('command.odm-generate-proxies', function (){
            return new DoctrineODMGenerateProxies();
        });
        $this->app->singleton('command.odm-generate-hydrators', function (){
            return new DoctrineODMGenerateHydrators();
        });
        $this->app->singleton('command.odm-query', function (){
            return new DoctrineODMQuery();
        });
        $this->app->singleton('command.odm-generate-persistent-collections', function (){
            return new DoctrineODMGeneratePersistentCollections();
        });
        $this->commands('command.odm-schema-update');
        $this->commands('command.odm-schema-validate');
        $this->commands('command.odm-schema-shard');
        $this->commands('command.odm-schema-drop');
        $this->commands('command.odm-schema-create');
        $this->commands('command.odm-generate-proxies');
        $this->commands('command.odm-query');
        $this->commands('command.odm-generate-hydrators');
        $this->commands('command.odm-generate-persistent-collections');
    }

    public function provides() : array
    {
        return ['command.odm-schema-update', 'command.odm-schema-validate', 'command.odm-schema-shard', 'command.odm-schema-drop', 'command.odm-schema-create', 'command.odm-generate-proxies', 'command.odm-query', 'command.odm-generate-hydrators', 'command.odm-generate-persistent-collections'];
    }
}