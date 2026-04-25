<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clinic extends Model
{
    use HasFactory;
    protected $fillable = ['category_id', 'name_ar', 'name_en'];
    public function clinicTime()
    {

        return $this->hasMany(ClinicTime::class);
    }
    public function categories(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function physicains(): BelongsToMany
    {
        return $this->belongsToMany(Physicain::class, 'clinic_providers', 'clinic_id', 'physicain_id')->withTimestamps();;
    }
    public function clients()
    {

        return $this->hasMany(Client::class);
    }

    public function client(): BelongsToMany
    {

        return $this->belongsToMany(Client::class, 'client_clinics', 'clinic_id', 'client_id')->withTimestamps();
    }
    public function clientClinics()
    {
        return $this->hasMany(ClientClinic::class);
    }
}
