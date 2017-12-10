<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifyUser extends Model
{
    protected $fillable = [
        'user_id',
        'token'
    ];

    /**
     * Faz o relacionamento de dependencia a um usuario.
     * Cada registro de geracao de token vai depender de um usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function User()
    {
        return $this->belongsTo(User::class);
    }
}
