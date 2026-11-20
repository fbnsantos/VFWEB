<?php
declare(strict_types=1);

const LOGIN_MAX_ATTEMPTS = 5;
const LOGIN_WINDOW = 900; // 15 minutos

function route_admin(string $sub, string $method): void
{
    session_boot();
    header('X-Robots-Tag: noindex, nofollow');
    header('Cache-Control: no-store');

    if ($method === 'POST') {
        csrf_check();
    }
    if ($sub === '/setup') {
        admin_setup($method);
        return;
    }
    if (user_count() === 0) {
        redirect('/admin/setup');
    }
    if ($sub === '/login') {
        admin_login($method);
        return;
    }
    if (!current_user_id()) {
        redirect('/admin/login');
    }

    if ($sub === '/') {
        admin_dashboard();
    } elseif ($sub === '/logout' && $method === 'POST') {
        session_destroy();
        redirect('/admin/login');
    } elseif (preg_match('#^/texts/([a-z]+)$#', $sub, $m) && isset(text_pages()[$m[1]])) {
        admin_texts($m[1], $method);
    } elseif (preg_match('#^/c/([a-z]+)(?:/(new|\d+)(?:/(delete|move))?)?$#', $sub, $m) && isset(collections()[$m[1]])) {
        admin_collection($m[1], $m[2] ?? '', $m[3] ?? '', $method);
    } elseif ($sub === '/messages') {
        admin_messages();
    } elseif (preg_match('#^/messages/(\d+)/(read|unread|delete)$#', $sub, $m) && $method === 'POST') {
        admin_message_action((int) $m[1], $m[2]);
    } elseif ($sub === '/media') {
        admin_render('media', ['title' => 'Imagens e vídeos', 'items' => db()->query('SELECT * FROM media ORDER BY id DESC')->fetchAll()]);
    } elseif ($sub === '/media/upload' && $method === 'POST') {
        admin_media_upload();
    } elseif ($sub === '/media.json') {
        header('Content-Type: application/json');
        $kind = ($_GET['kind'] ?? 'image') === 'video' ? 'video/%' : 'image/%';
        $st = db()->prepare('SELECT path, original_name, alt FROM media WHERE mime LIKE ? ORDER BY id DESC LIMIT 300');
        $st->execute([$kind]);
        echo json_encode(array_map(fn($r) => ['title' => $r['alt'] ?: $r['original_name'], 'value' => $r['path']], $st->fetchAll()));
    } elseif (preg_match('#^/media/(\d+)$#', $sub, $m) && $method === 'POST') {
        db()->prepare('UPDATE media SET alt = ? WHERE id = ?')->execute([mb_substr(trim((string) ($_POST['alt'] ?? '')), 0, 300), (int) $m[1]]);
        flash('Texto alternativo guardado.');
        redirect('/admin/media');
    } elseif (preg_match('#^/media/(\d+)/delete$#', $sub, $m) && $method === 'POST') {
        delete_media((int) $m[1]);
        flash('Ficheiro apagado.');
        redirect('/admin/media');
    } elseif ($sub === '/password') {
        admin_password($method);
    } elseif ($sub === '/update') {
        admin_update($method);
    } else {
        http_response_code(404);
        admin_render('message', ['title' => 'Não encontrado', 'message' => 'Esta página do backoffice não existe.']);
    }
}

function admin_render(string $view, array $vars = []): void
{
    render("admin/$view", $vars, 'admin/layout');
}

/* ---------- Textos de uma página ---------- */

function admin_texts(string $pageKey, string $method): void
{
    $def = text_pages()[$pageKey];
    if ($method === 'POST') {
        $values = [];
        try {
            foreach ($def['sections'] as $fields) {
                foreach ($fields as $key => $spec) {
                    if ($spec['type'] !== 'note') {
                        $values[$key] = (string) collect_field($key, $spec);
                    }
                }
            }
        } catch (RuntimeException $ex) {
            flash($ex->getMessage(), 'error');
            redirect("/admin/texts/$pageKey");
        }
        $pdo = db();
        $pdo->beginTransaction();
        $st = $pdo->prepare('INSERT INTO settings (name, value) VALUES (?, ?) ON CONFLICT(name) DO UPDATE SET value = excluded.value');
        foreach ($values as $k => $v) {
            $st->execute([$k, $v]);
        }
        $pdo->commit();
        settings_reset();
        flash('Guardado. As alterações já estão no site.');
        redirect("/admin/texts/$pageKey");
    }
    admin_render('texts', ['title' => $def['label'], 'pageKey' => $pageKey, 'def' => $def]);
}

