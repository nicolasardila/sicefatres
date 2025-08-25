<?php

namespace Modules\LOMBRISOFT\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\LOMBRISOFT\Entities\WormBed;

class BedActivity extends Model
{
    use HasFactory;

    protected $table = 'bed_activities';

    protected $fillable = [
        'worm_bed_id',
        'tipo',
        'descripcion',
        'fecha_actividad',
        'hora_actividad',
    ];

    // Relación con cama
    public function wormBed()
    {
        return $this->belongsTo(WormBed::class, 'worm_bed_id');
    }

    // Relaciones polimorfas o directas a cada tipo de actividad
    public function feeding()
    {
        return $this->hasOne(FeedingActivity::class, 'bed_activity_id');
    }

    public function moisture()
    {
        return $this->hasOne(MoistureActivity::class, 'bed_activity_id');
    }

    public function harvest()
    {
        return $this->hasOne(HarvestActivity::class, 'bed_activity_id');
    }

    public function ph()
    {
        return $this->hasOne(PhActivity::class, 'bed_activity_id');
    }

    public function temperature()
    {
        return $this->hasOne(TemperatureActivity::class, 'bed_activity_id');
    }

    // Alerta asociada a la actividad (por cama y tipo)
    public function alert()
    {
        return $this->hasOne(ActivityAlert::class, 'worm_bed_id', 'worm_bed_id')
            ->where('activity_type', $this->tipo);
    }
}
