<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pago exitoso — XISTI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css"/>
    <style>
        body { padding: 24px; background: #f5f5f5; }
        .panel { max-width: 480px; margin: 40px auto; }
    </style>
</head>
<body>
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading"><h1 class="panel-title">Pago exitoso</h1></div>
        <div class="panel-body">
            <div class="alert alert-success">
                <strong>Tu pago fue aprobado.</strong> Puedes cerrar esta ventana y volver a la app.
            </div>
            <button type="button" class="btn btn-primary btn-block" onclick="closeOrBack()">Volver al comercio</button>
        </div>
    </div>
</div>
<script>
function closeOrBack() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.close();
    }
}
</script>
</body>
</html>
