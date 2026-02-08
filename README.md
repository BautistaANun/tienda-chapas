# 🛒 Tienda Chapas – Sistema de ventas online

Sistema de ventas online desarrollado en **PHP + MySQL**, con carrito de compras, pagos en efectivo y Mercado Pago, envío de mails automáticos y panel de usuario/admin.

---

## 🚀 Requisitos del servidor

- PHP **8.0 o superior**
- MySQL / MariaDB
- Apache o Nginx
- **HTTPS obligatorio** (requerido por Mercado Pago)
- Acceso a phpMyAdmin o similar
- SMTP activo (email del dominio recomendado)

---

## 📂 Estructura del proyecto

El proyecto debe subirse dentro de la carpeta pública del hosting (ej: `public_html`):

public_html/
├─ index.php
├─ principal/
├─ includes/
├─ config/
├─ assets/
├─ database/


---

## 🗄️ Base de datos

1. Crear una base de datos MySQL
2. Crear un usuario y asignarle **todos los permisos**
3. Importar el archivo SQL:

/database/tienda_chapas.sql


---

## ⚙️ Configuración del proyecto

### 🔧 config/database.php

Editar con los datos del hosting:

```php
$host = 'localhost';
$db   = 'NOMBRE_DB';
$user = 'USUARIO_DB';
$pass = 'PASSWORD_DB';

🌐 config/config.php
Configurar la URL base del sitio:


define('BASE_URL', 'https://tudominio.com/');
define('APP_ENV', 'prod'); // dev | prod
💳 Mercado Pago (Producción)
Editar el archivo:


config/mercadopago.php
## Colocar:

Access Token REAL

Webhook Secret REAL

Webhook en Mercado Pago Developers
Configurar la URL del webhook:

https://tudominio.com/principal/webhook_mp.php
📧 Emails (SMTP)

## Configurar el envío de mails en:


includes/mailer.php
📌 Recomendado usar un email del dominio:


ventas@tudominio.com
##✅ Flujo del sistema
Compra en efectivo → mail automático al cliente y al admin

Compra con Mercado Pago → webhook actualiza el estado automáticamente

Usuario puede ver sus compras

Admin puede ver todas las compras y estados

##🧪 Pruebas recomendadas
Registro y login

Compra en efectivo

Compra con Mercado Pago

Recepción de mails

Actualización de estado de compra

Visualización en "Mis compras"

##📌 Notas importantes
El proyecto fue desarrollado y probado en entorno local

pago_forzado.php solo debe existir en entorno de desarrollo (dev)

En -producción-, APP_ENV debe estar configurado como prod

Ante cualquier duda, revisar los comentarios en el código

Las credenciales "SMTP" se cargan en entorno de producción (prod). Éstas se pueden colocar directamente en el servidor manualmente