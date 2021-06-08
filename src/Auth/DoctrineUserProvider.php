<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\Auth;

use Illuminate\Contracts\Auth\UserProvider;
use Doctrine\ODM\MongoDB\DocumentManager;
use Illuminate\Contracts\Hashing\Hasher;
use ReflectionClass;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;

class DoctrineUserProvider implements UserProvider
{
    /**
     * @var Hasher
     */
    protected Hasher $hasher;

    /**
     * @var DocumentManager
     */
    protected DocumentManager $dm;

    /**
     * @var string
     */
    protected string $entity;

    public function __construct(Hasher $hasher, DocumentManager $dm, string  $entity) {
        $this->dm = $dm;
        $this->hasher = $hasher;
        $this->entity = $entity;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $identifier
     *
     * @return Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return $this->getRepository()->find($identifier);
    }

    /**
     * Retrieve a user by by their unique identifier and "remember me" token.
     *
     * @param mixed  $identifier
     * @param string $token
     *
     * @return Authenticatable|null
     */
    public function retrieveByToken($identifier, string $token) : ?Authenticatable
    {
        return $this->getRepository()->findOneBy([
            $this->getEntity()->getAuthIdentifierName() => $identifier,
            $this->getEntity()->getRememberTokenName()  => $token
        ]);
    }


    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param Authenticatable $user
     * @param string          $token
     *
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, string $token) : void
    {
        $user->setRememberToken($token);
        $this->dm->persist($user);
        $this->dm->flush($user);
    }


    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     *
     * @return Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials) : ?Authenticatable
    {
        $criteria = [];
        foreach ($credentials as $key => $value) {
            if (!Str::contains($key, 'password')) {
                $criteria[$key] = $value;
            }
        }

        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param Authenticatable $user
     * @param array           $credentials
     *
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $this->hasher->check($credentials['password'], $user->getAuthPassword());
    }


    /**
     * @return mixed
     */
    protected function getRepository()
    {
        return $this->dm->getRepository($this->entity);
    }

    /**
     * Returns instantiated entity.
     * @return Authenticatable
     */
    protected function getEntity() : object
    {
        $refEntity = new ReflectionClass($this->entity);

        return $refEntity->newInstanceWithoutConstructor();
    }

    /**
     * Returns entity namespace.
     * @return string
     */
    public function getModel() : string
    {
        return $this->entity;
    }
}