/* ---------- Listas (coleções) ---------- */

function admin_collection(string $name, string $id, string $action, string $method): void
{
    $col = collections()[$name];
    $table = col_table($name);
    $order = $col['order'] ?? (isset($col['group']) ? $col['group'] . ', sort, id' : 'sort, id');

    if ($id === '') { // lista
        $rows = db()->query("SELECT * FROM $table ORDER BY $order")->fetchAll();
        admin_render('list', ['title' => $col['label'], 'name' => $name, 'col' => $col, 'rows' => $rows]);
        return;
    }

    if ($id === 'new' || $action === '') { // formulário e gravação
        $row = null;
        if ($id !== 'new') {
            $st = db()->prepare("SELECT * FROM $table WHERE id = ?");
            $st->execute([(int) $id]);
            $row = $st->fetch() ?: null;
            if (!$row) {
                flash('Não encontrado.', 'error');
                redirect("/admin/c/$name");
            }
        }
        if ($method === 'POST') {
            admin_collection_save($name, $col, $row);
            return;
        }
        admin_render('edit', ['title' => ($row ? 'Editar ' : 'Nova ') . $col['singular'], 'name' => $name, 'col' => $col, 'row' => $row]);
        return;
    }

    if ($method !== 'POST') {
        redirect("/admin/c/$name");
    }
    if ($action === 'delete') {
        db()->prepare("DELETE FROM $table WHERE id = ?")->execute([(int) $id]);
        flash('Apagado.');
    } elseif ($action === 'move') {
        collection_move($name, $col, (int) $id, ($_POST['dir'] ?? '') === 'up' ? -1 : 1);
    }
    redirect("/admin/c/$name");
}

function admin_collection_save(string $name, array $col, ?array $row): void
{
    $table = col_table($name);
    $values = [];
    try {
        foreach ($col['fields'] as $key => $spec) {
            $values[$key] = collect_field($key, $spec);
            if (!empty($spec['required']) && $values[$key] === '') {
                throw new RuntimeException('Preencha o campo "' . $spec['label'] . '".');
            }
        }
    } catch (RuntimeException $ex) {
        flash($ex->getMessage(), 'error');
        // Volta ao formulário com o que foi escrito (os ficheiros enviados têm de ser escolhidos de novo).
        admin_render('edit', ['title' => ($row ? 'Editar ' : 'Nova ') . $col['singular'], 'name' => $name, 'col' => $col,
            'row' => array_merge($row ?? ['id' => null], ['visible' => isset($_POST['visible']) ? 1 : 0],
                array_map(fn($k) => posted_value($k), array_combine(array_keys($col['fields']), array_keys($col['fields']))))]);
        return;
    }
    $values['visible'] = isset($_POST['visible']) ? 1 : 0;

    $pdo = db();
    $pdo->beginTransaction();
    $now = now();
    if ($row) {
        $sets = implode(', ', array_map(fn($k) => "\"$k\" = ?", array_keys($values)));
        $pdo->prepare("UPDATE $table SET $sets, updated_at = ? WHERE id = ?")->execute([...array_values($values), $now, $row['id']]);
        $id = (int) $row['id'];
    } else {
        $sort = (int) $pdo->query("SELECT COALESCE(MAX(sort), 0) + 1 FROM $table")->fetchColumn();
        $cols = implode(', ', array_map(fn($k) => "\"$k\"", array_keys($values)));
        $marks = implode(', ', array_fill(0, count($values), '?'));
        $pdo->prepare("INSERT INTO $table (sort, created_at, updated_at, $cols) VALUES (?, ?, ?, $marks)")
            ->execute([$sort, $now, $now, ...array_values($values)]);
        $id = (int) $pdo->lastInsertId();
    }
    // "Em destaque" só pode estar ativo num item (o último marcado).
    if (!empty($values['featured'])) {
        $pdo->prepare("UPDATE $table SET featured = 0 WHERE id <> ?")->execute([$id]);
    }
    $pdo->commit();
    flash($row ? 'Guardado.' : 'Criado.');
    redirect("/admin/c/$name");
}

