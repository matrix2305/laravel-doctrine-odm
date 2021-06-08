<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\Traits\Auth;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

trait Authenticatable
{
    /**
     * @ORM\Field(type="string")
     */
    protected string $password;

    /**
     * @ORM\Field (type="string", nullable=true)
     */
    protected ?string $rememberToken;

    /**
     * Get the column name for the primary key
     * @return string
     */
    public function getAuthIdentifierName() : string
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     * @return mixed
     */
    public function getAuthIdentifier() : string
    {
        $name = $this->getAuthIdentifierName();

        return $this->{$name};
    }

    /**
     * @return string
     */
    public function getPassword() : string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password) : void
    {
        $this->password = $password;
    }

    /**
     * Get the password for the user.
     * @return string
     */
    public function getAuthPassword() : string
    {
        return $this->getPassword();
    }

    /**
     * Get the token value for the "remember me" session.
     * @return string|null
     */
    public function getRememberToken() : ?string
    {
        return $this->rememberToken;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     *
     * @return void
     */
    public function setRememberToken(string $value) : void
    {
        $this->rememberToken = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     * @return string
     */
    public function getRememberTokenName() : string
    {
        return 'rememberToken';
    }
}