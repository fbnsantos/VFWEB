<?php
declare(strict_types=1);

/*
 * Campos dos formulários do backoffice (textos e coleções).
 * Valores em v[chave]; ficheiros enviados em files[chave].
 */

/** Ficheiro enviado para o campo $key, ou null se nenhum foi escolhido. */
function uploaded_file(string $key): ?array
{
    $f = $_FILES['files'] ?? null;
    if (!$f || !isset($f['name'][$key]) || !is_string($f['name'][$key])) {
        return null;
    }
    if (($f['error'][$key] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    return [
        'name' => $f['name'][$key],
        'type' => $f['type'][$key] ?? '',
        'tmp_name' => $f['tmp_name'][$key] ?? '',
        'error' => $f['error'][$key],
        'size' => $f['size'][$key] ?? 0,
    ];
}

function posted_value(string $key): string
{
    $v = $_POST['v'][$key] ?? '';
    return is_string($v) ? $v : '';
}

/** HTML de um campo de formulário. */
function render_field(string $key, array $spec, string $value): string
{
    $type = $spec['type'];
    $label = e($spec['label'] ?? $key);
    $name = 'v[' . e($key) . ']';
    $hint = !empty($spec['hint']) ? '<p class="hint">' . e($spec['hint']) . '</p>' : '';
    $req = !empty($spec['required']) ? ' required' : '';
    $html = '';

    switch ($type) {
        case 'note':
            return '<p class="hint">' . $label . '</p>';

        case 'textarea':
        case 'lines':
            $rows = $type === 'lines' ? 6 : 3;
            $html = "<label>$label <textarea name=\"$name\" rows=\"$rows\"$req>" . e($value) . '</textarea></label>';
            break;

        case 'select':
            $html = "<label>$label <select name=\"$name\">";
            foreach ($spec['options'] as $val => $text) {
                $sel = (string) $val === $value ? ' selected' : '';
                $html .= '<option value="' . e((string) $val) . '"' . $sel . '>' . e($text) . '</option>';
            }
            $html .= '</select></label>';
            break;

        case 'check':
            $on = $value === '1' ? ' checked' : '';
            $html = "<label class=\"check\"><input type=\"checkbox\" name=\"$name\" value=\"1\"$on> $label</label>";
            break;

        case 'image':
        case 'video':
            $isVideo = $type === 'video';
            $preview = '';
            if ($value !== '') {
                $src = e(asset_url($value));
                $preview = $isVideo
                    ? "<video src=\"$src\" muted preload=\"metadata\" controls></video>"
                    : "<img src=\"$src\" alt=\"\">";
            }
            $accept = $isVideo ? 'video/mp4,video/webm' : 'image/*';
            $html = "<div class=\"filefield\" data-kind=\"$type\"><span class=\"lbl\">$label</span>"
                . "<div class=\"filepreview\">$preview</div>"
                . "<div class=\"filerow\"><input name=\"$name\" value=\"" . e($value) . "\" placeholder=\"/" . ($isVideo ? 'videos' : 'images') . "/ficheiro\">"
                . '<button type="button" class="btn small" data-pick>Biblioteca</button></div>'
                . '<input type="file" name="files[' . e($key) . "]\" accept=\"$accept\">"
                . '<p class="hint">Escolha da biblioteca, envie um ficheiro novo (substitui o caminho acima) ou escreva um caminho/URL.</p></div>';
            break;

        case 'icon':
            $html = "<div class=\"iconfield\"><span class=\"lbl\">$label</span><div class=\"iconrow\">"
                . '<svg class="iconprev" viewBox="0 0 24 24" fill="none" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $value . '</svg>'
                . '<select data-icon-preset><option value="">Escolher um ícone…</option>';
            foreach (icon_presets() as $presetName => $svg) {
                $html .= '<option value="' . e($svg) . '">' . e($presetName) . '</option>';
            }
            $html .= '</select></div>'
                . "<textarea name=\"$name\" rows=\"3\" data-icon-code spellcheck=\"false\">" . e($value) . '</textarea>'
                . '<p class="hint">Avançado: SVG 24×24 (só formas simples: path, circle, rect, line…). Normalmente basta escolher um ícone da lista.</p></div>';
            break;

        default: // text, url, date
            $inputType = in_array($type, ['url', 'date'], true) ? $type : 'text';
            if ($type === 'url') {
                $inputType = 'text'; // aceita também "#", "/pagina" e "mailto:"
            }
            $html = "<label>$label <input type=\"$inputType\" name=\"$name\" value=\"" . e($value) . "\"$req></label>";
    }
    return $html . $hint;
}

/**
 * Valor a gravar para um campo, a partir do POST/FILES. Valida e limpa conforme o tipo.
 * Lança RuntimeException se um ficheiro enviado for inválido.
 */
function collect_field(string $key, array $spec): string|int
{
    $type = $spec['type'];
    $raw = posted_value($key);

    switch ($type) {
        case 'check':
            return isset($_POST['v'][$key]) ? 1 : 0;

        case 'image':
        case 'video':
            $file = uploaded_file($key);
            if ($file) {
                $res = handle_upload($file);
                if (!str_starts_with($res['mime'], $type . '/')) {
                    throw new RuntimeException(($spec['label'] ?? $key) . ': o ficheiro tem de ser ' . ($type === 'video' ? 'um vídeo (MP4 ou WebM)' : 'uma imagem') . '.');
                }
                return $res['url'];
            }
            return clean_path(trim($raw));

        case 'icon':
            return sanitize_svg($raw);

        case 'url':
            return clean_link(trim($raw));

        case 'date':
            $raw = trim($raw);
            return preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw) ? $raw : '';

        case 'select':
            return array_key_exists($raw, $spec['options']) ? $raw : (string) ($spec['default'] ?? array_key_first($spec['options']));

        case 'textarea':
        case 'lines':
            $raw = str_replace(["\r\n", "\r"], "\n", $raw);
            return mb_substr(trim($raw), 0, 5000);

        default:
            return mb_substr(trim(preg_replace('/\s+/u', ' ', $raw) ?? ''), 0, 500);
    }
}

/** Link seguro: http(s), mailto, caminho local ou âncora. Qualquer outra coisa (javascript:…) fica vazia. */
function clean_link(string $url): string
{
    $url = mb_substr($url, 0, 1000);
    if ($url === '' || preg_match('#^(https?://|mailto:|/(?!/)|\#)#i', $url)) {
        return $url;
    }
    // "www.exemplo.com" sem esquema → https://
    return preg_match('#^[a-z0-9.-]+\.[a-z]{2,}(/|$)#i', $url) ? 'https://' . $url : '';
}

/** Caminho de ficheiro: /pasta/ficheiro, ou URL http(s). */
function clean_path(string $path): string
{
    $path = mb_substr($path, 0, 500);
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    $path = '/' . ltrim($path, '/');
    return str_contains($path, '..') || str_contains($path, '//') || preg_match('/[\x00-\x1f<>"\']/', $path) ? '' : $path;
}
