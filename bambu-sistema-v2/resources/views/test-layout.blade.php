<!DOCTYPE html>
<html lang="es" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAMBU CRM - Test Layout Canónico</title>
    
    <!-- CSS compilado -->
    @vite(['resources/css/app.css'])
</head>
<body>
    <!-- App mount point -->
    <div id="app"></div>
    
    <!-- Vue app de prueba -->
    @vite(['resources/js/app-test.js'])
    
    <!-- Info de prueba -->
    <script>
        console.log('🎯 Test del Layout Canónico BAMBU');
        console.log('📋 Checklist de validación:');
        console.log('1. Sidebar es overlay en mobile (<1024px)');
        console.log('2. Sidebar es columna real en desktop (≥1024px)');
        console.log('3. No hay margin-left en el main');
        console.log('4. Header usa Grid auto 1fr auto');
        console.log('5. Botones son 40×40px con SVGs 16×16px');
        console.log('6. Escape cierra el sidebar');
        console.log('7. Body overflow se bloquea cuando sidebar abierto');
        console.log('✅ Verificar cada punto manualmente');
    </script>
</body>
</html>