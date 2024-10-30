<?php

// app/Models/ColumnConfiguration.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColumnConfiguration extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'column_name','table_name', 'is_visible'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