/** Troca um item com o vizinho (dentro do mesmo grupo, se a coleção tiver grupos). */
function collection_move(string $name, array $col, int $id, int $dir): void
{
    $table = col_table($name);
    $group = $col['group'] ?? null;
    $pdo = db();
    $st = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) {
        return;
    }
    $sql = "SELECT id FROM $table" . ($group ? " WHERE \"$group\" = ?" : '') . ' ORDER BY sort, id';
    $st = $pdo->prepare($sql);
    $st->execute($group ? [$row[$group]] : []);
    $ids = array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));
    $pos = array_search($id, $ids, true);
    $swap = $pos + $dir;
    if ($pos === false || !isset($ids[$swap])) {
        return;
    }
    [$ids[$pos], $ids[$swap]] = [$ids[$swap], $ids[$pos]];
    $pdo->beginTransaction();
    $up = $pdo->prepare("UPDATE $table SET sort = ? WHERE id = ?");
    foreach ($ids as $i => $itemId) {
        $up->execute([$i + 1, $itemId]);
    }
    $pdo->commit();
}

/* ---------- Mensagens do formulário de contacto ---------- */

function admin_messages(): void
{
    admin_render('messages', [
        'title' => 'Mensagens',
        'items' => db()->query('SELECT * FROM messages ORDER BY id DESC LIMIT 500')->fetchAll(),
    ]);
}

function admin_message_action(int $id, string $action): void
{
    if ($action === 'delete') {
        db()->prepare('DELETE FROM messages WHERE id = ?')->execute([$id]);
        flash('Mensagem apagada.');
    } else {
        db()->prepare('UPDATE messages SET is_read = ? WHERE id = ?')->execute([$action === 'read' ? 1 : 0, $id]);
    }
    redirect('/admin/messages');
}

function unread_messages(): int
{
    return (int) db()->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();
}

/* ---------- Imagens e vídeos ---------- */

