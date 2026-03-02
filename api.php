<?php
ob_start();
session_start();
// C:\xampp\htdocs\Baramovil\api.php

// Logging para depuración
function api_log($msg) {
    $logFile = 'c:/xampp/htdocs/Baramovil/api_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    @file_put_contents($logFile, "[$timestamp] $msg\n", FILE_APPEND);
}

header('Content-Type: application/json');
require_once 'db.php';

$action = $_GET['action'] ?? '';
api_log("Action: $action | Session: " . json_encode($_SESSION));

if ($action === 'login') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) $data = $_POST;
    
    $user = $data['username'] ?? '';
    $pass = $data['password'] ?? '';

    // Validación: OPE_NOMBRE, OPE_PASSWORD, OPE_WEB
    $stmt = $pdo->prepare("SELECT OPE_VENDEDOR, OPE_NOMBRE, OPE_WEB FROM SISTEMASADN.ADN_USUARIOS WHERE OPE_NOMBRE = ? AND OPE_PASSWORD = ? LIMIT 1");
    $stmt->execute([$user, $pass]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $_SESSION['user_id']     = $row['OPE_NOMBRE'];
        $_SESSION['user_name']   = $row['OPE_NOMBRE'];
        $_SESSION['vendedor_id'] = $row['OPE_VENDEDOR'];
        $_SESSION['ope_web']     = (int)$row['OPE_WEB'];   // 1 = puede hacer pedidos/clientes
        ob_clean();
        echo json_encode(['success' => true, 'ope_web' => (int)$row['OPE_WEB']]);
    } else {
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'Credenciales inválidas']);
    }
    exit;

} elseif ($action === 'logout') {
    session_destroy();
    ob_clean();
    echo json_encode(['success' => true]);
    exit;

} elseif ($action === 'get_config') {
    // Retorna la configuración global.
    $stmt = $pdo->query("SELECT CODIGO, VALOR FROM ADN_CONFIG WHERE CODIGO IN ('WEB_VER_USD', 'WEB_VER_COP', 'WEB_IMG_PROD', 'WEB_LOGO', 'WEB_COLOR', 'WEB_HAB_COMEN')");
    $configs = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Valores por defecto si no existen
    $default = [
        'WEB_VER_USD' => '1',
        'WEB_VER_COP' => '1',
        'WEB_IMG_PROD' => '1',
        'WEB_LOGO' => '1',
        'WEB_COLOR' => '#EA580C',
        'WEB_HAB_COMEN' => '0'
    ];
    
    ob_clean();
    echo json_encode(['success' => true, 'config' => array_merge($default, $configs)]);
    exit;

} elseif ($action === 'save_config') {
    if (($_SESSION['ope_web'] ?? 0) !== 1) {
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'Permiso denegado']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) $data = $_POST;
    
    $allowedKeys = ['WEB_VER_USD', 'WEB_VER_COP', 'WEB_IMG_PROD', 'WEB_LOGO', 'WEB_COLOR', 'WEB_HAB_COMEN'];
    
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO ADN_CONFIG (CODIGO, VALOR, UPD) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE VALOR = VALUES(VALOR), UPD = NOW()");
        
        foreach ($allowedKeys as $key) {
            if (isset($data[$key])) {
                $stmt->execute([$key, $data[$key]]);
            }
        }
        $pdo->commit();
        ob_clean();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        ob_clean();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;

} elseif ($action === 'get_orders') {
    // --- ACCIONES RESTRINGIDAS (requieren OPE_WEB = 1) ---
    $restricted = ['get_orders', 'submit_order', 'create_client', 'search_clients', 'get_order_detail'];
    if (in_array($action, $restricted) && ($_SESSION['ope_web'] ?? 0) !== 1) {
        ob_clean();
        echo json_encode(['error' => 'Sin permiso', 'no_permission' => true]);
        exit;
    }

} elseif ($action === 'get_orders') {
    if (!isset($_SESSION['vendedor_id'])) {
        ob_clean();
        echo json_encode(['error' => 'Sesión no activa (ID vendedor no encontrado)']);
        exit;
    }

    try {
        $vendedor = $_SESSION['vendedor_id'];
        api_log("Fetching orders for vendedor: $vendedor");
        
        $sql = "SELECT DCL_NUMERO, DCL_TDT_CODIGO, DCL_CLT_CODIGO, CLT_NOMBRE, DCL_NETO, DCL_BASEG, DCL_EXENTO, DCL_IVAG, DCL_FECHA, DCL_HORA 
                FROM ADN_DOCCLI 
                INNER JOIN ADN_CLIENTES ON CLT_CODIGO = DCL_CLT_CODIGO 
                WHERE DCL_TIPTRA = 'D' AND DCL_TDT_CODIGO = 'PED' AND DCL_FECHA = CURDATE() AND DCL_VEN_CODIGO = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$vendedor]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        api_log("Found " . count($orders) . " orders");
        
        ob_clean();
        echo json_encode($orders);
    } catch (Exception $e) {
        api_log("Error in get_orders: " . $e->getMessage());
        ob_clean();
        echo json_encode(['error' => 'Error al obtener pedidos: ' . $e->getMessage()]);
    }
    exit;
}

