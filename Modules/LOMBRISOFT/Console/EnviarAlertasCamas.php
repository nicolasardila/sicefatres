<?php

namespace Modules\LOMBRISOFT\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Modules\LOMBRISOFT\Entities\WormBed;
use Modules\LOMBRISOFT\Entities\BedActivity;
use Modules\LOMBRISOFT\Mail\AlertaCamaVencida;
use Carbon\Carbon;

class EnviarAlertasCamas extends Command
{
    protected $signature = 'lombrisoft:alertas-camas';
    protected $description = 'Enviar correos de alerta de camas con actividades vencidas';

    public function handle()
    {
        // Obtener todas las camas
        $camas = WormBed::all();

        foreach ($camas as $cama) {
            // Filtrar solo actividades vencidas de esta cama
            $activities = BedActivity::where('worm_bed_id', $cama->id)
                ->get()
                ->filter(function ($activity) {
                    $nextExpected = Carbon::parse($activity->fecha_actividad)
                        ->addDays($activity->frequency_days ?? 0);
                    return $nextExpected->lt(Carbon::today());
                });

            // Solo enviar correo si hay actividades vencidas
            if ($activities->isNotEmpty()) {
                Mail::to('ardilanicolas71@gmail.com')
                    ->send(new AlertaCamaVencida($cama, $activities));
            }
        }

        $this->info('Correos enviados correctamente.');
    }
}
