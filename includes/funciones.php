<?php

// Incluimos sistema de mensajes flash reutilizable
require_once __DIR__ . '/flash.php';



/**
 * Obtiene listado de productos activos aplicando:
 * - Filtros por categoría y búsqueda
 * - Ordenamiento controlado
 * - Paginación
 * Utiliza consultas preparadas para prevenir SQL Injection.
 */
function obtenerProductos(PDO $pdo, array $filtros): array
{

    // Construcción dinámica segura de filtros
    // Se utilizan parámetros bind para evitar inyección SQL
    $sql = "SELECT * FROM productos WHERE activo = 1";
    $params = [];

    if (!empty($filtros['categoria'])) {
        $sql .= " AND categoria = :categoria";
        $params[':categoria'] = $filtros['categoria'];
    }

    if (!empty($filtros['busqueda'])) {
        $sql .= " AND nombre LIKE :busqueda";
        $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
    }

    $orderBy = obtenerOrderBy($filtros['orden']);
    $sql .= " ORDER BY $orderBy";

    // El ORDER BY no se parametriza directamente en PDO,
    // por eso se controla mediante una whitelist (obtenerOrderBy)

    $limit  = (int) $filtros['porPagina'];
    $offset = ($filtros['page'] - 1) * $limit;

    $sql .= " LIMIT $limit OFFSET $offset";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}




/**
 * Obtiene un producto activo por ID.
 * Se utiliza bindParam tipado para asegurar integridad.
 */
function obtenerProductoPorId(PDO $pdo, int $id) {
    $sql = "SELECT * FROM productos WHERE id = :id AND activo = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


/**
 * Agrupa productos por categoría.
 * Facilita renderizado estructurado en la vista.
 */
function agruparPorCategoria(array $productos) {
    $resultado = [];

    foreach ($productos as $producto) {
        $categoria = $producto['categoria'];
        $resultado[$categoria][] = $producto;
    }

    return $resultado;
}

/**
 * Resalta coincidencias de búsqueda dentro de un texto.
 * Escapa primero el contenido para prevenir XSS
 * antes de aplicar el resaltado.
 */
function resaltarTexto($texto, $busqueda) {
    if (!$busqueda) {
        return htmlspecialchars($texto);
    }

    $textoSeguro = htmlspecialchars($texto);

    return preg_replace(
        '/' . preg_quote($busqueda, '/') . '/i',
        '<mark>$0</mark>',
        $textoSeguro
    );
}

function obtenerGet($clave, $default = null) {
    return isset($_GET[$clave]) && $_GET[$clave] !== ''
        ? trim($_GET[$clave])
        : $default;
}

function obtenerPost(string $clave, $default = null)
{
    if (!isset($_POST[$clave])) {
        return $default;
    }

    if (is_string($_POST[$clave])) {
        return trim($_POST[$clave]);
    }

    return $_POST[$clave];
}



function mostrarError($mensaje) {
    setFlash('error', $mensaje);
    header('Location: index.php');
    exit;
}


/**
 * Escapa texto para salida HTML.
 * Previene ataques XSS.
 */
function e($texto) {
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}




/**
 * Devuelve el total de productos activos
 * según los filtros aplicados.
 * Se utiliza para paginación.
 */
function contarProductos(PDO $pdo, array $filtros): int
{
    $sql = "SELECT COUNT(*) FROM productos WHERE activo = 1";
    $params = [];

    if (!empty($filtros['categoria'])) {
        $sql .= " AND categoria = :categoria";
        $params[':categoria'] = $filtros['categoria'];
    }

    if (!empty($filtros['busqueda'])) {
        $sql .= " AND nombre LIKE :busqueda";
        $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return (int) $stmt->fetchColumn();
}


/**
 * Devuelve cláusula ORDER BY validada.
 * Se utiliza una whitelist para evitar inyección
 * en campos dinámicos de ordenamiento.
 */
function obtenerOrderBy(string $orden): string
{
    $permitidos = [
        'precio_asc'  => 'precio ASC',
        'precio_desc' => 'precio DESC',
        'nombre'      => 'nombre ASC'
    ];

    return $permitidos[$orden] ?? 'nombre ASC';
}


/**
 * Calcula la cantidad total de ítems en el carrito
 * almacenado en sesión.
 */
function totalItemsCarrito(): int {
    if (!isset($_SESSION['carrito'])) {
        return 0;
    }

    $total = 0;
    foreach ($_SESSION['carrito'] as $item) {
        $total += $item['cantidad'];
    }

    return $total;
}


function setMensaje($mensaje) {
    $_SESSION['mensaje'] = $mensaje;
}

function getMensaje() {
    if (!empty($_SESSION['mensaje'])) {
        $mensaje = $_SESSION['mensaje'];
        unset($_SESSION['mensaje']); // CLAVE
        return $mensaje;
    }
    return null;
}





 
function usuarioLogueado(): bool {
    return isset($_SESSION['usuario']);
}  // Indica si hay un usuario autenticado.

function usuarioActual() {
    return $_SESSION['usuario'] ?? null;
}  //Devuelve los datos del usuario autenticado

function usuarioId() {
    return $_SESSION['usuario']['id'] ?? null;
}




/**
 * Obtiene una compra validando que:
 * - Si hay usuario autenticado, solo pueda acceder a su propia compra.
 * - Si es invitado, solo pueda acceder a compras sin usuario asociado.
 * Previene acceso indebido a compras de terceros.
 */
function obtenerCompraSegura(PDO $pdo, int $compraId, ?array $usuario)
{
    if ($usuario) {
        // Usuario o admin → solo su compra
        $stmt = $pdo->prepare("
            SELECT * FROM compras
            WHERE id = ? AND usuario_id = ?
        ");
        $stmt->execute([$compraId, $usuario['id']]);
    } else {
        // Invitado → solo compras sin usuario
        $stmt = $pdo->prepare("
            SELECT * FROM compras
            WHERE id = ? AND usuario_id IS NULL
        ");
        $stmt->execute([$compraId]);
    }

    return $stmt->fetch(PDO::FETCH_ASSOC);
}