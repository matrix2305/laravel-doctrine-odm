<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\Facades;


use Illuminate\Support\Facades\Facade;
use Doctrine\ODM\MongoDB\DocumentManager as DoctrineDocumentManager;

class DocumentManager extends Facade
{
    protected static function getFacadeAccessor() : string
    {
        return 'DocumentManager';
    }
}