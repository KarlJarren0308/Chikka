<?php

namespace KarlMacz\Chikka\Models;

use Illuminate\Database\Eloquent\Model;

class ChikkaOutgoingSmsModel extends Model
{
    protected $table = 'chikka_outgoing_sms';

    public function requestInfo() {
        return $this->belongsTo('KarlMacz\Chikka\Models\ChikkaIncomingSmsModel', 'request_id', 'request_id');
    }
}
