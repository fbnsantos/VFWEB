<?php
// Copie este ficheiro para config.php e ajuste os valores.
return [
    // URL público do site, sem barra final. Usado em canonical, sitemap, robots.txt e Open Graph.
    // Pode ser sobreposto pela variável de ambiente SITE_URL (útil em desenvolvimento).
    'base_url' => 'https://vifield.com',

    // Redireciona (301) http:// e www./outros domínios para o base_url.
    // Desligue se estiver a testar o site noutro domínio.
    'canonical_redirect' => true,

    // Chave pedida em /admin/setup para criar o primeiro utilizador.
    // Troque por um valor longo e aleatório antes de publicar o site.
    'setup_key' => 'mude-isto',

    // Caminho da base de dados SQLite (deve ficar protegida pelo .htaccess).
    'db_path' => __DIR__ . '/data/site.sqlite',

    'timezone' => 'Europe/Lisbon',

    // Tamanho máximo de cada upload, em MB (os vídeos dos setores são grandes).
    'max_upload_mb' => 50,

    // Deploy automático (git.php). Vazio = desativado.
    // Gere um valor aleatório longo, por exemplo: php -r 'echo bin2hex(random_bytes(24));'
    'deploy_secret' => '',
    'deploy_branch' => 'main',
    // Pasta pessoal no servidor, se o git se queixar de HOME (ex.: '/home2/utilizador'). Normalmente vazio.
    'deploy_home' => '',
];
