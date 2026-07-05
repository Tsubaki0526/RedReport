<?php
include('../sesion.php');
require_once '../app/config/conexion.php';
include('../parte1.php');

$baseUrl = APP_URL . 'api/index.php';
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-code me-2 text-primary"></i>Documentacion API REST</h1>
            </div>
            <div class="col-sm-6 text-end text-muted">
                <span id="fechaHora"></span>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-3 col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list me-2"></i>Endpoints</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush rounded-0" id="endpointNav">
                            <a href="#auth" class="list-group-item list-group-item-action"><i class="fas fa-lock me-2 text-warning"></i>Autenticacion</a>
                            <a href="#clientes" class="list-group-item list-group-item-action"><i class="fas fa-users me-2 text-primary"></i>Clientes</a>
                            <a href="#facturas" class="list-group-item list-group-item-action"><i class="fas fa-file-invoice me-2 text-success"></i>Facturas</a>
                            <a href="#contratos" class="list-group-item list-group-item-action"><i class="fas fa-file-contract me-2 text-info"></i>Contratos</a>
                            <a href="#planes" class="list-group-item list-group-item-action"><i class="fas fa-tags me-2 text-purple"></i>Planes</a>
                            <a href="#dashboard" class="list-group-item list-group-item-action"><i class="fas fa-chart-pie me-2 text-danger"></i>Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-8">

                <!-- Auth -->
                <div class="card mb-4" id="auth">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-lock me-2 text-warning"></i>Autenticacion</h3>
                    </div>
                    <div class="card-body">
                        <p>Todos los endpoints requieren una <strong>API Key</strong> valida. Se puede enviar de dos formas:</p>
                        <div class="mb-3">
                            <h6 class="fw-bold">Header HTTP</h6>
                            <pre class="bg-light p-3 rounded border"><code>X-API-Key: TU_API_KEY</code></pre>
                        </div>
                        <div class="mb-3">
                            <h6 class="fw-bold">Query string</h6>
                            <pre class="bg-light p-3 rounded border"><code><?= hescape($baseUrl) ?>?endpoint=clientes&amp;api_key=TU_API_KEY</code></pre>
                        </div>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            La API Key se configura en el archivo <code>.env</code> con la variable <code>API_KEY</code>.
                            Valor por defecto: <code>RedReport2024API</code>
                        </div>
                    </div>
                </div>

                <!-- Base URL -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-globe me-2 text-secondary"></i>URL Base</h3>
                    </div>
                    <div class="card-body">
                        <pre class="bg-light p-3 rounded border mb-0"><code><?= hescape($baseUrl) ?></code></pre>
                        <small class="text-muted mt-2 d-block">Metodos soportados: <span class="badge bg-success">GET</span> <span class="badge bg-primary">POST</span> <span class="badge bg-warning text-dark">PUT</span> <span class="badge bg-danger">DELETE</span></small>
                    </div>
                </div>

                <!-- Endpoint: Clientes -->
                <div class="card mb-4" id="clientes">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class="fas fa-users me-2 text-primary"></i>Clientes</h3>
                        <span class="badge bg-success fs-6">GET</span>
                    </div>
                    <div class="card-body">
                        <p>Listar todos los clientes.</p>
                        <pre class="bg-light p-3 rounded border"><code><?= hescape($baseUrl) ?>?endpoint=clientes&api_key=TU_API_KEY</code></pre>

                        <hr>
                        <h5 class="fw-bold mb-3">Respuesta</h5>
                        <pre class="bg-dark text-light p-3 rounded border overflow-auto" style="max-height:300px;"><code>{
  "data": [
    {
      "id_cliente": 1,
      "nombre": "Juan Perez",
      "documento": "123456789",
      "telefono": "3001234567",
      "direccion": "Calle 123",
      "email": "juan@example.com",
      "estado_servicio": "Activo",
      "lat": "4.7110",
      "lng": "-74.0721",
      "instalador_nombre": "Carlos Lopez"
    }
  ]
}</code></pre>

                        <hr>
                        <h5 class="fw-bold mb-3">Parametros</h5>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr><th>Parametro</th><th>Tipo</th><th>Requerido</th><th>Descripcion</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>endpoint</td><td>string</td><td class="text-danger">Si</td><td><code>clientes</code></td></tr>
                                <tr><td>api_key</td><td>string</td><td class="text-danger">Si</td><td>API Key del sistema</td></tr>
                                <tr><td>id</td><td>int</td><td class="text-success">No</td><td>ID del cliente para obtener uno especifico</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class="fas fa-user me-2 text-primary"></i>Obtener Cliente</h3>
                        <span class="badge bg-success fs-6">GET</span>
                    </div>
                    <div class="card-body">
                        <p>Obtener un cliente por su ID.</p>
                        <pre class="bg-light p-3 rounded border"><code><?= hescape($baseUrl) ?>?endpoint=clientes&id=1&api_key=TU_API_KEY</code></pre>

                        <hr>
                        <h5 class="fw-bold mb-3">Respuesta</h5>
                        <pre class="bg-dark text-light p-3 rounded border overflow-auto" style="max-height:300px;"><code>{
  "data": {
    "id_cliente": 1,
    "nombre": "Juan Perez",
    "documento": "123456789",
    "telefono": "3001234567",
    "direccion": "Calle 123",
    "email": "juan@example.com",
    "estado_servicio": "Activo",
    "lat": "4.7110",
    "lng": "-74.0721",
    "instalador_nombre": "Carlos Lopez"
  }
}</code></pre>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class="fas fa-plus-circle me-2 text-primary"></i>Crear Cliente</h3>
                        <span class="badge bg-primary fs-6">POST</span>
                    </div>
                    <div class="card-body">
                        <p>Cuerpo de la solicitud en JSON.</p>
                        <pre class="bg-light p-3 rounded border"><code><?= hescape($baseUrl) ?>?endpoint=clientes&api_key=TU_API_KEY</code></pre>

                        <hr>
                        <h5 class="fw-bold mb-3">Parametros (JSON body)</h5>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr><th>Parametro</th><th>Tipo</th><th>Requerido</th><th>Descripcion</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>nombre</td><td>string</td><td class="text-danger">Si</td><td>Nombre del cliente</td></tr>
                                <tr><td>documento</td><td>string</td><td class="text-danger">Si</td><td>Documento de identidad</td></tr>
                                <tr><td>telefono</td><td>string</td><td class="text-success">No</td><td>Telefono de contacto</td></tr>
                                <tr><td>direccion</td><td>string</td><td class="text-success">No</td><td>Direccion</td></tr>
                                <tr><td>email</td><td>string</td><td class="text-success">No</td><td>Correo electronico</td></tr>
                                <tr><td>estado_servicio</td><td>string</td><td class="text-success">No</td><td>Activo (defecto), Suspendido, Cortado</td></tr>
                                <tr><td>lat</td><td>float</td><td class="text-success">No</td><td>Latitud</td></tr>
                                <tr><td>lng</td><td>float</td><td class="text-success">No</td><td>Longitud</td></tr>
                            </tbody>
                        </table>

                        <h5 class="fw-bold mb-3">Ejemplo body</h5>
                        <pre class="bg-dark text-light p-3 rounded border overflow-auto" style="max-height:200px;"><code>{
  "nombre": "Maria Gomez",
  "documento": "987654321",
  "telefono": "3109876543",
  "direccion": "Carrera 45 #67-89",
  "email": "maria@example.com",
  "estado_servicio": "Activo"
}</code></pre>

                        <h5 class="fw-bold mb-3">Respuesta</h5>
                        <pre class="bg-dark text-light p-3 rounded border overflow-auto" style="max-height:200px;"><code>{
  "success": true,
  "id": 2
}</code></pre>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class="fas fa-edit me-2 text-primary"></i>Actualizar Cliente</h3>
                        <span class="badge bg-warning text-dark fs-6">PUT</span>
                    </div>
                    <div class="card-body">
                        <p>Actualizar datos de un cliente. Solo se envian los campos a modificar.</p>
                        <pre class="bg-light p-3 rounded border"><code><?= hescape($baseUrl) ?>?endpoint=clientes&id=1&api_key=TU_API_KEY</code></pre>

                        <hr>
                        <h5 class="fw-bold mb-3">Parametros (JSON body)</h5>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr><th>Parametro</th><th>Tipo</th><th>Requerido</th><th>Descripcion</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>nombre</td><td>string</td><td class="text-success">No</td><td>Nombre del cliente</td></tr>
                                <tr><td>documento</td><td>string</td><td class="text-success">No</td><td>Documento de identidad</td></tr>
                                <tr><td>telefono</td><td>string</td><td class="text-success">No</td><td>Telefono</td></tr>
                                <tr><td>direccion</td><td>string</td><td class="text-success">No</td><td>Direccion</td></tr>
                                <tr><td>email</td><td>string</td><td class="text-success">No</td><td>Correo electronico</td></tr>
                                <tr><td>estado_servicio</td><td>string</td><td class="text-success">No</td><td>Activo, Suspendido, Cortado</td></tr>
                                <tr><td>lat</td><td>float</td><td class="text-success">No</td><td>Latitud</td></tr>
                                <tr><td>lng</td><td>float</td><td class="text-success">No</td><td>Longitud</td></tr>
                            </tbody>
                        </table>

                        <h5 class="fw-bold mb-3">Respuesta</h5>
                        <pre class="bg-dark text-light p-3 rounded border overflow-auto" style="max-height:200px;"><code>{
  "success": true
}</code></pre>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class="fas fa-trash me-2 text-primary"></i>Eliminar Cliente</h3>
                        <span class="badge bg-danger fs-6">DELETE</span>
                    </div>
                    <div class="card-body">
                        <p>Eliminar un cliente por su ID.</p>
                        <pre class="bg-light p-3 rounded border"><code><?= hescape($baseUrl) ?>?endpoint=clientes&id=1&api_key=TU_API_KEY</code></pre>

                        <hr>
                        <h5 class="fw-bold mb-3">Respuesta</h5>
                        <pre class="bg-dark text-light p-3 rounded border overflow-auto" style="max-height:200px;"><code>{
  "success": true
}</code></pre>
                    </div>
                </div>

                <!-- Endpoint: Facturas -->
                <div class="card mb-4" id="facturas">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class="fas fa-file-invoice me-2 text-success"></i>Listar Facturas</h3>
                        <span class="badge bg-success fs-6">GET</span>
                    </div>
                    <div class="card-body">
                        <p>Listar facturas. Opcionalmente filtrar por estado.</p>
                        <pre class="bg-light p-3 rounded border"><code><?= hescape($baseUrl) ?>?endpoint=facturas&api_key=TU_API_KEY
