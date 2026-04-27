# Deploy al hosting (cPanel tradicional)

**Solución implementada:** `git clone` + `git pull` desde la **Terminal del cPanel**, con autenticación por deploy key SSH.

Este flujo funciona en cualquier cPanel que tenga la aplicación **Terminal** habilitada
(Avanzada → Terminal) y acceso a `git`. Es el método que usa este proyecto en producción.

## Por qué SSH y no FTP/FTPS/SFTP desde GitHub Actions

Se descartó el deploy automático por GitHub Actions porque el proveedor de hosting
tiene un firewall que **bloquea todas las conexiones entrantes desde IPs de
datacenters** (incluidas las de GitHub Actions). Probamos FTP, FTPS y SFTP en
múltiples puertos (21, 22, 2222, 2082, 2083, 65002) — todos dieron timeout.

La Terminal del cPanel no tiene ese problema porque hace conexiones **salientes**
desde el propio server hacia GitHub.

## Setup inicial (una sola vez)

### 1. Generar una deploy key en el servidor

En cPanel: **Avanzada → Terminal**. Corré:

```bash
ssh-keygen -t ed25519 -f ~/.ssh/github_deploy -N "" -C "cpanel-deploy"
cat ~/.ssh/github_deploy.pub
```

Copiá la línea completa que empieza con `ssh-ed25519 ...`.

### 2. Registrar la pública como Deploy Key en GitHub

Abrí: `https://github.com/<owner>/<repo>/settings/keys/new`

- **Title:** `cpanel-deploy`
- **Key:** pegá la pública del paso 1
- **Allow write access:** dejar sin marcar (read-only es suficiente)
- Click **Add key**

### 3. Configurar SSH y verificar acceso

En la Terminal del cPanel:

```bash
mkdir -p ~/.ssh && chmod 700 ~/.ssh
printf 'Host github.com\n\tHostName github.com\n\tUser git\n\tIdentityFile ~/.ssh/github_deploy\n\tIdentitiesOnly yes\n' > ~/.ssh/config
chmod 600 ~/.ssh/config
ssh-keyscan github.com >> ~/.ssh/known_hosts 2>/dev/null
ssh -T git@github.com
```

Debe responder con algo del tipo:
`Hi <owner>/<repo>! You've successfully authenticated, but GitHub does not provide shell access.`

### 4. Clonar el repo en `public_html`

Hace backup del contenido previo y clona limpio:

```bash
cd ~
mv public_html public_html.backup.$(date +%Y%m%d_%H%M%S)
git clone git@github.com:<owner>/<repo>.git public_html
```

### 5. Crear la BD y correr el installer

En cPanel:

1. **Bases de datos → MySQL Databases** → crear BD + usuario + asignar con ALL PRIVILEGES.
2. Anotar: nombre de BD, usuario (ambos con el prefijo del cPanel, ej. `cse12345_...`), password, host (`localhost`).

En el browser:

1. Abrir `https://<dominio>/install/`
2. Pegar las 4 credenciales + crear el primer admin (email + password)
3. Submit. El wizard escribe `config.php`, corre las migraciones en orden y crea el admin.
4. **Borrar la carpeta `/install/`** desde **Administrador de archivos** del cPanel (seguridad básica).

### 6. (Opcional) Alias de deploy

Para que cada deploy sea un solo comando:

```bash
echo "alias deploy='cd ~/public_html && git pull'" >> ~/.bashrc
source ~/.bashrc
```

## Flujo de trabajo diario

Desde tu máquina local:

```bash
# editar código
git add .
git commit -m "feat: lo que hayas cambiado"
git push
```

En la Terminal del cPanel:

```bash
deploy   # alias; equivale a "cd ~/public_html && git pull"
```

Tarda 1-2 segundos. Si la migración que subiste agrega tablas o settings, van a
aplicarse automáticamente la próxima vez que alguien visite `/admin/` (el método
`runMigrations()` se ejecuta ahí y es idempotente).

## Rollback

### Revertir el último commit

En local:

```bash
git revert HEAD
git push
```

En el hosting: `deploy` (o `cd ~/public_html && git pull`).

### Volver a un commit anterior

En local:

```bash
git reset --hard <hash-bueno>
git push --force-with-lease
```

En el hosting:

```bash
cd ~/public_html
git fetch
git reset --hard origin/main
```

## Archivos que NO viajan por git

Están excluidos en `.gitignore` y no se suben al repo:

- `config.php` — credenciales de BD del hosting. Cada entorno (local / hosting) tiene el suyo.
- `/uploads/` — contenido subido por el cliente en el futuro. No debe volver desde el repo.
- `/respaldo/` — backups locales del dev.
- `/.claude/`, `.DS_Store`, `*.log` — ruido local.

Como el hosting NO borra archivos en cada `git pull`, `config.php` y `/uploads/`
sobreviven perfectamente entre deploys. Nunca se pisan.

## Troubleshooting

### `git pull` pide usuario/password

Significa que se usó `https://` en vez de `git@github.com:`. Reconfigurar el remote:

```bash
cd ~/public_html
git remote set-url origin git@github.com:<owner>/<repo>.git
```

### `Permission denied (publickey)`

La deploy key no está cargada o no está autorizada.

```bash
ssh -vT git@github.com 2>&1 | grep -i "identity\|accepted"
```

Verificar que exista `~/.ssh/github_deploy` y que `~/.ssh/config` apunte a esa ruta.
Confirmar en GitHub que la Deploy Key está listada y habilitada.

### El pull funcionó pero el sitio no refleja cambios

Cache del browser (Ctrl+Shift+R). O si usás un caching plugin en el server,
limpiar cache. Este proyecto no usa cache de salida.

### El `install/` dice "Ya instalado"

Es porque `config.php` existe. Correcto: NO hay que volver a instalar. Ir directo
a `/admin/`. Si necesitás reinstalar (BD nueva), borrá `config.php` y visitá
`/install/` de nuevo.
