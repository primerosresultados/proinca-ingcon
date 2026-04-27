# cPanel / hPanel Starter

Sitio para clientes chicos sobre PHP + MySQL en hosting compartido (Hostinger / cPanel). **Sin build step, sin Node, sin composer.** Subís los archivos por FTP y listo.

## Qué incluye

- **Landing pública** con form de contacto (honeypot + timing + CSRF).
- **Panel admin** con login, listado de leads, filtros, paginación, detalle, notas con autoría, exportación CSV.
- **Configuración editable** desde admin: nombre del sitio, email de notificaciones, Google Analytics, Facebook Pixel, auto-respuesta.
- **Mini-CMS de páginas** (slug + título + body HTML + SEO), accesible en `/slug`.
- **Notificación por email** al admin cada vez que entra un lead, y auto-respuesta opcional al cliente.
- **Instalador web** que crea `config.php`, corre migraciones y se auto-bloquea.
- **Migraciones** idempotentes en `/migrations/NNN_*.sql`.

## Requisitos

- PHP 8.0+ (Hostinger/cPanel típicos traen 8.1+).
- MySQL / MariaDB.
- Apache o LiteSpeed con `mod_rewrite` y `mod_headers`.

## Despliegue en Hostinger (hPanel)

1. **Crear base de datos**: hPanel → *Bases de datos → MySQL* → crear DB + usuario. Guardar host/nombre/usuario/password.
2. **Subir archivos**: vía FTP / *Administrador de archivos*, a `public_html/`. Excluir `/respaldo/`.
3. **Permisos**: la raíz debe ser escribible por PHP para crear `config.php` (normalmente 755 alcanza en Hostinger).
4. **Visitar `https://tudominio.com/install/`**: completar credenciales DB + crear cuenta admin.
5. **Confirmar**: la instalación auto-bloquea `/install/`. Igual borrá la carpeta por FTP.
6. **Configurar**: entrar a `/admin/ → Configuración` y setear:
   - Email de notificaciones (por default queda el del admin).
   - `notification_from` con un email de tu dominio (ej. `no-reply@tudominio.com`) para que no caiga en spam.
   - GA / Pixel si corresponde.
7. **Probar**: mandar un lead desde el form público; confirmar que llega el email.

## Configurar cron de backup (opcional)

hPanel → *Trabajos cron* → agregar:

```
0 3 * * * mysqldump -h <host> -u <user> -p<pass> <db> | gzip > ~/backups/db-$(date +\%Y\%m\%d).sql.gz && find ~/backups -mtime +14 -delete
```

Carpeta `~/backups` fuera de `public_html/`.

## Estructura

```
/
├── index.php              # Landing + router CMS público
├── admin/index.php        # Panel admin (router)
├── install/index.php      # Instalador (se auto-bloquea)
├── config.php             # Generado por el instalador (NO commitear)
├── lib/                   # Bootstrap, DB, auth, CSRF, mail, migraciones, helpers
├── migrations/            # NNN_*.sql idempotentes
├── components/            # Partials reutilizables
│   ├── lead_form.php
│   ├── admin_nav.php
│   └── admin/             # Vistas del panel
├── assets/css/            # Estilos
├── uploads/               # Logs + futuros archivos (bloqueado a web excepto públicos)
└── .htaccess              # HTTPS redirect, HSTS, bloqueos
```

## Seguridad (checklist pre-producción)

- [x] `config.php` fuera del repo (`.gitignore`).
- [x] HTTPS forzado por `.htaccess` + HSTS.
- [x] CSRF en todos los forms POST.
- [x] Rate-limit de login en DB (IP + email, ventana 15 min).
- [x] Passwords con `bcrypt` cost 12.
- [x] Session regenerate en login y cambio de password.
- [x] Session con `HttpOnly`, `SameSite=Lax`, `Secure` (si HTTPS), idle timeout.
- [x] Migraciones gated por auth (no ejecutables por anónimos).
- [x] Instalador auto-bloqueado post-instalación.
- [x] Honeypot + timing + dedupe en form público.
- [x] IP real detrás de proxy/Cloudflare.
- [ ] **Borrar `/install/`** del servidor una vez instalado.
- [ ] Cron de backup configurado.
- [ ] (Opcional) reCAPTCHA / Turnstile si recibís mucho spam.

## Desarrollo local

```bash
# Crear DB local, luego:
cp config.example.php config.php   # editar credenciales
php -S localhost:8000               # abrir http://localhost:8000/install/
```

Las migraciones corren automáticamente al entrar al admin autenticado.

## Agregar una migración

Crear `/migrations/007_lo_que_sea.sql` con SQL idempotente (`CREATE TABLE IF NOT EXISTS`, `INSERT ... ON DUPLICATE KEY UPDATE`). Se aplica al próximo pageload del admin. Prefijar con `_` para ignorar (ej: templates).

## Notas

- El campo `leads.notes` (texto único) quedó por retrocompatibilidad; las notas nuevas van a `lead_notes` con autoría y timestamp.
- El renderer CMS muestra el `body` como HTML tal cual — **solo lo edita el admin autenticado**, no habilitar edición pública.
- `mail()` usa el MTA local de Hostinger. Para SMTP autenticado (Gmail/SendGrid) reemplazar `lib/mail.php` por PHPMailer.
