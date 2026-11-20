<?php
declare(strict_types=1);

function config(string $key, mixed $default = null): mixed
{
    global $CONFIG;
    return $CONFIG[$key] ?? $default;
}

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
}

function base_url(): string
{
    $url = getenv('SITE_URL') ?: (string) config('base_url', '');
    if ($url === '') {
        $url = (is_https() ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    }
    return rtrim($url, '/');
}

/** Converte um caminho relativo (/news) num URL absoluto. */
function abs_url(string $path): string
{
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    return base_url() . '/' . ltrim($path, '/');
}

/** Caminho de um ficheiro (imagem, vídeo) pronto para usar num src/href. */
function asset_url(string $path): string
{
    if ($path === '' || preg_match('#^(https?:)?//#i', $path) || $path[0] === '/') {
        return $path;
    }
    return '/' . $path;
}

function redirect(string $to, int $code = 302): never
{
    header('Location: ' . $to, true, $code);
    exit;
}

function now(): string
{
    return date('Y-m-d H:i:s');
}

/** Data em português para o backoffice (ex.: "3 de março de 2026"). */
function format_date(?string $datetime): string
{
    if (!$datetime) {
        return '';
    }
    $t = strtotime($datetime);
    $months = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho',
        'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
    return date('j', $t) . ' de ' . $months[(int) date('n', $t) - 1] . ' de ' . date('Y', $t);
}

function slugify(string $s): string
{
    $map = [
        'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
        'ç' => 'c', 'ñ' => 'n',
    ];
    $s = strtr(mb_strtolower(trim($s), 'UTF-8'), $map);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? '';
    return substr(trim($s, '-'), 0, 80);
}

function render(string $view, array $vars = [], string $layout = 'site/layout'): void
{
    extract($vars);
    ob_start();
    require ROOT . "/app/views/$view.php";
    $content = ob_get_clean();
    require ROOT . "/app/views/$layout.php";
}

/* ---------- Definições e textos (tabela settings) ---------- */

/** Cache das definições (por referência, para o admin o poder atualizar). */
function &settings_cache(): ?array
{
    static $cache = null;
    return $cache;
}

function settings(): array
{
    $cache = &settings_cache();
    if ($cache === null) {
        $cache = [];
        foreach (db()->query('SELECT name, value FROM settings') as $row) {
            $cache[$row['name']] = (string) $row['value'];
        }
    }
    return $cache;
}

function settings_reset(): void
{
    $cache = &settings_cache();
    $cache = null;
}

/** Texto editável: o valor gravado no backoffice, ou o valor por omissão se ainda não foi editado. */
function tx(string $key): string
{
    $s = settings();
    if (array_key_exists($key, $s)) {
        return $s[$key];
    }
    return (string) (text_default($key) ?? '');
}

function indexing_allowed(): bool
{
    return tx('site.allow_indexing') === '1';
}

/** Texto escapado; *palavra* passa a <em>palavra</em> (usado nos títulos com destaque). */
function tx_html(string $key): string
{
    return em_markup(tx($key));
}

function em_markup(string $s): string
{
    return preg_replace('/\*([^*\n]+)\*/u', '<em>$1</em>', e($s)) ?? e($s);
}

/** Texto com quebras de linha como <br>. */
function tx_br(string $key): string
{
    return nl2br(e(tx($key)), false);
}

/** Linhas não vazias de um texto (uma por linha). */
function lines(string $text): array
{
    return array_values(array_filter(array_map('trim', preg_split('/\R/', $text) ?: []), fn($l) => $l !== ''));
}

/** Imagem/vídeo de um campo de texto, com valor por omissão se vazio. */
function tx_file(string $key): string
{
    $v = tx($key);
    return $v !== '' ? asset_url($v) : '';
}

/* ---------- Sessão, autenticação e CSRF (backoffice) ---------- */

function session_boot(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_name('vf_admin');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/** Utilizador autenticado, sem abrir sessão para visitantes anónimos. */
function current_user_id(): ?int
{
    if (session_status() !== PHP_SESSION_ACTIVE && !isset($_COOKIE['vf_admin'])) {
        return null;
    }
    session_boot();
    return isset($_SESSION['uid']) ? (int) $_SESSION['uid'] : null;
}

function csrf_token(): string
{
    session_boot();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $sent = $_POST['csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(400);
        exit('Pedido inválido (CSRF). Volte atrás e recarregue a página.');
    }
}

function flash(?string $msg = null, string $type = 'ok'): ?array
{
    session_boot();
    if ($msg !== null) {
        $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
        return null;
    }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}
