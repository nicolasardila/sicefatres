@extends('lombrisoft::layouts.master')

@section('content')
<div class="container mt-5">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="background: #d7f2d2; border-color: #4caf50; color: #2f6627;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="background: #f9d6d5; border-color: #e05145; color: #7a1e1a;">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
        </div>
    @endif

    <h1 class="text-center mb-4 fw-bold" style="color: #2d4a1e; font-family: 'Georgia', serif;">Bienvenido al Lombricultivo 🍃</h1>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
        @forelse ($camas as $cama)
            <div class="col">
                <div class="card shadow-sm h-100 border-start border-4" 
                    style="
                        border-color: 
                            @if($cama->status == 'Disponible') #4caf50 
                            @elseif($cama->status == 'Ocupada') #ffa726 
                            @else #d32f2f 
                            @endif;
                        background: linear-gradient(135deg, #f6fff5, #e3f1d7);
                        border-radius: 12px;
                        font-family: 'Verdana', sans-serif;
                        box-shadow: 0 4px 10px rgba(34, 49, 15, 0.1);
                    ">
                    @if (isset($cama->alerts_count) && $cama->alerts_count > 0)
                        <span class="position-absolute top-0 end-0 badge rounded-pill" 
                            style="background:#d4623e; color:#fff; font-weight:600; margin: 0.3rem 0.5rem; box-shadow:0 0 8px #d4623e;">
                            {{ $cama->alerts_count }}
                        </span>
                    @endif

                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ asset('imgLombri/lombriz4welcome.png') }}" alt="Lombriz" 
                                class="me-3" style="width: 48px; height: 48px; object-fit: contain; filter: drop-shadow(0 1px 1px rgba(0,0,0,0.1));">
                            <div>
                                <h5 class="card-title mb-1" style="color: #3a5510;">Cama {{ $cama->number }}</h5>
                                <p class="card-text mb-1" style="font-weight: 600; color: 
                                    @if($cama->status == 'Disponible') #4caf50 
                                    @elseif($cama->status == 'Ocupada') #ffa726 
                                    @else #d32f2f 
                                    @endif;">
                                    Estado: {{ $cama->status }}
                                </p>
                                <p class="card-text text-muted" style="font-size: 0.9rem;">Inicio: {{ \Carbon\Carbon::parse($cama->start_date)->format('d/m/Y') }}</p>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn flex-fill" onclick="showActivityModal({{ $cama->id }}, {{ $cama->number }})" 
                                style="background: linear-gradient(135deg, #7fb77e, #5c9467); color: #fff; border:none; font-weight:600; box-shadow: 0 4px 8px rgba(92,148,103,0.3);">
                                <i class="fas fa-plus me-1"></i> Actividad
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center text-muted fst-italic">No hay camas registradas. ¡Agrega una cama para comenzar!</p>
            </div>
        @endforelse
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('lombrisoft.admin.camas.create') }}" 
            class="btn btn-success fw-semibold px-5 py-2" 
            style="background: linear-gradient(135deg, #6ebd44, #4a7a18); border:none; font-family: 'Georgia', serif; box-shadow: 0 5px 15px rgba(74,122,24,.4);">
            Agregar Nueva Cama
        </a>
    </div>

    <!-- Modal Crear Actividad -->
    <div class="modal fade" id="activityModal" tabindex="-1" aria-labelledby="activityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content" style="border-radius: 14px; box-shadow: 0 6px 18px rgba(34,49,15,0.2);">
                <div class="modal-header" style="background: #7fb77e; color: #fff; border-bottom: 3px solid #5c9467;">
                    <h5 class="modal-title fw-bold" id="activityModalLabel">Registrar Actividad en Cama <span id="modalBedNumber"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('lombrisoft.admin.bed_activities.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="worm_bed_id" id="modalBedId">

                        <div class="mb-3">
                            <label for="tipo" class="form-label fw-semibold" style="color: #3a5510;">Tipo de Actividad</label>
                            <select class="form-select" id="tipo" name="tipo" required onchange="toggleFields()" style="box-shadow: 0 0 5px #7fb77e;">
                                <option value="" disabled selected>Seleccione un tipo</option>
                                <option value="mantenimiento">Mantenimiento</option>
                                <option value="alimentacion">Alimentación</option>
                                <option value="humedad">Humedad</option>
                                <option value="recoleccion">Recolección</option>
                                <option value="ph">pH</option>
                                <option value="temperatura">Temperatura</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label fw-semibold" style="color: #3a5510;">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" style="box-shadow: 0 0 5px #7fb77e;"></textarea>
                        </div>

                        <div class="mb-3" id="cantidad_alimento_section" style="display: none;">
                            <label for="cantidad_alimento" class="form-label fw-semibold" style="color: #3a5510;">Cantidad de Alimento (kg)</label>
                            <input type="number" class="form-control" id="cantidad_alimento" name="cantidad_alimento" min="1" style="box-shadow: 0 0 5px #7fb77e;">
                            <label for="tipo_alimento" class="form-label fw-semibold mt-2" style="color: #3a5510;">Tipo de Alimento</label>
                            <input type="text" class="form-control" id="tipo_alimento" name="tipo_alimento" style="box-shadow: 0 0 5px #7fb77e;">
                        </div>

                        <div class="mb-3" id="nivel_humedad_section" style="display: none;">
                            <label for="nivel_humedad" class="form-label fw-semibold" style="color: #3a5510;">Nivel de Humedad (%)</label>
                            <input type="number" step="0.1" class="form-control" id="nivel_humedad" name="nivel_humedad" min="0" max="100" style="box-shadow: 0 0 5px #7fb77e;">
                        </div>

                        <div class="mb-3" id="cantidad_recolectada_section" style="display: none;">
                            <label for="cantidad_recolectada" class="form-label fw-semibold" style="color: #3a5510;">Cantidad Recolectada</label>
                            <input type="number" class="form-control" id="cantidad_recolectada" name="cantidad_recolectada" min="1" style="box-shadow: 0 0 5px #7fb77e;">
                            <label for="tipo_recoleccion" class="form-label fw-semibold mt-2" style="color: #3a5510;">Tipo de Recolección</label>
                            <select class="form-select" id="tipo_recoleccion" name="tipo_recoleccion" style="box-shadow: 0 0 5px #7fb77e;">
                                <option value="" selected disabled>Seleccione tipo de recolección</option>
                                <option value="humus">Humus</option>
                                <option value="lixiviado">Lixiviado</option>
                            </select>
                        </div>

                        <div class="mb-3" id="ph_section" style="display: none;">
                            <label for="ph" class="form-label fw-semibold" style="color: #3a5510;">Nivel de pH</label>
                            <input type="number" step="0.1" class="form-control" id="ph" name="ph" min="0" max="14" style="box-shadow: 0 0 5px #7fb77e;">
                        </div>

                        <div class="mb-3" id="temperatura_section" style="display: none;">
                            <label for="temperatura" class="form-label fw-semibold" style="color: #3a5510;">Temperatura (°C)</label>
                            <input type="number" step="0.1" class="form-control" id="temperatura" name="temperatura" min="-50" max="100" style="box-shadow: 0 0 5px #7fb77e;">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_actividad" class="form-label fw-semibold" style="color: #3a5510;">Fecha</label>
                                <input type="date" class="form-control" id="fecha_actividad" name="fecha_actividad" required style="box-shadow: 0 0 5px #7fb77e;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="hora_actividad" class="form-label fw-semibold" style="color: #3a5510;">Hora</label>
                                <input type="time" class="form-control" id="hora_actividad" name="hora_actividad" required style="box-shadow: 0 0 5px #7fb77e;">
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success" style="background: #5c9467; border:none;">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ver Alertas -->
    <div class="modal fade" id="alertsModal" tabindex="-1" aria-labelledby="alertsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header" style="background: #d4623e; color: #fff;">
                    <h5 class="modal-title fw-bold" id="alertsModalLabel">Alertas de la cama <span id="alertsBedNumber"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div id="alertsList" class="d-flex flex-column gap-2"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" style="background: #d4623e; border:none;">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipoActividad = document.getElementById('tipo');
    const fechaInput = document.getElementById('fecha_actividad');
    const horaInput = document.getElementById('hora_actividad');

    // Auto-fill fecha y hora
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    fechaInput.value = `${year}-${month}-${day}`;
    horaInput.value = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;

    function toggleFields() {
        const tipo = tipoActividad.value;
        ['cantidad_alimento_section', 'nivel_humedad_section', 'cantidad_recolectada_section', 'ph_section', 'temperatura_section'].forEach(id => {
            document.getElementById(id).style.display = 'none';
        });

        switch (tipo) {
            case 'alimentacion':
                document.getElementById('cantidad_alimento_section').style.display = 'block';
                break;
            case 'humedad':
                document.getElementById('nivel_humedad_section').style.display = 'block';
                break;
            case 'recoleccion':
                document.getElementById('cantidad_recolectada_section').style.display = 'block';
                break;
            case 'ph':
                document.getElementById('ph_section').style.display = 'block';
                break;
            case 'temperatura':
                document.getElementById('temperatura_section').style.display = 'block';
                break;
        }
    }

    tipoActividad.addEventListener('change', toggleFields);
    toggleFields();

    window.showActivityModal = function(bedId, bedNumber) {
        document.getElementById('modalBedId').value = bedId;
        document.getElementById('modalBedNumber').innerText = bedNumber;
        const modal = new bootstrap.Modal(document.getElementById('activityModal'));
        modal.show();
    };

    window.openAlertsModal = function(bedId, bedNumber) {
        document.getElementById('alertsBedNumber').innerText = bedNumber;
        let alerts = @json($camas);
        let cama = alerts.find(c => c.id === bedId);
        let container = document.getElementById('alertsList');
        container.innerHTML = '';

        if (cama.activity_alerts && cama.activity_alerts.length > 0) {
            cama.activity_alerts.forEach(al => {
                container.innerHTML += `
                    <div class="border p-2 rounded" style="background: #f9f4ec; border-color:#d1bfa7;">
                        <strong>Tipo:</strong> ${al.activity_type} <br>
                        <strong>Frecuencia:</strong> ${al.frequency_days} días
                    </div>
                `;
            });
        } else {
            container.innerHTML = '<p class="text-muted text-center fst-italic">No tiene alertas.</p>';
        }

        const modal = new bootstrap.Modal(document.getElementById('alertsModal'));
        modal.show();
    };
});
</script>
@endsection
