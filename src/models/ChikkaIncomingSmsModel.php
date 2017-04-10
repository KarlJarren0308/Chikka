<?php

namespace KarlMacz\Chikka\Models;

use Illuminate\Database\Eloquent\Model;

class ChikkaIncomingSmsModel extends Model
{
    protected $table = 'chikka_incoming_sms';

    public function requestInfo() {
        return $this->hasOne('KarlMacz\Chikka\Models\ChikkaOutgoingSmsModel', 'request_id', 'request_id');
    }
}
