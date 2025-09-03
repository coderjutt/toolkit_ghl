<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class UserScriptPermission extends Model
{
    use HasFactory;
    protected $table = 'user_script_permissions';

    protected $fillable = [
        'user_id',
        'permission',
    ];
}
