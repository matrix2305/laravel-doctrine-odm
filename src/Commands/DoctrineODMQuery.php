<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\Commands;


use Illuminate\Console\Command;
use LogicException;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;


class DoctrineODMQuery extends Command
{
    protected $signature = 'odm:query';

    protected $description = 'Query mongodb and inspect the outputted results from your document classes.';

    public function handle() : void
    {
        $query = $this->ask('Insert your query ');
        assert(is_string($query));

        $dm = app('DocumentManager');
        $qb = $dm->getRepository($this->ask('Insert namespace of entity '))->createQueryBuilder();
        $qb->setQueryArray((array)json_decode($query));
        $qb->hydrate((bool)$this->hasOption('hydrate'));

        $skip = $this->ask('Insert skip: ');
        if ($skip !== null) {
            if (! is_numeric($skip)) {
                throw new LogicException("Option 'skip' must contain an integer value");
            }

            $qb->skip((int) $skip);
        }

        $limit = $this->ask('Insert limit ');
        if ($limit !== null) {
            if (! is_numeric($limit)) {
                throw new LogicException("Option 'limit' must contain an integer value");
            }

            $qb->limit((int) $limit);
        }

        dd($qb->getQuery()->toArray());
    }
}