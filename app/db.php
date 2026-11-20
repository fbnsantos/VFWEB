<?php
declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo) {
        return $pdo;
    }
    $path = (string) config('db_path', ROOT . '/data/site.sqlite');
    if (!is_dir(dirname($path))) {
        mkdir(dirname($path), 0775, true);
    }
    $fresh = !is_file($path);
    $pdo = new PDO('sqlite:' . $path, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA journal_mode = WAL; PRAGMA foreign_keys = ON; PRAGMA busy_timeout = 5000;');
    migrate($pdo);
    if ($fresh) {
        seed($pdo);
    }
    return $pdo;
}

/** Nome da tabela de uma coleção. */
function col_table(string $name): string
{
    return 'c_' . $name;
}

function migrate(PDO $pdo): void
{
    $pdo->exec(<<<SQL
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY,
        username TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL,
        created_at TEXT NOT NULL
    );
    CREATE TABLE IF NOT EXISTS settings (
        name TEXT PRIMARY KEY,
        value TEXT NOT NULL DEFAULT ''
    );
    CREATE TABLE IF NOT EXISTS media (
        id INTEGER PRIMARY KEY,
        path TEXT NOT NULL UNIQUE,
        original_name TEXT NOT NULL,
        mime TEXT NOT NULL,
        size INTEGER NOT NULL,
        width INTEGER,
        height INTEGER,
        alt TEXT NOT NULL DEFAULT '',
        created_at TEXT NOT NULL
    );
    CREATE TABLE IF NOT EXISTS login_attempts (
        ip TEXT NOT NULL,
        attempted_at INTEGER NOT NULL
    );
    CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        message TEXT NOT NULL,
        ip_hash TEXT NOT NULL DEFAULT '',
        mailed INTEGER NOT NULL DEFAULT 0,
        is_read INTEGER NOT NULL DEFAULT 0,
        created_at TEXT NOT NULL
    );
    CREATE TABLE IF NOT EXISTS page_views (
        id INTEGER PRIMARY KEY,
        created_at TEXT NOT NULL,
        day TEXT NOT NULL,
        path TEXT NOT NULL,
        content_type TEXT NOT NULL DEFAULT '',
        content_id INTEGER,
        visitor TEXT NOT NULL,
        referrer_host TEXT NOT NULL DEFAULT '',
        source TEXT NOT NULL DEFAULT '',
        channel TEXT NOT NULL DEFAULT '',
        country TEXT NOT NULL DEFAULT '',
        language TEXT NOT NULL DEFAULT '',
        device TEXT NOT NULL DEFAULT '',
        os TEXT NOT NULL DEFAULT '',
        browser TEXT NOT NULL DEFAULT ''
    );
    CREATE INDEX IF NOT EXISTS page_views_day ON page_views (day);
    CREATE TABLE IF NOT EXISTS bot_hits (
        day TEXT NOT NULL,
        bot TEXT NOT NULL,
        hits INTEGER NOT NULL DEFAULT 0,
        PRIMARY KEY (day, bot)
    );
    SQL);

    // Uma tabela por coleção, criada e atualizada a partir de collections() (app/content.php).
    foreach (collections() as $name => $col) {
        $table = col_table($name);
        $defs = [];
        foreach ($col['fields'] as $field => $spec) {
            $defs[$field] = $spec['type'] === 'check' ? 'INTEGER NOT NULL DEFAULT 0' : "TEXT NOT NULL DEFAULT ''";
        }
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id INTEGER PRIMARY KEY,
            sort INTEGER NOT NULL DEFAULT 0,
            visible INTEGER NOT NULL DEFAULT 1,
            created_at TEXT NOT NULL DEFAULT '',
            updated_at TEXT NOT NULL DEFAULT ''";
        foreach ($defs as $field => $def) {
            $sql .= ",\n            \"$field\" $def";
        }
        $pdo->exec($sql . "\n        )");

        // Campos acrescentados depois de a tabela existir.
        $existing = array_column($pdo->query("PRAGMA table_info($table)")->fetchAll(), 'name');
        foreach ($defs as $field => $def) {
            if (!in_array($field, $existing, true)) {
                $pdo->exec("ALTER TABLE $table ADD COLUMN \"$field\" $def");
            }
        }
    }
}

/** Conteúdo inicial (o do site original) e registo das imagens/vídeos que já vêm no repositório. */
function seed(PDO $pdo): void
{
    $now = now();
    foreach (seed_data() as $name => $rows) {
        $col = collections()[$name] ?? null;
        if (!$col) {
            continue;
        }
        $fields = array_keys($col['fields']);
        $cols = implode(', ', array_map(fn($f) => "\"$f\"", $fields));
        $marks = implode(', ', array_fill(0, count($fields), '?'));
        $st = $pdo->prepare('INSERT INTO ' . col_table($name) . " (sort, visible, created_at, updated_at, $cols) VALUES (?, 1, ?, ?, $marks)");
        foreach ($rows as $i => $row) {
            $st->execute(array_merge([$i + 1, $now, $now], array_map(fn($f) => $row[$f] ?? ($col['fields'][$f]['type'] === 'check' ? 0 : ''), $fields)));
        }
    }

    $mimes = ['png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'webp' => 'image/webp', 'gif' => 'image/gif', 'mp4' => 'video/mp4', 'webm' => 'video/webm'];
    $st = $pdo->prepare('INSERT OR IGNORE INTO media (path, original_name, mime, size, width, height, alt, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    foreach (['images', 'videos'] as $dir) {
        foreach (glob(ROOT . "/$dir/*") ?: [] as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!is_file($file) || !isset($mimes[$ext])) {
                continue;
            }
            $info = str_starts_with($mimes[$ext], 'image/') ? @getimagesize($file) : false;
            $st->execute(["/$dir/" . basename($file), basename($file), $mimes[$ext], filesize($file),
                $info ? $info[0] : null, $info ? $info[1] : null, '', $now]);
        }
    }
}

function user_count(): int
{
    return (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
}

/* ---------- Leitura das coleções (site público) ---------- */

/** Itens visíveis de uma coleção, pela ordem definida (sort, ou a ordem própria da coleção). */
function col_items(string $name, string $where = '', array $params = []): array
{
    $col = collections()[$name];
    $order = $col['order'] ?? (isset($col['group']) ? $col['group'] . ', sort, id' : 'sort, id');
    $sql = 'SELECT * FROM ' . col_table($name) . ' WHERE visible = 1' . ($where !== '' ? " AND ($where)" : '') . " ORDER BY $order";
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}
