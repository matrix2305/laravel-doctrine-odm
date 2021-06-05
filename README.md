# LaravelDoctrineODM

This library provides a Doctrine ODM wrapper for Laravel.

## Installation

The suggested installation method is via [composer](https://getcomposer.org/):

```sh
composer require "matrix98/laravel_doctrine_odm:~1.0.0"
```
1. Add MongoDB configuration parameters in 'config/database.php' ('connection' array index child) : 
``` php
'mongodb'   => [
            'driver'        => 'mongodb',
            'dsn'           => 'mongodb://'.env('DB_HOST'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'port' => env('DB_PORT', '27017'),
            'database'      => env('DB_DATABASE'), // Default DB to perform queries against (not authenticate against)
            'retryConnect'  => 2, // Number of connection retry attempts before failing (doctrine feature)
            'retryQuery'    => 1, // Number of query retry attempts before failing (doctrine feature)
            'options'       => [
                // mapped to MongoClient $options
                'connectTimeoutMS'  => 1000, // Connection attempt timeout (milliseconds)
                'wTimeoutMS'        => 2500, // DB write attempt timeout (milliseconds)
                'socketTimeoutMS'   => 10000, // Client side socket timeout (milliseconds)
                'w'                 => 'majority', // Default write concern (normally w=1)
                'readPreference'    => 'primaryPreferred', // Default read preference
            ],
            'driverOptions' => [ // mapped to MongoClient $driverOptions (e.g. for SSL stream context)

            ]
        ],
```

2. Publish Laravel service provider:

```sh
php artisan vendor:publish --provider=LaravelDoctrineODM\ServiceProviders\IdeHelperServiceProvider
```

## Using

Example to get DocumentManager instance with dependency injection: 

``` php
    use Doctrine\ODM\MongoDB\DocumentManager;

    class TestRepository {
        protected DocumentManager $dm;
    
        public function __construct(DocumentManager $dm)
        {
            $this->dm = $dm;
        }
    }
```

Example with DocumentManager facade:

```php
use LaravelDoctrineODM\Facades\DocumentManager;

$sm = DocumentManager::getSchemaManager();
```
