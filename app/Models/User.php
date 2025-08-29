<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Carts\Cart;
use App\Enums\UserRoleEnum;
use App\Models\Orders\Order;
use App\Models\Supports\File;
use App\Models\Payments\Invoice;
use App\Models\Customers\CustomerStat;
use Illuminate\Notifications\Notifiable;
use App\Models\Customers\ShippingAddress;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasUlids, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'username'          => 'string',
        'email'             => 'string',
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'role'              => UserRoleEnum::class,
    ];

    // *****************************************************************************************************************
    // RELATIONSHIPS
    // *****************************************************************************************************************

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function avatar(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function shippingAddresses(): HasMany
    {
        return $this->hasMany(ShippingAddress::class);
    }

    public function defaultShippingAddress(): HasOne
    {
        return $this->hasOne(ShippingAddress::class)
            ->where('is_default', true);
    }

    public function customerStat(): HasOne
    {
        return $this->hasOne(CustomerStat::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
