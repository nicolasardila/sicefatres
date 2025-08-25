@extends('lombrisoft::layouts.master')

@section('content')
<div class="container mt-5">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    @if ($alerts->isEmpty())
        <div class="alert alert-info text-center">No hay alertas configuradas.</div>
    @else
        <div class="card shadow-lg rounded mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Filtrar Alertas</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('lombrisoft.admin.activity_alerts.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="filter_tipo" class="form-label">Tipo de Actividad</label>
                            <select class="form-select" id="filter_tipo" name="tipo">
                                <option value="">Todos los tipos</option>
                                <option value="mantenimiento" {{ request('tipo') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                                <option value="alimentacion" {{ request('tipo') == 'alimentacion' ? 'selected' : '' }}>Alimentación</option>
                                <option value="humedad" {{ request('tipo') == 'humedad' ? 'selected' : '' }}>Humedad</option>
                                <option value="recoleccion" {{ request('tipo') == 'recoleccion' ? 'selected' : '' }}>Recolección</option>
                                <option value="ph" {{ request('tipo') == 'ph' ? 'selected' : '' }}>pH</option>
                                <option value="temperatura" {{ request('tipo') == 'temperatura' ? 'selected' : '' }}>Temperatura</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="filter_estado" class="form-label">Estado</label>
                            <select class="form-select" id="filter_estado" name="estado">
                                <option value="">Todos</option>
                                <option value="activas" {{ request('estado') == 'activas' ? 'selected' : '' }}>Activas</option>
                                <option value="inactivas" {{ request('estado') == 'inactivas' ? 'selected' : '' }}>Inactivas</option>
                                <option value="vencidas" {{ request('estado') == 'vencidas' ? 'selected' : '' }}>Vencidas</option>
                                <option value="proximas" {{ request('estado') == 'proximas' ? 'selected' : '' }}>Próximas</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="filter_cama" class="form-label">Cama</label>
                            <select class="form-select" id="filter_cama" name="cama_id">
                                <option value="">Todas las camas</option>
                                @foreach($camas as $cama)
                                    <option value="{{ $cama->id }}" {{ request('cama_id') == $cama->id ? 'selected' : '' }}>
                                        Cama N° {{ $cama->number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-success me-2">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                            <a href="{{ route('lombrisoft.admin.activity_alerts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                            <a href="{{ route('lombrisoft.admin.activity_alerts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Nueva Alerta
        </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-lg rounded">
            <div class="card-header bg-success text-white text-center">
                <h4>Listado de Alertas de Actividades</h4>
                @if(request()->has('tipo') || request()->has('estado') || request()->has('cama_id'))
                    <p class="mb-0">
                        Filtros aplicados: 
                        @if(request('tipo'))
                            <span class="badge bg-info">Tipo: {{ ucfirst(request('tipo')) }}</span>
                        @endif
                        @if(request('estado'))
                            <span class="badge bg-info">Estado: {{ ucfirst(request('estado')) }}</span>
                        @endif
                        @if(request('cama_id'))
                            @php
                                $selectedCama = $camas->firstWhere('id', request('cama_id'));
                            @endphp
                            <span class="badge bg-info">Cama: {{ $selectedCama ? 'N° ' . $selectedCama->number : 'Desconocida' }}</span>
                        @endif
                    </p>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Cama</th>
                                <th>Tipo</th>
                                <th>Frecuencia</th>
                                <th>Advertencia</th>
                                <th>Última Ejecución</th>
                                <th>Próxima Ejecución</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alerts as $alert)
                            <tr class="{{ 
                                $alert->isOverdue() ? 'table-danger' : 
                                ($alert->isUpcoming() ? 'table-warning' : '') 
                            }}">
                                <td>Cama N° {{ $alert->wormBed->number }}</td>
                                <td>{{ ucfirst($alert->activity_type) }}</td>
                                <td>{{ $alert->frequency_days }} días</td>
                                <td>{{ $alert->warning_days }} días antes</td>
                                <td>
                                    {{ $alert->last_execution ? $alert->last_execution->format('d/m/Y') : 'Nunca' }}
                                </td>
                                <td>
                                    @if($alert->next_expected)
                                        {{ $alert->next_expected->format('d/m/Y') }}
                                        @if($alert->isOverdue())
                                            <span class="badge bg-danger">Vencida</span>
                                        @elseif($alert->isUpcoming())
                                            <span class="badge bg-warning">Próxima</span>
                                        @endif
                                    @else
                                        No definida
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $alert->is_active ? 'success' : 'secondary' }}">
                                        {{ $alert->is_active ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#editModal"
                                        data-id="{{ $alert->id }}"
                                        data-worm_bed_id="{{ $alert->worm_bed_id }}"
                                        data-activity_type="{{ $alert->activity_type }}"
                                        data-frequency_days="{{ $alert->frequency_days }}"
                                        data-warning_days="{{ $alert->warning_days }}"
                                        data-is_active="{{ $alert->is_active }}"
                                        data-last_execution="{{ $alert->last_execution ? $alert->last_execution->format('Y-m-d') : '' }}"
                                        data-next_expected="{{ $alert->next_expected ? $alert->next_expected->format('Y-m-d') : '' }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <form action="{{ route('lombrisoft.admin.activity_alerts.destroy', $alert->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmarEliminacion(this)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($alerts->hasPages())
                <div class="mt-3">
                    {{ $alerts->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- Modal de Edición -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editForm" action="{{ route('lombrisoft.admin.activity_alerts.update', ['id' => '__ID__']) }}">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editModalLabel">Editar Alerta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editWormBed" class="form-label">Cama</label>
                        <select class="form-select" id="editWormBed" name="worm_bed_id" required>
                            @foreach ($camas as $cama)
                            <option value="{{ $cama->id }}">Cama N° {{ $cama->number }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="editActivityType" class="form-label">Tipo de Actividad</label>
                        <select class="form-select" id="editActivityType" name="activity_type" required>
                            <option value="mantenimiento">Mantenimiento</option>
                            <option value="alimentacion">Alimentación</option>
                            <option value="humedad">Humedad</option>
                            <option value="recoleccion">Recolección</option>
                            <option value="ph">pH</option>
                            <option value="temperatura">Temperatura</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editFrequencyDays" class="form-label">Frecuencia (días)</label>
                                <input type="number" class="form-control" id="editFrequencyDays" name="frequency_days" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editWarningDays" class="form-label">Advertencia (días antes)</label>
                                <input type="number" class="form-control" id="editWarningDays" name="warning_days" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="editIsActive" name="is_active" value="1">
                        <label class="form-check-label" for="editIsActive">Alerta activa</label>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editLastExecution" class="form-label">Última ejecución</label>
                                <input type="date" class="form-control" id="editLastExecution" name="last_execution">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editNextExpected" class="form-label">Próxima ejecución</label>
                                <input type="date" class="form-control" id="editNextExpected" name="next_expected">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Definimos la función en el alcance global
    function confirmarEliminacion(element) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esta acción!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                element.closest('form').submit();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Configuración del modal de edición
        const editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const form = document.getElementById('editForm');

            // Actualizar la acción del formulario con el ID correcto
            form.action = form.action.replace('__ID__', button.getAttribute('data-id'));

            // Llenar los campos del formulario
            document.getElementById('editWormBed').value = button.getAttribute('data-worm_bed_id');
            document.getElementById('editActivityType').value = button.getAttribute('data-activity_type');
            document.getElementById('editFrequencyDays').value = button.getAttribute('data-frequency_days');
            document.getElementById('editWarningDays').value = button.getAttribute('data-warning_days');
            document.getElementById('editIsActive').checked = button.getAttribute('data-is_active') === '1';
            document.getElementById('editLastExecution').value = button.getAttribute('data-last_execution');
            document.getElementById('editNextExpected').value = button.getAttribute('data-next_expected');
        });

        // Validar que los días de advertencia no sean mayores que la frecuencia
        document.getElementById('editWarningDays').addEventListener('change', function() {
            const frequency = document.getElementById('editFrequencyDays').value;
            if (this.value > frequency) {
                alert('Los días de advertencia no pueden ser mayores que la frecuencia');
                this.value = frequency;
            }
        });
    });
</script>

@endsection