<?= hescape($baseUrl) ?>?endpoint=facturas&estado=pendiente&api_key=TU_API_KEY</code></pre>

                        <hr>
                        <h5 class="fw-bold mb-3">Parametros</h5>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr><th>Parametro</th><th>Tipo</th><th>Requerido</th><th>Descripcion</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>endpoint</td><td>string</td><td class="text-danger">Si</td><td><code>facturas</code></td></tr>
                                <tr><td>api_key</td><td>string</td><td class="text-danger">Si</td><td>API Key</td></tr>
                                <tr><td>estado</td><td>string</td><td class="text-success">No</td><td>Filtrar por: pendiente, pagada, vencida, anulada</td></tr>
                                <tr><td>id</td><td>int</td><td class="text-success">No</td><td>ID de factura para obtener una especifica con items</td></tr>
                            </tbody>
                        </table>

                        <hr>
                        <h5 class="fw-bold mb-3">Respuesta (lista)</h5>
                        <pre class="bg-dark text-light p-3 rounded border overflow-auto" style="max-height:300px;"><code>{
  "data": [
    {
      "id_factura": 1,
      "numero_factura": "FAC-00001",
      "id_cliente": 1,
      "subtotal": 100000,
      "iva": 19000,
      "total": 119000,
      "estado": "pendiente",
      "fecha_emision": "2026-01-15",
      "fecha_vencimiento": "2026-02-14",
      "cliente_nombre": "Juan Perez"
    }
  ]
}</code></pre>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class="fas fa-file-invoice me-2 text-success"></i>Obtener Factura</h3>
                        <span class="badge bg-success fs-6">GET</span>
                    </div>
                    <div class="card-body">
                        <p>Obtener una factura con sus items y datos del cliente.</p>
                        <pre class="bg-light p-3 rounded border"><code><?= hescape($baseUrl) ?>?endpoint=facturas&id=1&api_key=TU_API_KEY</code></pre>

                        <hr>
                        <h5 class="fw-bold mb-3">Respuesta</h5>
                        <pre class="bg-dark text-light p-3 rounded border overflow-auto" style="max-height:400px;"><code>{
  "data": {
    "id_factura": 1,
    "numero_factura": "FAC-00001",
    "id_cliente": 1,
    "subtotal": 100000,
    "iva": 19000,
    "total": 119000,
    "estado": "pendiente",
    "fecha_emision": "2026-01-15",
    "fecha_vencimiento": "2026-02-14",
    "cliente_nombre": "Juan Perez",
    "documento": "123456789",
    "items": [
      {
        "id_item": 1,
        "id_factura": 1,
        "descripcion": "Internet 50MB",
        "cantidad": 1,
        "precio_unitario": 100000,
        "subtotal": 100000
      }
    ]
  }
}</code></pre>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class="fas fa-plus-circle me-2 text-success"></i>Crear Factura</h3>
                        <span class="badge bg-primary fs-6">POST</span>
                    </div>
                    <div class="card-body">
                        <p>Crea una factura con items. El numero se genera automaticamente (FAC-XXXXX).</p>
                        <pre class="bg-light p-3 rounded border"><code><?= hescape($baseUrl) ?>?endpoint=facturas&api_key=TU_API_KEY</code></pre>

                        <hr>
                        <h5 class="fw-bold mb-3">Parametros (JSON body)</h5>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr><th>Parametro</th><th>Tipo</th><th>Requerido</th><th>Descripcion</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>id_cliente</td><td>int</td><td class="text-danger">Si</td><td>ID del cliente</td></tr>
                                <tr><td>items</td><td>array</td><td class="text-danger">Si</td><td>Array de objetos con descripcion, cantidad, precio_unitario</td></tr>
                            </tbody>
                        </table>

                        <h5 class="fw-bold mb-3">Ejemplo body</h5>
                        <pre class="bg-dark text-light p-3 rounded border overflow-auto" style="max-height:250px;"><code>{
  "id_cliente": 1,
  "items": [
    {
      "descripcion": "Internet 100MB",
      "cantidad": 1,
      "precio_unitario": 120000
    },
    {
      "descripcion": "Instalacion",
      "cantidad": 1,
      "precio_unitario": 50000
    }
  ]
}</code></pre>

                        <h5 class="fw-bold mb-3">Respuesta</h5>
                        <pre class="bg-dark text-light p-3 rounded border overflow-auto" style="max-height:200px;"><code>{
  "success": true,
  "id": 2,
  "numero": "FAC-00002"
}</code></pre>
                    </div>
                </div>

                <!-- Endpoint: Contratos -->
                <div class="card mb-4" id="contratos">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class="fas fa-file-contract me-2 text-info"></i>Listar Contratos</h3>
                        <span class="badge bg-success fs-6">GET</span>
                    </div>
                    <div class="card-body">
                        <p>Listar contratos. Opcionalmente filtrar por cliente.</p>
                        <pre class="bg-light p-3 rounded border"><code><?= hescape($baseUrl) ?>?endpoint=contratos&api_key=TU_API_KEY
