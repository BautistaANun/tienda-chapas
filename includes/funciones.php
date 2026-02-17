<?php


require_once __DIR__ . '/flash.php';


function obtenerProductos(PDO $pdo, array $filtros): array
{
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

    $limit  = (int) $filtros['porPagina'];
    $offset = ($filtros['page'] - 1) * $limit;

    $sql .= " LIMIT $limit OFFSET $offset";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function obtenerProductoPorId(PDO $pdo, int $id) {
    $sql = "SELECT * FROM productos WHERE id = :id AND activo = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function agruparPorCategoria(array $productos) {
    $resultado = [];

    foreach ($productos as $producto) {
        $categoria = $producto['categoria'];
        $resultado[$categoria][] = $producto;
    }

    return $resultado;
}


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


function e($texto) {
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}


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


function obtenerOrderBy(string $orden): string
{
    $permitidos = [
        'precio_asc'  => 'precio ASC',
        'precio_desc' => 'precio DESC',
        'nombre'      => 'nombre ASC'
    ];

    return $permitidos[$orden] ?? 'nombre ASC';
}

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
}

function usuarioActual() {
    return $_SESSION['usuario'] ?? null;
}

function usuarioId() {
    return $_SESSION['usuario']['id'] ?? null;
}

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