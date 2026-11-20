<?php
declare(strict_types=1);

/* Site público: páginas, formulário de contacto, robots.txt e sitemap.xml. */

const PAGES = [
    'home' => ['path' => '/', 'css' => 'home'],
    'orioos' => ['path' => '/orioos', 'css' => 'orioos'],
    'news' => ['path' => '/news', 'css' => 'news'],
];

/** URLs antigos do modelo estático → novos (301). */
const LEGACY = ['/index.html' => '/', '/orioos.html' => '/orioos', '/news.html' => '/news'];

function route_site(string $path): void
{
    if (isset(LEGACY[$path])) {
        redirect(LEGACY[$path], 301);
    }
    if ($path === '/contact' && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        handle_contact();
        return;
    }
    if ($path === '/robots.txt') {
        robots_txt();
        return;
    }
    if ($path === '/sitemap.xml') {
        sitemap_xml();
        return;
    }
    foreach (PAGES as $page => $def) {
        if ($def['path'] === $path) {
            track_view('page');
            $fn = 'page_' . $page;
            $fn();
            return;
        }
    }
    not_found();
}

/** Subsistemas (PK, DSS…) definidos em Textos → Geral: lista de [nome, endereço]. */
function subsystems(): array
{
    $out = [];
    foreach (lines(tx('site.subsystems')) as $line) {
        [$name, $url] = array_map('trim', explode('|', $line, 2)) + [1 => ''];
        $url = clean_link($url);
        if ($name !== '' && $url !== '') {
            $out[] = [$name, $url];
        }
    }
    return $out;
}

function not_found(): void
{
    track_view('404');
    http_response_code(404);
    render('site/404', ['page' => '404', 'title' => 'Page not found — ' . tx('site.name'), 'description' => '', 'noindex' => true]);
}

function indexing_allowed(): bool
{
    return tx('site.allow_indexing') === '1';
}

/** Prefixo dos links de âncora: vazio na página inicial, "/" nas outras. */
function hp(string $page): string
{
    return $page === 'home' ? '' : '/';
}

/** Ícone (SVG de traço) a partir do conteúdo interno guardado (já limpo ao gravar). */
function icon_svg(string $inner): string
{
    return '<svg viewBox="0 0 24 24" fill="none" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">' . $inner . '</svg>';
}

/** "2026-07-23" → "23 Jul 2026". */
function news_date(string $iso): string
{
    $t = strtotime($iso . ' 00:00:00');
    return $t ? date('j M Y', $t) : $iso;
}

function news_label(string $category): string
{
    return collections()['news']['fields']['category']['options'][$category] ?? $category;
}

/* ---------- Páginas ---------- */

function page_home(): void
{
    render('site/home', [
        'page' => 'home',
        'title' => tx('home.meta_title'),
        'description' => tx('home.meta_description'),
        'tech' => col_items('tech'),
        'sectors' => col_items('sectors'),
        'team' => col_items('team'),
    ]);
}

function page_orioos(): void
{
    $caps = ['agriculture' => [], 'surveillance' => []];
    foreach (col_items('caps') as $c) {
        $caps[$c['mode']][] = $c;
    }
    render('site/orioos', [
        'page' => 'orioos',
        'title' => tx('orioos.meta_title'),
        'description' => tx('orioos.meta_description'),
        'metrics' => col_items('metrics'),
        'caps' => array_filter($caps),
        'specs' => col_items('specs'),
        'sensors' => col_items('sensors'),
        'platform' => col_items('platform'),
        'reqs' => col_items('reqs'),
    ]);
}

function page_news(): void
{
    $all = col_items('news');
    $featured = null;
    $rest = [];
    foreach ($all as $item) {
        if ($featured === null && $item['featured']) {
            $featured = $item;
        } else {
            $rest[] = $item;
        }
    }
    render('site/news', [
        'page' => 'news',
        'title' => tx('news.meta_title'),
        'description' => tx('news.meta_description'),
        'featured' => $featured,
        'items' => $rest,
    ]);
}

/* ---------- Formulário de contacto ---------- */

function contact_reply(bool $ok, string $msg, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => $ok, 'message' => $msg]);
    exit;
}

function handle_contact(): void
{
    // Campo-isco: os robôs preenchem-no, as pessoas não o veem.
    if (trim((string) ($_POST['website'] ?? '')) !== '') {
        contact_reply(true, tx('home.form_ok'));
    }
    $name = trim(preg_replace('/\s+/u', ' ', (string) ($_POST['name'] ?? '')) ?? '');
    $email = trim((string) ($_POST['email'] ?? ''));
    $message = trim(str_replace("\r\n", "\n", (string) ($_POST['message'] ?? '')));

    if ($name === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        contact_reply(false, 'Please fill in your name, a valid email and a message.', 422);
    }
    $name = mb_substr($name, 0, 120);
    $message = mb_substr($message, 0, 5000);

    // Limite por visitante: 5 mensagens por hora (guarda só um hash do IP, não o IP).
    $ipHash = hash('sha256', client_ip() . '|' . config('setup_key', ''));
    $st = db()->prepare('SELECT COUNT(*) FROM messages WHERE ip_hash = ? AND created_at > ?');
    $st->execute([$ipHash, date('Y-m-d H:i:s', time() - 3600)]);
    if ((int) $st->fetchColumn() >= 5) {
        contact_reply(false, 'Too many messages. Please try again later.', 429);
    }

    db()->prepare('INSERT INTO messages (name, email, message, ip_hash, created_at) VALUES (?, ?, ?, ?, ?)')
        ->execute([$name, $email, $message, $ipHash, now()]);
    $id = (int) db()->lastInsertId();

    // Cópia por email (se o servidor tiver mail()); a mensagem já está guardada no backoffice.
    $to = tx('site.notify_email');
    if ($to !== '' && filter_var($to, FILTER_VALIDATE_EMAIL) && function_exists('mail')) {
        $host = parse_url(base_url(), PHP_URL_HOST) ?: 'localhost';
        $headers = [
            'From: ' . tx('site.name') . ' <no-reply@' . $host . '>',
            'Reply-To: ' . $email, // já validado: sem quebras de linha
            'Content-Type: text/plain; charset=UTF-8',
        ];
        $subject = '=?UTF-8?B?' . base64_encode(tx('site.name') . ' — Contact Request') . '?=';
        $body = "Name: $name\nEmail: $email\n\nMessage:\n$message\n";
        if (@mail($to, $subject, $body, implode("\r\n", $headers))) {
            db()->prepare('UPDATE messages SET mailed = 1 WHERE id = ?')->execute([$id]);
        }
    }
    contact_reply(true, tx('home.form_ok'));
}

/* ---------- SEO ---------- */

function robots_txt(): void
{
    header('Content-Type: text/plain; charset=utf-8');
    if (!indexing_allowed()) {
        echo "User-agent: *\nDisallow: /\n";
        return;
    }
    echo "User-agent: *\nDisallow: /admin\nDisallow: /contact\n\nSitemap: " . abs_url('/sitemap.xml') . "\n";
}

function sitemap_xml(): void
{
    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    if (indexing_allowed()) {
        foreach (PAGES as $def) {
            echo '  <url><loc>' . e(abs_url($def['path'])) . "</loc></url>\n";
        }
    }
    echo "</urlset>\n";
}
