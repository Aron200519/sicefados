<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Modal - FABRICASOFT</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Test Modal - FABRICASOFT</h1>
        
        <button type="button" class="btn btn-info" onclick="asignarAnalista()">
            <i class="fas fa-user-plus me-2"></i>Asignar Analista (Test)
        </button>
        
        <hr>
        
        <h3>Analistas disponibles:</h3>
        <ul>
            @foreach($analysts as $analyst)
                <li>{{ $analyst->nickname }} ({{ $analyst->email }}) - ID: {{ $analyst->id }}</li>
            @endforeach
        </ul>
    </div>

    <!-- Modal para Asignar Analista -->
    <div class="modal fade" id="modalAsignarAnalista" tabindex="-1" aria-labelledby="modalAsignarAnalistaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAsignarAnalistaLabel">Test: Asignar Analista</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Test:</strong> Este es un modal de prueba para verificar que funcione correctamente.
                    </div>
                    <div class="mb-3">
                        <label for="analyst_id" class="form-label">Analista *</label>
                        <select class="form-select" id="analyst_id" name="analyst_id" required>
                            <option value="">Selecciona un analista</option>
                            @foreach($analysts as $analyst)
                                <option value="{{ $analyst->id }}">{{ $analyst->nickname }} ({{ $analyst->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-info" onclick="alert('Modal funcionando correctamente!')">
                        <i class="fas fa-user-plus me-2"></i>Test Modal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function asignarAnalista() {
        console.log('Función asignarAnalista() ejecutada');
        const modal = document.getElementById('modalAsignarAnalista');
        console.log('Modal encontrado:', modal);
        
        if (modal) {
            const bootstrapModal = new bootstrap.Modal(modal);
            console.log('Bootstrap Modal creado:', bootstrapModal);
            bootstrapModal.show();
        } else {
            console.error('Modal no encontrado');
            alert('Error: Modal no encontrado');
        }
    }
    </script>
</body>
</html>