/** Usado pelo formulário da biblioteca. */
function admin_media_upload(): void
{
    $files = $_FILES['file'] ?? null;
    if (!$files) {
        redirect('/admin/media');
    }
    // Normaliza envio múltiplo (name="file[]") e simples.
    $list = is_array($files['name'])
        ? array_map(fn($i) => array_combine(array_keys($files), array_column($files, $i)), array_keys($files['name']))
        : [$files];

    $done = 0;
    $errors = [];
    foreach ($list as $f) {
        if (($f['error'] ?? 0) === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        try {
            handle_upload($f, mb_substr(trim((string) ($_POST['alt'] ?? '')), 0, 300));
            $done++;
        } catch (RuntimeException $ex) {
            $errors[] = ($f['name'] ?? '') . ': ' . $ex->getMessage();
        }
    }
    flash($errors ? implode(' ', $errors) : "$done ficheiro(s) enviado(s).", $errors ? 'error' : 'ok');
    redirect('/admin/media');
}

/* ---------- Atualizar o site a partir do GitHub (mesmo código do git.php) ---------- */

function admin_update(string $method): void
{
    global $CONFIG;
    require_once ROOT . '/app/deploy.php';
    $config = (array) $CONFIG;
    $result = null;
    if ($method === 'POST') {
        $result = deploy_run($config, 'backoffice');
    }
    admin_render('update', [
        'title' => 'Atualizar site',
        'branch' => deploy_branch($config),
        'version' => deploy_current_version($config),
        'result' => $result,
        'hook' => strlen((string) ($config['deploy_secret'] ?? '')) >= 20,
        'log' => is_file(ROOT . '/data/deploy.log') ? implode('', array_slice(file(ROOT . '/data/deploy.log') ?: [], -30)) : '',
    ]);
}

/* ---------- Autenticação ---------- */

/** true se este IP já falhou demasiadas vezes (login e setup). */
function too_many_attempts(): bool
{
    db()->prepare('DELETE FROM login_attempts WHERE attempted_at < ?')->execute([time() - LOGIN_WINDOW]);
    $st = db()->prepare('SELECT COUNT(*) FROM login_attempts WHERE ip = ?');
    $st->execute([client_ip()]);
    return (int) $st->fetchColumn() >= LOGIN_MAX_ATTEMPTS;
}

function record_failed_attempt(): void
{
    db()->prepare('INSERT INTO login_attempts (ip, attempted_at) VALUES (?, ?)')->execute([client_ip(), time()]);
}

function admin_setup(string $method): void
{
    if (user_count() > 0) {
        redirect('/admin/login');
    }
    $key = (string) config('setup_key', '');
    $error = null;
    if ($key === '' || $key === 'mude-isto') {
        admin_render('message', ['title' => 'Configuração inicial',
            'message' => 'Defina um "setup_key" próprio em config.php (copie config.sample.php) antes de criar o administrador.']);
        return;
    }
    if ($method === 'POST') {
        $user = trim((string) ($_POST['username'] ?? ''));
        $pass = (string) ($_POST['password'] ?? '');
        if (too_many_attempts()) {
            $error = 'Demasiadas tentativas. Tente de novo dentro de 15 minutos.';
        } elseif (!hash_equals($key, (string) ($_POST['setup_key'] ?? ''))) {
            record_failed_attempt();
            $error = 'Chave de configuração errada.';
        } elseif (!preg_match('/^[A-Za-z0-9_.@-]{3,64}$/', $user)) {
            $error = 'Nome de utilizador inválido (3–64 caracteres, sem espaços).';
        } elseif (mb_strlen($pass) < 10) {
            $error = 'A palavra-passe deve ter pelo menos 10 caracteres.';
        } else {
            db()->prepare('INSERT INTO users (username, password_hash, created_at) VALUES (?, ?, ?)')
                ->execute([$user, password_hash($pass, PASSWORD_DEFAULT), now()]);
            session_regenerate_id(true);
            $_SESSION['uid'] = (int) db()->lastInsertId();
            flash('Administrador criado. Bem-vindo!');
            redirect('/admin');
        }
    }
    admin_render('setup', ['title' => 'Configuração inicial', 'error' => $error, 'bare' => true]);
}

function admin_login(string $method): void
{
    if (current_user_id()) {
        redirect('/admin');
    }
    $error = null;
    if ($method === 'POST') {
        if (too_many_attempts()) {
            $error = 'Demasiadas tentativas. Tente de novo dentro de 15 minutos.';
        } else {
            $st = db()->prepare('SELECT * FROM users WHERE username = ?');
            $st->execute([trim((string) ($_POST['username'] ?? ''))]);
            $user = $st->fetch();
            // Verifica sempre um hash (mesmo sem utilizador) para o tempo de resposta não revelar se o nome existe.
            $hash = $user['password_hash'] ?? '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
            if (password_verify((string) ($_POST['password'] ?? ''), $hash) && $user) {
                db()->prepare('DELETE FROM login_attempts WHERE ip = ?')->execute([client_ip()]);
                if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                    db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
                        ->execute([password_hash((string) $_POST['password'], PASSWORD_DEFAULT), $user['id']]);
                }
                session_regenerate_id(true);
                $_SESSION['uid'] = (int) $user['id'];
                redirect('/admin');
            }
            record_failed_attempt();
            $error = 'Utilizador ou palavra-passe errados.';
        }
    }
    admin_render('login', ['title' => 'Entrar', 'error' => $error, 'bare' => true]);
}

function admin_password(string $method): void
{
    $error = null;
    if ($method === 'POST') {
        $st = db()->prepare('SELECT * FROM users WHERE id = ?');
        $st->execute([current_user_id()]);
        $user = $st->fetch();
        $new = (string) ($_POST['new'] ?? '');
        if (!password_verify((string) ($_POST['current'] ?? ''), $user['password_hash'])) {
            $error = 'A palavra-passe atual está errada.';
        } elseif (mb_strlen($new) < 10) {
            $error = 'A nova palavra-passe deve ter pelo menos 10 caracteres.';
        } elseif ($new !== ($_POST['confirm'] ?? '')) {
            $error = 'As palavras-passe não coincidem.';
        } else {
            db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([password_hash($new, PASSWORD_DEFAULT), $user['id']]);
            session_regenerate_id(true);
            flash('Palavra-passe alterada.');
            redirect('/admin');
        }
    }
    admin_render('password', ['title' => 'Palavra-passe', 'error' => $error]);
}

/* ---------- Painel ---------- */

function admin_dashboard(): void
{
    $counts = [];
    foreach (collections() as $name => $col) {
        $counts[$name] = (int) db()->query('SELECT COUNT(*) FROM ' . col_table($name))->fetchColumn();
    }
    admin_render('dashboard', [
        'title' => 'Painel',
        'counts' => $counts,
        'unread' => unread_messages(),
        'recent' => db()->query('SELECT * FROM messages ORDER BY id DESC LIMIT 5')->fetchAll(),
    ]);
}
