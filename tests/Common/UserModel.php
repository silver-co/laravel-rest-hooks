<?php

namespace SilverCO\RestHooks\Tests\Common;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User;

class UserModel extends User implements Authenticatable
{
    use HasFactory;

    protected $table = 'users';

    /**
     * Create a new factory instance for the model.
     *
     * @return \SilverCO\Tests\Common\UserFactory
     */
    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function getForeignKey()
    {
        return 'user_id';
    }
}
