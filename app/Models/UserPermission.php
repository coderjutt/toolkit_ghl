<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class UserPermission extends Model
{
    use HasFactory;
    protected $table = 'user_module_permissions';

    protected $fillable = [
        'user_id',
        'module',
        'permission',
    ];
}
