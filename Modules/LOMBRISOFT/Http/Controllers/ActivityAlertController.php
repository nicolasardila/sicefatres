<?php

namespace Modules\LOMBRISOFT\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\LOMBRISOFT\Entities\ActivityAlert;
use Modules\LOMBRISOFT\Entities\WormBed;
use Modules\LOMBRISOFT\Entities\BedActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Modules\LOMBRISOFT\Emails\PendingAlertsMail;


class ActivityAlertController extends Controller
{
    public function index(Request $request)
    {
        // Consulta base con relaciones
        $query = ActivityAlert::with('wormBed');
        
        // Filtrar por tipo de actividad
        if ($request->filled('tipo')) {
            $query->where('activity_type', $request->tipo);
        }
        
        // Filtrar por cama
        if ($request->filled('cama_id')) {
            $query->where('worm_bed_id', $request->cama_id);
        }
        
        // Obtener todas las alertas para procesamiento
        $allAlerts = $query->orderBy('worm_bed_id')->orderBy('activity_type')->get();
        
        // Procesar cada alerta para calcular estados
        foreach ($allAlerts as $alert) {
            // 1) Buscar última actividad registrada
            $lastActivity = BedActivity::where('worm_bed_id', $alert->worm_bed_id)
                ->where('tipo', $alert->activity_type)
                ->orderBy('fecha_actividad', 'desc')
                ->first();

            if ($lastActivity) {
                $alert->last_execution = Carbon::parse($lastActivity->fecha_actividad);
                $alert->next_expected = $alert->last_execution->copy()->addDays($alert->frequency_days);
            } else {
                $alert->last_execution = null;
                $alert->next_expected = null;
            }

            // 2) Calcular estado dinámico
            if ($alert->next_expected) {
                $today = Carbon::today();

                if ($alert->next_expected->lt($today)) {
                    $alert->calculated_status = 'vencida';
                } elseif ($alert->next_expected->between($today, $today->copy()->addDays($alert->warning_days))) {
                    $alert->calculated_status = 'proxima';
                } else {
                    $alert->calculated_status = 'activa';
                }
            } else {
                $alert->calculated_status = 'activa';
            }
        }

        // 3) Aplicar filtro de estado si existe
        if ($request->filled('estado')) {
            if ($request->estado == 'vencidas') {
                $allAlerts = $allAlerts->where('calculated_status', 'vencida');
            } elseif ($request->estado == 'proximas') {
                $allAlerts = $allAlerts->where('calculated_status', 'proxima');
            } elseif ($request->estado == 'activas') {
                $allAlerts = $allAlerts->where('is_active', true);
            } elseif ($request->estado == 'inactivas') {
                $allAlerts = $allAlerts->where('is_active', false);
            }
        }

        // Paginación manual
        $page = $request->get('page', 1);
        $perPage = 10;
        $alerts = $allAlerts->slice(($page - 1) * $perPage, $perPage)->all();
        $alerts = new \Illuminate\Pagination\LengthAwarePaginator(
            $alerts, 
            $allAlerts->count(), 
            $perPage, 
            $page, 
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $camas = WormBed::all();

        return view('lombrisoft::activity_alerts.index', compact('alerts', 'camas'));
    }

    // El resto de tus métodos se mantienen igual...
    public function create()
    {
        $wormBeds = WormBed::all();
        $activityTypes = [
            'mantenimiento' => 'Mantenimiento',
            'alimentacion'  => 'Alimentación',
            'humedad'       => 'Control de Humedad',
            'recoleccion'   => 'Recolección',
            'ph'            => 'Control de pH',
            'temperatura'   => 'Control de Temperatura',
        ];

        return view('lombrisoft::activity_alerts.create', compact('wormBeds', 'activityTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'worm_bed_id'    => 'required|exists:wormsBeds,id',
            'activity_type'  => 'required|in:mantenimiento,alimentacion,humedad,recoleccion,ph,temperatura',
            'frequency_days' => 'required|integer|min:1',
            'warning_days'   => 'required|integer|min:0|lte:frequency_days',
            'is_active'      => 'sometimes|boolean'
        ]);

        // Verificar duplicado
        if (ActivityAlert::where('worm_bed_id', $validated['worm_bed_id'])
            ->where('activity_type', $validated['activity_type'])->exists()) {
            return back()->withErrors([
                'activity_type' => 'Ya existe una alerta para este tipo de actividad en la cama seleccionada'
            ])->withInput();
        }

        ActivityAlert::create($validated);

        return redirect()->route('lombrisoft.admin.activity_alerts.index')
            ->with('success', 'Alerta creada exitosamente');
    }

    public function show($id)
    {
        $alert = ActivityAlert::with('wormBed')->findOrFail($id);
        return view('lombrisoft::activity_alerts.show', compact('alert'));
    }

    public function edit($id)
    {
        $alert = ActivityAlert::findOrFail($id);
        $wormBeds = WormBed::all();

        $activityTypes = [
            'mantenimiento' => 'Mantenimiento',
            'alimentacion'  => 'Alimentación',
            'humedad'       => 'Control de Humedad',
            'recoleccion'   => 'Recolección',
            'ph'            => 'Control de pH',
            'temperatura'   => 'Control de Temperatura',
        ];

        return view('lombrisoft::activity_alerts.edit', compact('alert', 'wormBeds', 'activityTypes'));
    }

    public function update(Request $request, $id)
    {
        $alert = ActivityAlert::findOrFail($id);

        $validated = $request->validate([
            'worm_bed_id'    => 'required|exists:wormsBeds,id',
            'activity_type'  => 'required|in:mantenimiento,alimentacion,humedad,recoleccion,ph,temperatura',
            'frequency_days' => 'required|integer|min:1',
            'warning_days'   => 'required|integer|min:0|lte:frequency_days',
            'is_active'      => 'sometimes|boolean',
        ]);

        // Verificar duplicado con exclusión
        if (ActivityAlert::where('worm_bed_id', $validated['worm_bed_id'])
            ->where('activity_type', $validated['activity_type'])
            ->where('id', '!=', $id)->exists()) {
            return back()->withErrors([
                'activity_type' => 'Ya existe una alerta para este tipo de actividad en la cama seleccionada'
            ])->withInput();
        }

        $alert->update($validated);

        return redirect()->route('lombrisoft.admin.activity_alerts.index')
            ->with('success', 'Alerta actualizada exitosamente');
    }

    public function destroy($id)
    {
        $alert = ActivityAlert::findOrFail($id);
        $alert->delete();

        return redirect()->route('lombrisoft.admin.activity_alerts.index')
            ->with('success', 'Alerta eliminada exitosamente');
    }
    
    public function getPendingAlerts()
    {
        $alerts = ActivityAlert::with('wormBed')
            ->get()
            ->map(function ($alert) {
                // Calcular última actividad y próxima ejecución
                $lastActivity = BedActivity::where('worm_bed_id', $alert->worm_bed_id)
                    ->where('tipo', $alert->activity_type)
                    ->orderBy('fecha_actividad', 'desc')
                    ->first();

                if ($lastActivity) {
                    $alert->last_execution = Carbon::parse($lastActivity->fecha_actividad);
                    $alert->next_expected = $alert->last_execution->copy()->addDays($alert->frequency_days);
                } else {
                    $alert->last_execution = null;
                    $alert->next_expected = null;
                }

                // Calcular estado dinámico
                if ($alert->next_expected) {
                    $today = Carbon::today();
                    if ($alert->next_expected->lt($today)) {
                        $alert->calculated_status = 'vencida';
                    } elseif ($alert->next_expected->between($today, $today->copy()->addDays($alert->warning_days))) {
                        $alert->calculated_status = 'proxima';
                    } else {
                        $alert->calculated_status = 'activa';
                    }
                } else {
                    $alert->calculated_status = 'activa';
                }

                return $alert;
            })
            ->where('calculated_status', 'proxima')
            ->values(); // Reindexar el array

        return response()->json(['alerts' => $alerts, 'count' => $alerts->count()]);
    }
   public function sendPendingAlertsEmail()
{
    $camas = WormBed::all();

    foreach ($camas as $cama) {
        // Traer la última actividad de cada tipo
        $activities = BedActivity::where('worm_bed_id', $cama->id)
            ->select('tipo', DB::raw('MAX(fecha_actividad) as ultima_fecha'))
            ->groupBy('tipo')
            ->get()
            ->map(function ($actividad) use ($cama) {
                // Buscar el registro completo de esa última actividad
                return BedActivity::where('worm_bed_id', $cama->id)
                    ->where('tipo', $actividad->tipo)
                    ->whereDate('fecha_actividad', $actividad->ultima_fecha)
                    ->first();
            })
            ->filter(function ($activity) {
                // Validar que esté vencida
                $nextExpected = Carbon::parse($activity->fecha_actividad)
                    ->addDays($activity->frequency_days ?? 0);
                return $nextExpected->lt(Carbon::today());
            });

        if ($activities->isNotEmpty()) {
            Mail::to('ardilanicolas71@gmail.com')
                ->send(new AlertaCamaVencida($cama, $activities));
        }
    }

    return response()->json(['message' => 'Se enviaron las alertas solo a la última actividad vencida de cada tipo.']);
}



}