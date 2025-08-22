<?php

namespace Modules\LOMBRISOFT\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Modules\LOMBRISOFT\Entities\WormBed;
use Modules\LOMBRISOFT\Entities\BedActivity;
use Modules\LOMBRISOFT\Mail\AlertaCamaVencida;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class EnviarAlertasCamas extends Command
{
    protected $signature = 'lombrisoft:alertas-camas';
    protected $description = 'Enviar correos de alerta de camas con actividades vencidas';

    public function handle()
{
    $camas = WormBed::all();

    foreach ($camas as $cama) {
        $activities = BedActivity::where('worm_bed_id', $cama->id)
            ->select('tipo', DB::raw('MAX(fecha_actividad) as ultima_fecha'))
            ->groupBy('tipo')
            ->get()
            ->map(function ($actividad) use ($cama) {
                return BedActivity::where('worm_bed_id', $cama->id)
                    ->where('tipo', $actividad->tipo)
                    ->whereDate('fecha_actividad', $actividad->ultima_fecha)
                    ->first();
            })
            ->filter(function ($activity) {
                $nextExpected = Carbon::parse($activity->fecha_actividad)
                    ->addDays($activity->frequency_days ?? 0);
                return $nextExpected->lt(Carbon::today());
            });

        if ($activities->isNotEmpty()) {
            Mail::to('paceburo21@gmail.com')
                ->send(new AlertaCamaVencida($cama, $activities));
        }
    }

    $this->info('Correos enviados solo con la última actividad vencida de cada tipo.');
}

}
