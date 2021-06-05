<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\Facades;

use Illuminate\Support\Facades\Facade;
use Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteManager as DoctrineSoftDeleteManager;

class SoftDeleteManager extends Facade
{
    protected static function getFacadeAccessor() : string
    {
        return 'SoftDeleteManager';
    }
}