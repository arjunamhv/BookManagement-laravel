<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model implements Authenticatable
{
    use HasFactory;
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = ['username', 'password', 'token'];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'user_id', 'id');
    }


    public function getAuthIdentifierName(): string
    {
        return 'username';
    }
    public function getAuthIdentifier(): string
    {
        return $this->username;
    }
    public function getAuthPassword(): string
    {
        return $this->password;
    }
    public function getAuthPasswordName(): string
    {
        return 'password';
    }
    public function getRememberToken(): string
    {
        return $this->token;
    }
    public function setRememberToken($value): void
    {
        $this->token = $value;
    }
    public function getRememberTokenName(): string
    {
        return 'token';
    }
}
