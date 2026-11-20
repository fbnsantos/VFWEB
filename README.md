# vifield.com: site + backoffice

Site da ViField em **PHP + SQLite** (sem frameworks nem Composer), com backoffice para gerir o conteúdo e `git.php` para deploy automático. Mesma estrutura do `fbnsantosSI`. O aspeto é o do template original (`css/`, `js/` e `images/` não mudaram).

Requisitos: PHP 8.1+ com `pdo_sqlite`, `mbstring`, `fileinfo`, `dom` (e `gd`, opcional, para reduzir imagens grandes).

## Estrutura

```
index.php          router (páginas, /admin, /contact, robots, sitemap)
git.php            deploy automático (webhook do GitHub ou token)
config.php         configuração e segredos (NÃO está no git; copiar de config.sample.php)
app/               código e vistas (não acessível pela web)
  content.php      MODELO DE CONTEÚDO: textos editáveis, listas, ícones e conteúdo inicial
  views/site/      páginas públicas (home, orioos, news, layout)
  views/admin/     ecrãs do backoffice
css/ js/ images/ videos/   estáticos do template
assets/            CSS/JS do backoffice
uploads/           ficheiros enviados no backoffice (não está no git)
data/              base de dados SQLite e log de deploy (não está no git)
_legacy/           HTML estático original, só para referência (bloqueado pelo .htaccess)
```

## Arrancar localmente

```bash
cp config.sample.php config.php      # mudar setup_key
SITE_URL=http://localhost:8080 php -S localhost:8080 index.php
```

Abrir http://localhost:8080/admin: na primeira vez pede a `setup_key` para criar o administrador. A base de dados é criada e preenchida com o conteúdo do site original.

## O que se gere no backoffice

| Secção | O quê |
|---|---|
| **Textos das páginas** | Geral (nome, logótipos, contactos, rodapé, **menus do topo e do rodapé**), Página inicial, Página Orioos, Página de notícias: títulos, parágrafos, botões, imagens e vídeo. Nos títulos, `*palavra*` aparece destacada a verde. |
| **Listas** | Notícias, Equipa, Tecnologia, Setores, e do Orioos: métricas, capacidades (Agricultura/Vigilância), especificações, sensores, plataforma, requisitos. Criar, editar, esconder («Visível»), apagar e reordenar (↑ ↓). |
| **SEO (Google)** | Secção só de SEO (`/admin/seo`): título e descrição de cada página com contador e pré-visualização do resultado no Google, indexação ligada/desligada, imagem de partilha, códigos do Google Search Console e Bing, e avisos (https, tamanhos, imagens sem texto alternativo). `robots.txt` e `sitemap.xml` são automáticos. |
| **Visitas** | Estatísticas próprias, sem cookies nem serviços externos e sem guardar IPs: visualizações, visitantes únicos, páginas mais vistas, origem (Google, LinkedIn, IA…), países (base DB-IP local, botão no ecrã), dispositivos, robôs e 404. Visitas do admin e robôs não contam. Dados com mais de 13 meses são apagados. |
| **Mensagens** | O formulário de contacto guarda cada mensagem aqui (e envia cópia por email se o servidor tiver `mail()`). Limite de 5 por hora por visitante e campo-isco anti-spam. |
| **Imagens e vídeos** | Biblioteca. Cada campo de imagem/vídeo também aceita envio direto ou «Biblioteca». |
| **Atualizar site (git)** | Botão que faz o mesmo que `git.php`. |

Para acrescentar um campo ou uma lista nova, edite `app/content.php` (e mostre o campo em `app/views/site/*.php`): a base de dados e o backoffice acompanham sozinhos.

## Subsistemas /PK/ e /dss/

`https://vifield.com/PK/login.php` e `https://vifield.com/dss/login.php` continuam a funcionar: são pastas reais do servidor, fora do git. O `.htaccess` serve ficheiros e pastas existentes tal como estão (só o resto passa pelo router) e o `git.php` (`fetch` + `reset --hard`) não toca em pastas que não estão no repositório. Por omissão aparecem como submenus de **Solutions**; para os mudar (ex.: passar para o rodapé) edite **Textos → Geral → Menu do topo** e **Menu do rodapé** (uma linha por item, `Nome | endereço`; `- ` no início = submenu).

## Publicar (HostGator / cPanel) com deploy automático

1. cPanel → **Git Version Control** → clonar o repositório (para a raiz do domínio, ou para uma pasta que seja a *Document Root*). Se o domínio já tem `PK/` e `dss/`, mantenha-os nessa mesma raiz.
2. `cp config.sample.php config.php && chmod 600 config.php`; definir `base_url`, `setup_key` e `deploy_secret` (`php -r 'echo bin2hex(random_bytes(24));'`).
3. Abrir `/admin/setup` e criar o administrador.
4. GitHub → *Settings → Webhooks*: URL `https://vifield.com/git.php`, *application/json*, Secret = `deploy_secret`, evento *push*. Cada `git push` para `main` atualiza o site; também se pode forçar com `https://vifield.com/git.php?token=<deploy_secret>` ou em **Atualizar site** no backoffice. Histórico em `data/deploy.log`.

`git.php` faz `git fetch` + `git reset --hard origin/main`: não edite ficheiros do repositório diretamente no servidor. `config.php`, `data/` e `uploads/` ficam intactos.

## Notas

- Os endereços antigos `/index.html`, `/orioos.html` e `/news.html` redirecionam (301) para `/`, `/orioos` e `/news`.
- O site tem de estar na raiz do domínio (os caminhos são `/css/...`, `/images/...`).
- Desligue **Permitir que o Google indexe** (Textos → Geral) enquanto estiver em testes.
- Pendente do template: o menu em telemóvel (< 640 px) continua sem botão de menu.
- As visitas a `/PK/` e `/dss/` não aparecem em **Visitas** (são aplicações separadas); só se contam as páginas do site (início, Orioos, notícias, 404).
