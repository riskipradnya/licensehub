<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable; // <-- 1. Tambahkan baris ini di atas

class NotificationRecipient extends Model
{
    // 2. Tambahkan Notifiable di dalam class (bersama HasFactory)
    use HasFactory, Notifiable; 

    protected $fillable = [
        'name',
        'email',
        'whatsapp',
        'is_active',
    ];
}