<?= hescape($baseUrl) ?>?endpoint=contratos&id_cliente=1&api_key=TU_API_KEY</code></pre>

                        <hr>
                        <h5 class="fw-bold mb-3">Parametros</h5>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr><th>Parametro</th><th>Tipo</th><th>Requerido</th><th>Descripcion</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>endpoint</td><td>string</td><td class="text-danger">Si</td><td><code>contratos</code></td></tr>
                                <tr><td>api_key</td><td>string</td><td class="text-danger">Si</td><td>API Key</td></tr>
                                <tr><td>id_cliente</td><td>int</td><td class="text-success">No</td><td>Filtrar por cliente</td></tr>
                                <tr><td>id</td><td>int</td><td class="text-success">No</td><td>ID del contrato especifico</td></tr>
                            </tbody>
                        </table>

                        <hr>
                        <h5 class="fw-bold mb-3">Respuesta</h5>
                        <pre class="bg-dark text-light p-3 rounded border overflow-auto" style="max-height:300px;"><code>{
  "data": [
    {
      "id_contrato": 1,
      "id_cliente": 1,
      "id_plan": 1,
      "id_vendedor": 1,
      "fecha_inicio": "2026-01-01",
      "fecha_fin": "2027-01-01",
      "estado": "activo",
      "cliente_nombre": "Juan Perez",
      "plan_nombre": "Plan 100MB",
      "precio": "120000.00",
      "vendedor_nombre": "Admin"
    }
  ]
}</code></pre>
                    </div>
                </div>

                <!-- Endpoint: Planes -->
                <div class="card mb-4" id="planes">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class="fas fa-tags me-2 text-purple"></i>Listar Planes</h3>
                        <span class="badge bg-success fs-6">GET</span>
                    </div>
                    <div class="card-body">
                        <p>Obtener todos los planes activos.</p>
                        <pre class="bg-light p-3 rounded border"><code><?= hescape($baseUrl) ?>?endpoint=planes&api_key=TU_API_KEY</code></pre>

                        <hr>
                        <h5 class="fw-bold mb-3">Respuesta</h5>
                        <pre class="bg-dark text-light p-3 rounded border overflow-auto" style="max-height:300px;"><code>{
  "data": [
    {
      "id_plan": 1,
      "nombre": "Plan 100MB",
      "velocidad": "100 Mbps",
      "precio": "120000.00",
      "descripcion": "Internet 100MB fibra optica"
    }
  ]
}</code></pre>
                    </div>
                </div>

                <!-- Endpoint: Dashboard -->
                <div class="card mb-4" id="dashboard">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class="fas fa-chart-pie me-2 text-danger"></i>Dashboard</h3>
                        <span class="badge bg-success fs-6">GET</span>
                    </div>
                    <div class="card-body">
                        <p>Resumen de estadisticas del sistema.</p>
                        <pre class="bg-light p-3 rounded border"><code><?= hescape($baseUrl) ?>?endpoint=dashboard&api_key=TU_API_KEY</code></pre>

                        <hr>
                        <h5 class="fw-bold mb-3">Respuesta</h5>
                        <pre class="bg-dark text-light p-3 rounded border overflow-auto" style="max-height:300px;"><code>{
  "data": {
    "total": 150,
    "activos": 120,
    "contratos": 98,
    "deuda": 5250000
  }
}</code></pre>

                        <table class="table table-bordered mt-3 mb-0">
                            <thead class="table-light">
                                <tr><th>Campo</th><th>Descripcion</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>total</td><td>Total de clientes registrados</td></tr>
                                <tr><td>activos</td><td>Clientes con estado de servicio Activo</td></tr>
                                <tr><td>contratos</td><td>Contratos activos vigentes</td></tr>
                                <tr><td>deuda</td><td>Suma total de facturas pendientes o vencidas</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
<?php include('../parte2.php'); ?>
