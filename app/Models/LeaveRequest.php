<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'from', 'to', 'status', 'type', 'note', 'attachment'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
