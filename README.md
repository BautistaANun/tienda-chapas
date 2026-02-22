🛒 Tienda Chapas – Sistema de ventas online

Sistema de ventas online desarrollado en PHP + MySQL, con carrito de compras, pagos en efectivo y Mercado Pago, envío de mails automáticos y panel de usuario/admin.

🚀 Requisitos del servidor

PHP 8.0 o superior

MySQL / MariaDB

Apache o Nginx

HTTPS obligatorio (requerido por Mercado Pago)

Acceso a phpMyAdmin o similar

SMTP activo (email del dominio recomendado)

📂 Estructura del proyecto

El proyecto debe subirse dentro de la carpeta pública del hosting (ej: public_html):

public_html/
├─ assets/
|  |css/...
│  |js/...
|  |uploads/...
|__config/...
|__includes/... 
│__principal/...  
|  |_admin/...
|  |_ auth/...
| carrito.php
| webhook.php
| ...
|_vendor/...

🗄️ Base de datos

Crear una base de datos MySQL

Crear un usuario y asignarle todos los permisos

Importar el archivo SQL:

/database/tienda_chapas.sql
⚙️ Configuración del proyecto
🔧 config/database.php

Editar con los datos del hosting:

$host = 'localhost';
$db   = 'NOMBRE_DB';
$user = 'USUARIO_DB';
$pass = 'PASSWORD_DB';
🌐 config/config.php

Configurar la URL base del sitio:

define('BASE_URL', 'https://tudominio.com/');
define('APP_ENV', 'prod'); // dev | prod
🛍️ Panel de Administración

Acceso restringido a usuarios con rol admin.

Funcionalidades:

Crear productos

Subir imágenes (con validación de tamaño)

Editar productos

Activar / desactivar productos

Gestión de stock

Visualización de compras

Control de estados de pedidos

Las imágenes se almacenan en la carpeta /uploads/.

💳 Mercado Pago (Producción)

Editar el archivo:

config/mercadopago.php

Colocar:

Access Token REAL

Webhook Secret REAL

🔔 Webhook en Mercado Pago Developers

Configurar la URL:

https://tudominio.com/principal/webhook_mp.php
📧 Emails (SMTP)

Configurar en:

includes/mailer.php

Recomendado usar un email del dominio:

ventas@tudominio.com

Las credenciales SMTP se cargan en entorno de producción (prod).

✅ Flujo del sistema

Compra en efectivo → mail automático al cliente y al admin

Compra con Mercado Pago → webhook actualiza el estado automáticamente

Usuario puede ver sus compras

Admin puede ver todas las compras y estados

Gestión completa de productos desde panel admin

🧪 Pruebas recomendadas

Registro y login

Compra en efectivo

Compra con Mercado Pago

Recepción de mails

Actualización automática por webhook

Visualización en "Mis compras"

Creación y edición de productos desde panel admin

📌 Notas importantes

El proyecto fue desarrollado y probado en entorno local (XAMPP).

pago_forzado.php solo debe existir en entorno de desarrollo (dev).

En producción, APP_ENV debe estar configurado como prod.

HTTPS es obligatorio para Mercado Pago.

Ante cualquier duda, revisar los comentarios en el código.

Proyecto funcional y probado en entorno local (XAMPP) y listo para despliegue en hosting con HTTPS.