try {
    if ($action === 'products') {
        $department = $_GET['department'] ?? '';
        $category = $_GET['category'] ?? '';
        $search = $_GET['search'] ?? '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

        $sql = "SELECT 
				PDT_CODIGO AS codigo, 
				PDT_DESCRIPCION AS descripcion, 
				PDT_DEP_CODIGO, 
				PDT_CAT_CODIGO,  
				ROUND(PRE_PRECIO * PERSONALIZAR_GET_TASA('USD', CURDATE()),2) AS bs,
				ROUND(PRE_PRECIO * PERSONALIZAR_GET_TASA('COP', CURDATE()),2) AS cop, 
				PRE_PRECIO AS usd,
                CONCAT('img/', PDT_CODIGO, '.jpg') as imageUrl,
                0 AS iva_rate, 
				UGR_EX1 AS exis
                FROM ADN_PRODUCTOS
				INNER JOIN ADN_PRECIOS ON PRE_UGR_PDT_CODIGO = PDT_CODIGO AND PRE_PLT_LISTA = 'A'
				INNER JOIN ADN_UNDAGRU ON UGR_PDT_CODIGO = PRE_UGR_PDT_CODIGO 
				AND UGR_UND_ID = PRE_UGR_UND_ID
				WHERE 1=1";
        
        $params = [];
        if ($department) {
            $sql .= " AND PDT_DEP_CODIGO = ?";
            $params[] = $department;
        }
        if ($category) {
            $sql .= " AND PDT_CAT_CODIGO = ?";
            $params[] = $category;
        }
        if ($search) {
            $sql .= " AND (PDT_CODIGO LIKE ? OR PDT_DESCRIPCION LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode($stmt->fetchAll());

    } elseif ($action === 'departments') {
        $stmt = $pdo->query("SELECT DEP_CODIGO, DEP_DESCRIPCION FROM ADN_DEPARTAMENTOS");
        echo json_encode($stmt->fetchAll());

    } elseif ($action === 'categories') {
        $stmt = $pdo->query("SELECT CAT_CODIGO, CAT_DESCRIPCION FROM ADN_CATEGORIAS");
        echo json_encode($stmt->fetchAll());

    } elseif ($action === 'get_order_detail') {
        $numero = $_GET['numero'] ?? '';
        if (!$numero) {
            echo json_encode(['error' => 'Número de pedido requerido']);
        } else {
            $stmt = $pdo->prepare("SELECT 
                MCL_UPP_PDT_CODIGO AS CODIGO,
                MCL_DESCRI        AS DESCRIPCION,
                MCL_BASE          AS BASE,
                MCL_CANTIDAD      AS CANTIDAD,
                MCL_TIVACOD       AS IVA
            FROM ADN_MOVCLI
            WHERE MCL_DCL_NUMERO = ?
              AND MCL_DCL_TDT_CODIGO = 'PED'");
            $stmt->execute([$numero]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }

    } elseif ($action === 'get_rates') {
        $stmt = $pdo->query("SELECT 
            PERSONALIZAR_GET_TASA('USD', CURDATE()) AS bcv,
            PERSONALIZAR_GET_TASA('COP', CURDATE()) AS cop");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        ob_clean();
        echo json_encode($row);

    } elseif ($action === 'search_clients') {
        $search = $_GET['search'] ?? '';
        $stmt = $pdo->prepare("SELECT CLT_CODIGO, CLT_NOMBRE, CLT_RIF FROM ADN_CLIENTES WHERE CLT_NOMBRE LIKE ?");
        $stmt->execute(["%$search%"]);
        echo json_encode($stmt->fetchAll());

    } elseif ($action === 'get_next_client_code') {
        // Obtenemos el siguiente código formateado a 10 dígitos
        $stmt = $pdo->query("SELECT LPAD(MAX(CLT_CODIGO) + 1, 10, '0') AS next_code FROM ADN_CLIENTES WHERE CLT_CODIGO NOT RLIKE '^[a-zA-Z]'");
        $res = $stmt->fetch();
        echo json_encode(['next_code' => $res['next_code']]);

    } elseif ($action === 'create_client') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) $data = $_POST;

        $sql = "INSERT INTO ADN_CLIENTES (CLT_CODIGO, CLT_NOMBRE, CLT_RIF, CLT_DIRECCION1, CLT_TELEFONO1) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['codigo'],
            trim($data['nombre'] . ' ' . $data['apellido']),
            $data['rif'],
            $data['direccion'] ?? '',
            $data['telefono'] ?? ''
        ]);
        echo json_encode(['success' => true]);

    } elseif ($action === 'submit_order') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) $data = $_POST;
        
        if (!isset($_SESSION['vendedor_id'])) {
            ob_clean();
            api_log("submit_order FAILED: No session");
            echo json_encode(['success' => false, 'error' => 'Sesión expirada o no activa. Por favor inicie sesión de nuevo.']);
            exit;
        }

        $cCliente = $data['cCliente'] ?? '';
        $pseudoId = $data['pseudoId'] ?? '';
        $jData = json_encode($data['jData'] ?? []);
        $cVendedor = $_SESSION['vendedor_id'];
        $cComentario = $data['comentario'] ?? '';

        api_log("Submitting order: Client=$cCliente, PseudoId=$pseudoId, Vendedor=$cVendedor, Comentario=$cComentario");

        try {
            // 1. Ejecutar el procedimiento almacenado
            $stmt = $pdo->prepare("CALL WEB_PEDIDO(?, ?, ?, ?, ?)");
            $stmt->execute([$cCliente, $jData, $pseudoId, $cVendedor, $cComentario]);
            
            api_log("WEB_PEDIDO executed. Verifying PseudoId: $pseudoId");

            // 2. Verificación Manual
            $checkStmt = $pdo->prepare("SELECT DCL_NUMERO FROM ADN_DOCCLI WHERE DCL_CVN_DESCRIPCION = ?");
            $checkStmt->execute([$pseudoId]);
            $verify = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($verify) {
                api_log("Order verified: " . $verify['DCL_NUMERO']);
                ob_clean();
                echo json_encode([
                    'success' => true, 
                    'pedido' => $verify['DCL_NUMERO']
                ]);
            } else {
                api_log("Order verification FAILED for PseudoId: $pseudoId");
                ob_clean();
                echo json_encode([
                    'success' => false, 
                    'error' => 'El pedido se envió pero no se pudo verificar en la base de datos.'
                ]);
            }
        } catch (Exception $e) {
            api_log("submit_order EXCEPTION: " . $e->getMessage());
            ob_clean();
            echo json_encode([
                'success' => false, 
                'error' => 'Excepción al procesar pedido: ' . $e->getMessage()
            ]);
        }

    } else {
        echo json_encode(['error' => 'Acción no válida']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
