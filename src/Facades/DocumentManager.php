<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\Facades;


use Illuminate\Support\Facades\Facade;

class DocumentManager extends Facade
{
    protected static function getFacadeAccessor() : string
    {
        return 'DocumentManager';
    }
}