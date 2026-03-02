<?php
// C:\xampp\htdocs\Baramovil\test_db.php
header('Content-Type: application/json');
require_once 'db.php';

try {
    $results = [];
    
    // Prueba de tablas
    $tables = ['ADN_PRODUCTOS', 'ADN_DEPARTAMENTOS', 'ADN_CATEGORIAS', 'ADN_PRECIOS', 'ADN_CLIENTES'];
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch()['count'];
            $results[$table] = "OK ($count registros)";
        } catch (Exception $e) {
            $results[$table] = "ERROR: " . $e->getMessage();
        }
    }
    
    echo json_encode([
        'status' => 'Conexión exitosa',
        'database' => 'ADN_SEBAS',
        'tables' => $results
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
