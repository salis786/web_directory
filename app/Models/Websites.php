<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Websites extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'url', 'description', 'added_by'];

    public function categories()
    {
        return $this->belongsToMany(Categories::class, 'categories_websites', 'website_id', 'category_id');
    }

    public function votes()
    {
        return $this->hasMany(Votes::class, 'website_id')->where('vote', true);
    }

    public function getVotesCountAttribute()
    {
        return $this->votes()->count();
    }
}
