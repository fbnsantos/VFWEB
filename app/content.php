<?php
declare(strict_types=1);

/*
 * MODELO DE CONTEÚDO DO SITE
 * --------------------------
 * Tudo o que se edita no backoffice está definido aqui:
 *
 *  - text_pages():  textos e imagens soltos de cada página (títulos, parágrafos, botões…).
 *                   Guardam-se na tabela `settings`. Enquanto não forem editados, vale o valor
 *                   por omissão (`default`) escrito aqui, que é o texto original do site.
 *  - collections(): listas repetíveis (notícias, equipa, tecnologia…). Cada coleção é uma tabela
 *                   SQLite criada e atualizada automaticamente a partir desta definição.
 *  - seed_data():   conteúdo inicial das coleções (usado só quando a base de dados é criada).
 *
 * Para acrescentar um campo ou uma coleção basta editar este ficheiro: o backoffice, a base de dados
 * e os formulários acompanham. Falta apenas mostrar o campo na vista (app/views/site/*.php).
 *
 * Tipos de campo: text, textarea, lines (uma linha por item), url, date, select, check,
 *                 image, video, icon.
 */

function tf(string $label, string $default = '', string $type = 'text', string $hint = '', int $count = 0): array
{
    return ['type' => $type, 'label' => $label, 'default' => $default, 'hint' => $hint, 'count' => $count];
}

/** Textos e imagens editáveis, agrupados por página e por secção. */
function text_pages(): array
{
    static $pages = null;
    if ($pages !== null) {
        return $pages;
    }
    $em = 'Use *asteriscos* à volta das palavras que devem aparecer destacadas (a verde, em itálico).';

    return $pages = [
        'site' => [
            'label' => 'Geral (contactos e rodapé)',
            'url' => '/',
            'sections' => [
                'Identidade' => [
                    'site.name' => tf('Nome da marca', 'ViField'),
                    'site.logo' => tf('Logótipo (menu)', '/images/ViField6_original.png', 'image'),
                    'site.logo_white' => tf('Logótipo claro (rodapé)', '/images/ViField6_white.png', 'image'),
                ],
                'Contactos' => [
                    'site.email' => tf('Email de contacto', 'info@vifield.com', 'text', 'Aparece no site (secção Contacto e rodapé).'),
                    'site.notify_email' => tf('Receber mensagens do formulário em', 'info@vifield.com', 'text', 'As mensagens ficam sempre guardadas em Mensagens; este endereço também recebe uma cópia por email.'),
                    'site.address' => tf('Morada', "Rua de Godim, nº 389, 4300-394\nPorto, Portugal", 'textarea'),
                    'site.linkedin' => tf('LinkedIn (URL)', 'https://linkedin.com/company/vifield', 'url', 'Deixe vazio para esconder o ícone.'),
                ],
                'Menu do topo' => [
                    'site.menu_top' => tf('Itens do menu (um por linha: Nome | endereço)', "Technology | #technology\nIndustries | #industries\nSolutions | #solutions\n- Orioos | /orioos\n- PK | /PK/login.php\n- DSS | /dss/login.php\nTeam | #team\nNews | /news", 'lines',
                        'Uma linha por item. Para um submenu, comece a linha com «- » (fica dentro do item de cima). Endereços: #secção (na página inicial), /página, ou https://… . O item com endereço #solutions recebe sozinho as soluções criadas em Soluções; o item /news mostra as últimas notícias. Para passar o PK para o rodapé, apague a linha aqui e acrescente-a no menu do rodapé.'),
                ],
                'Menu do rodapé' => [
                    'site.menu_footer' => tf('Itens do menu (um por linha: Nome | endereço)', "Home | #top\nTechnology | #technology\nIndustries | #industries\nSolutions | #solutions\nTeam | #team\nNews | /news\nContact | #contact", 'lines',
                        'Coluna «Navigation» do rodapé. Mesmo formato do menu do topo (submenus com «- » aparecem como itens normais). Ex.: PK (login) | /PK/login.php'),
                ],
                'Rodapé' => [
                    'site.footer_tagline' => tf('Frase do rodapé', 'Modular robotic systems for continuous monitoring, surveillance, and data collection.', 'textarea'),
                    'site.backed_title' => tf('Título "Backed by"', 'Backed by'),
                    'site.backer1_logo' => tf('Apoio 1 (logótipo)', '/images/inesctec.png', 'image'),
                    'site.backer1_name' => tf('Apoio 1 (nome)', 'INESC TEC'),
                    'site.backer2_logo' => tf('Apoio 2 (logótipo)', '/images/uporto.png', 'image'),
                    'site.backer2_name' => tf('Apoio 2 (nome)', 'Universidade do Porto'),
                    'site.copyright' => tf('Direitos (depois do ano)', 'ViField. All rights reserved.'),
                ],
            ],
        ],

        'seo' => [
            'label' => 'SEO (Google)',
            'url' => '/',
            'sections' => [
                'Indexação e partilha' => [
                    'site.allow_indexing' => tf('Permitir que o Google indexe o site', '1', 'check', 'Desligue enquanto o site estiver em testes: o robots.txt passa a bloquear tudo.'),
                    'site.share_image' => tf('Imagem de partilha (LinkedIn, WhatsApp, redes sociais)', '/images/orioosv2-1.jpeg', 'image', 'Ideal: 1200×630 px.'),
                ],
                'Página inicial' => [
                    'home.meta_title' => tf('Título', 'ViField — Autonomous Robots for Field Operations', 'text', 'Ideal: até 60 caracteres.', 60),
                    'home.meta_description' => tf('Descrição', 'ViField designs and deploys modular robotic systems for continuous monitoring, surveillance, and data collection.', 'textarea', 'Ideal: 120 a 160 caracteres.', 160),
                ],
                'Página Orioos' => [
                    'orioos.meta_title' => tf('Título', 'Orioos v2.0 — Autonomous Field Intelligence Robot | ViField', 'text', 'Ideal: até 60 caracteres.', 60),
                    'orioos.meta_description' => tf('Descrição', "Orioos is ViField’s fully electric, modular autonomous ground robot for crop monitoring, field data collection, and perimeter surveillance in outdoor and mixed-terrain environments.", 'textarea', 'Ideal: 120 a 160 caracteres.', 160),
                ],
                'Página de notícias' => [
                    'news.meta_title' => tf('Título', 'News & Press — ViField', 'text', 'Ideal: até 60 caracteres.', 60),
                    'news.meta_description' => tf('Descrição', 'News, press coverage, and milestones from ViField — autonomous robots for field operations.', 'textarea', 'Ideal: 120 a 160 caracteres.', 160),
                ],
                'Google Search Console e Bing' => [
                    'seo.google_verification' => tf('Código de verificação do Google', '', 'text', 'Cole o código, ou a meta tag inteira que o Search Console mostra (método «Etiqueta HTML»). Depois submeta o sitemap.xml no Search Console.'),
                    'seo.bing_verification' => tf('Código de verificação do Bing', '', 'text', 'Opcional (Bing Webmaster Tools).'),
                ],
            ],
        ],

        'home' => [
            'label' => 'Página inicial',
            'url' => '/',
            'sections' => [
                'Topo (hero)' => [
                    'home.hero_eyebrow' => tf('Etiqueta', 'Robotics for field operations'),
                    'home.hero_title' => tf('Título', 'Autonomous robots that *protect perimeters* and *empower farms*.', 'text', $em),
                    'home.hero_model' => tf('Linha de modelo', 'Field-deployed · Modular · Autonomous'),
                    'home.hero_desc' => tf('Descrição', 'ViField designs and deploys modular robotic systems for continuous monitoring, surveillance, and data collection — built for industrial sites, critical infrastructure, and precision agriculture.', 'textarea'),
                    'home.hero_cta1' => tf('Botão principal', 'Our solutions'),
                    'home.hero_cta2' => tf('Botão secundário', 'Get in touch'),
                ],
                'Tecnologia' => [
                    'home.tech_label' => tf('Etiqueta', 'Technology'),
                    'home.tech_title' => tf('Título', 'Technology that performs in the field.'),
                    'home.tech_desc' => tf('Descrição', 'A modular stack combining perception, autonomy, and edge compute — built for reliable operation in complex environments.', 'textarea', 'Os cartões editam-se em Listas → Tecnologia.'),
                ],
                'Setores' => [
                    'home.ind_label' => tf('Etiqueta', 'Industries'),
                    'home.ind_title' => tf('Título', 'Industries we operate in.'),
                    'home.ind_desc' => tf('Descrição', 'Built for the field. Proven in demanding environments — industrial yards, critical infrastructure, and precision agriculture.', 'textarea', 'Os setores editam-se em Listas → Setores.'),
                    'home.ind_cta1' => tf('Botão de cada setor (principal)', 'Request a demo'),
                    'home.ind_cta2' => tf('Botão de cada setor (secundário)', 'See solutions'),
                ],
                'Soluções (produto em destaque)' => [
                    'home.sol_label' => tf('Etiqueta', 'Solutions'),
                    'home.sol_title' => tf('Título', 'Built for real-world operations.'),
                    'home.sol_desc' => tf('Descrição', 'A growing set of field-ready products and deployments.', 'textarea'),
                    'home.product_image' => tf('Imagem do produto', '/images/orioosv2-1.jpeg', 'image'),
                    'home.product_image_alt' => tf('Texto alternativo da imagem', 'Orioos v2.0 robot'),
                    'home.product_label' => tf('Etiqueta do produto', 'Autonomous robot'),
                    'home.product_name' => tf('Nome do produto', 'Orioos v2.0®'),
                    'home.product_desc' => tf('Descrição do produto', 'ORIOOS is a fully electric, modular, and autonomous ground robot designed for continuous monitoring in outdoor and mixed-terrain environments.', 'textarea'),
                    'home.product_features' => tf('Características (uma por linha)', "Autonomous navigation & patrol routes\nMulti-sensor payload support\n24/7 continuous operation\nEdge AI & real-time alerting", 'lines'),
                    'home.product_btn' => tf('Texto do botão', 'Learn more about Orioos'),
                ],
                'Equipa' => [
                    'home.team_label' => tf('Etiqueta', 'Team'),
                    'home.team_title' => tf('Título', 'Meet the team.'),
                    'home.advisory_label' => tf('Advisory Board: etiqueta', 'Advisory'),
                    'home.advisory_title' => tf('Advisory Board: título', 'Advisory board.'),
                    'home.advisory_desc' => tf('Advisory Board: descrição', '', 'textarea', 'Opcional. A secção só aparece se houver pessoas no Advisory Board (Listas → Equipa).'),
                    'home.team_desc' => tf('Descrição', 'A multidisciplinary group bringing together robotics, hardware, software, electrical engineering, and agronomic expertise.', 'textarea', 'Os membros editam-se em Listas → Equipa.'),
                    'home.origin_label' => tf('Origem: etiqueta', 'Research origins'),
                    'home.origin_text' => tf('Origem: texto', 'ViField is a spin-off born from research conducted at INESC TEC and the University of Porto. Built on years of academic excellence, we bring advanced robotics from the lab into real-world field operations.', 'textarea'),
                ],
                'Contacto' => [
                    'home.contact_label' => tf('Etiqueta', 'Contact'),
                    'home.contact_title' => tf('Título', 'Ready to automate?'),
                    'home.contact_desc' => tf('Descrição', 'Tell us about your challenge. Our team will design a solution tailored to your operations.', 'textarea'),
                    'home.form_title' => tf('Formulário: título', 'Send us a message'),
                    'home.form_subtitle' => tf('Formulário: subtítulo', "We'll get back to you within 24h"),
                    'home.form_note' => tf('Formulário: nota (antes do email)', 'Or email us directly at'),
                    'home.form_ok' => tf('Mensagem após envio', 'Thank you! Your message has been sent. We will get back to you soon.'),
                ],
            ],
        ],

        'orioos' => [
            'label' => 'Página Orioos',
            'url' => '/orioos',
            'sections' => [
                'Topo (hero)' => [
                    'orioos.hero_eyebrow' => tf('Etiqueta', 'Autonomous field intelligence'),
                    'orioos.hero_title' => tf('Título', 'Orioos *v2.0®*', 'text', $em),
                    'orioos.hero_subtitle' => tf('Subtítulo', 'A fully electric, modular autonomous ground robot for perimeter surveillance and crop monitoring across outdoor and mixed-terrain environments.', 'textarea'),
                    'orioos.hero_cta1' => tf('Botão principal', 'Request a demo'),
                    'orioos.hero_cta2' => tf('Botão secundário', 'View specifications'),
                    'orioos.hero_image' => tf('Imagem', '/images/orioosv2-1.jpeg', 'image'),
                    'orioos.hero_image_alt' => tf('Texto alternativo da imagem', 'Orioos v2.0 autonomous robot'),
                ],
                'Métricas' => [
                    'orioos.metrics_hint' => tf('(as métricas editam-se em Listas → Orioos: métricas)', '', 'note'),
                ],
                'Vídeo' => [
                    'orioos.video_label' => tf('Etiqueta', 'In action'),
                    'orioos.video_title' => tf('Título', 'See Orioos in the field.'),
                    'orioos.video_desc' => tf('Descrição', 'A short silent view of the platform’s movement, form factor, and field-ready design.', 'textarea'),
                    'orioos.video' => tf('Vídeo (MP4)', '/videos/orioos_360.mp4', 'video'),
                ],
                'Visão geral' => [
                    'orioos.overview_label' => tf('Etiqueta', 'Overview'),
                    'orioos.overview_title' => tf('Título', 'One autonomous platform for security patrols and precision agriculture.'),
                    'orioos.overview_text' => tf('Texto (um parágrafo por linha)', "Orioos performs autonomous missions along configurable routes, combining reliable field mobility with perception, edge processing, and cloud connectivity.\nIn surveillance scenarios, it monitors perimeters, detects intrusions, and reports operational events. In agricultural scenarios, the same platform can support crop monitoring, fruit counting for yield assessment, disease symptom detection, and field data collection.", 'lines'),
                ],
                'Capacidades' => [
                    'orioos.cap_label' => tf('Etiqueta', 'Capabilities'),
                    'orioos.cap_title' => tf('Título', 'Switch between agriculture and surveillance missions.'),
                    'orioos.cap_desc' => tf('Descrição', 'Orioos uses the same autonomous field platform for two high-value operating modes: precision agriculture and perimeter security.', 'textarea', 'Os cartões editam-se em Listas → Orioos: capacidades.'),
                    'orioos.cap_tab_agriculture' => tf('Separador "Agricultura"', 'Agriculture'),
                    'orioos.cap_tab_surveillance' => tf('Separador "Vigilância"', 'Surveillance'),
                    'orioos.cap_note_agriculture' => tf('Nota do modo Agricultura', 'Agriculture mode is designed for permanent crops, such as vineyards or orchards, and open-field operations where regular autonomous observation can support better crop decisions.', 'textarea'),
                    'orioos.cap_note_surveillance' => tf('Nota do modo Vigilância', 'Surveillance mode is designed for logistics sites, industrial yards, critical infrastructure, and large perimeters where continuous mobile monitoring can complement fixed cameras and human patrols.', 'textarea'),
                ],
                'Especificações' => [
                    'orioos.spec_label' => tf('Etiqueta', 'Technical specifications'),
                    'orioos.spec_title' => tf('Título', 'A compact electric platform for field deployment.'),
                    'orioos.spec_desc' => tf('Descrição', 'Orioos is built around a simplified modular frame, electric propulsion, RTK-assisted positioning, 360° sensing, and cloud connectivity.', 'textarea', 'A tabela edita-se em Listas → Orioos: especificações.'),
                ],
                'Sensores' => [
                    'orioos.sensors_label' => tf('Etiqueta', 'Sensors & connectivity'),
                    'orioos.sensors_title' => tf('Título', 'Perception and connectivity for outdoor monitoring.'),
                    'orioos.sensors_desc' => tf('Descrição', 'The sensing architecture supports wide-area visibility, obstacle detection, route following, and event capture in connected perimeter deployments.', 'textarea', 'Os cartões editam-se em Listas → Orioos: sensores.'),
                ],
                'Plataforma de software' => [
                    'orioos.platform_label' => tf('Etiqueta', 'Software & data platform'),
                    'orioos.platform_title' => tf('Título', 'From robot events to actionable security data.'),
                    'orioos.platform_desc' => tf('Descrição', 'Orioos is connected to the ViField cloud platform for remote operation, live monitoring, event review, and controlled data export.', 'textarea', 'Os cartões editam-se em Listas → Orioos: plataforma.'),
                ],
                'Privacidade' => [
                    'orioos.privacy_label' => tf('Etiqueta', 'Privacy-aware operation'),
                    'orioos.privacy_title' => tf('Título', 'Designed for compliant perimeter deployments.'),
                    'orioos.privacy_desc' => tf('Descrição', 'Orioos supports security monitoring workflows where access control, event-based recording, retention policies, and data location matter.', 'textarea'),
                    'orioos.privacy_text' => tf('Texto (um parágrafo por linha)', "Event-triggered recording can be used so video is captured when an intrusion or relevant movement event is detected, rather than recording continuously during normal patrols.\nData can be managed with role-based access, authenticated exports, audit logs, configurable retention periods, and deployment-specific privacy procedures.\nThe system does not require facial recognition or biometric identification for perimeter detection use cases.", 'lines'),
                ],
                'Instalação' => [
                    'orioos.deploy_label' => tf('Etiqueta', 'Deployment requirements'),
                    'orioos.deploy_title' => tf('Título', 'Prepared for quick site onboarding.'),
                    'orioos.deploy_desc' => tf('Descrição', 'A typical deployment starts with a mapped patrol perimeter, route validation, connectivity checks, and ViField onboarding support.', 'textarea', 'Os requisitos editam-se em Listas → Orioos: requisitos.'),
                ],
                'Chamada final' => [
                    'orioos.cta_title' => tf('Título', 'Ready to evaluate Orioos?'),
                    'orioos.cta_text' => tf('Texto', 'Tell us about your site, perimeter length, terrain, connectivity, and operational goals. We’ll help define the right deployment model.', 'textarea'),
                    'orioos.cta_btn' => tf('Botão (abre o email)', 'Request a demo'),
                ],
            ],
        ],

        'news' => [
            'label' => 'Página de notícias',
            'url' => '/news',
            'sections' => [
                'Topo (hero)' => [
                    'news.hero_eyebrow' => tf('Etiqueta', 'Newsroom'),
                    'news.hero_title' => tf('Título', 'News, press & *milestones*.', 'text', $em),
                    'news.hero_lead' => tf('Texto', 'Coverage, announcements, and deployments from ViField — as we bring autonomous robots into real-world operations.', 'textarea'),
                ],
                'Listas' => [
                    'news.featured_label' => tf('Etiqueta do destaque', 'Featured'),
                    'news.all_label' => tf('Etiqueta da lista', 'All updates'),
                    'news.all_title' => tf('Título da lista', 'Everything from the newsroom.'),
                    'news.empty' => tf('Mensagem de filtro vazio', 'No items in this category yet.'),
                    'news.default_image' => tf('Imagem por omissão das notícias', '/images/orioosv2-1.jpeg', 'image', 'Usada nas notícias que não têm imagem própria, para o cartão nunca ficar só com texto.'),
                    'news.items_hint' => tf('(as notícias editam-se em Listas → Notícias)', '', 'note'),
                ],
            ],
        ],
    ];
}

/** Valor por omissão de um texto, ou null se a chave não existir. */
function text_default(string $key): ?string
{
    static $index = null;
    if ($index === null) {
        $index = [];
        foreach (text_pages() as $page) {
            foreach ($page['sections'] as $fields) {
                foreach ($fields as $k => $spec) {
                    $index[$k] = $spec['default'];
                }
            }
        }
    }
    return $index[$key] ?? null;
}

/* ---------- Ícones (SVG simples, 24×24, traço) ---------- */

function icon_presets(): array
{
    return [
        'Módulos' => '<path d="M9 4.5C7.343 4.5 6 5.843 6 7.5v9c0 1.657 1.343 3 3 3"/><path d="M15 4.5c1.657 0 3 1.343 3 3v9c0 1.657-1.343 3-3 3"/><path d="M9 7h6M9 11h6M9 15h6"/>',
        'Olho (visão)' => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
        'Chip' => '<path d="M9 3v2M15 3v2M9 19v2M15 19v2M3 9h2M3 15h2M19 9h2M19 15h2"/><rect x="7" y="7" width="10" height="10" rx="1"/><rect x="10" y="10" width="4" height="4"/>',
        'Raio' => '<path d="M13 2 3 14h7l-1 8 12-14h-7l-1-6Z"/>',
        'Escudo' => '<path d="M12 2 20 6v7c0 5-3.5 9.4-8 9.4S4 18 4 13V6l8-4Z"/>',
        'Indicador' => '<path d="M12 20a8 8 0 1 1 8-8"/><path d="M12 12l5-3"/><path d="M4 20h16"/>',
        'Planta' => '<path d="M12 22c4-4 8-8 8-13a8 8 0 0 0-16 0c0 5 4 9 8 13Z"/><path d="M9 9c2.5 0 4.5 2 4.5 4.5"/>',
        'Fruta' => '<circle cx="8" cy="12" r="3"/><circle cx="16" cy="12" r="3"/><path d="M8 9V6m8 3V6m-4 9v3"/>',
        'Gota' => '<path d="M12 2c4 4 7 8 7 13a7 7 0 0 1-14 0c0-5 3-9 7-13Z"/><path d="M12 8v8m-4-4h8"/>',
        'Fenotipagem' => '<path d="M4 19c4-8 12-8 16 0"/><path d="M6 15c3-5 9-5 12 0"/><path d="M12 3v18"/>',
        'Gráfico' => '<path d="M5 19V5"/><path d="M5 19h14"/><path d="M9 15l3-4 3 2 4-7"/>',
        'Rota' => '<path d="M3 11l19-8-8 19-2-8-9-3Z"/>',
        'Vídeo' => '<path d="M15 10 20 7v10l-5-3"/><rect x="4" y="6" width="11" height="12" rx="2"/>',
        'Terreno' => '<path d="M12 2v20M2 12h20"/><path d="M5 5c4 2 10 2 14 0M5 19c4-2 10-2 14 0"/>',
        'Cruz' => '<path d="M12 5v14M5 12h14"/>',
        'Círculo' => '<circle cx="12" cy="12" r="8"/>',
    ];
}

function icon_preset(string $name): string
{
    return icon_presets()[$name] ?? icon_presets()['Círculo'];
}

/**
 * Limpa o SVG escrito no backoffice: só formas simples e atributos de desenho
 * (sem scripts, eventos, links ou estilos). Devolve '' se o SVG for inválido.
 */
function sanitize_svg(string $inner): string
{
    $inner = trim($inner);
    if ($inner === '') {
        return '';
    }
    $allowedTags = ['path', 'circle', 'rect', 'line', 'polyline', 'polygon', 'ellipse', 'g'];
    $allowedAttrs = ['d', 'cx', 'cy', 'r', 'rx', 'ry', 'x', 'y', 'x1', 'y1', 'x2', 'y2', 'width', 'height', 'points',
        'transform', 'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'opacity'];

    $doc = new DOMDocument();
    $prev = libxml_use_internal_errors(true);
    $ok = $doc->loadXML('<svg xmlns="http://www.w3.org/2000/svg">' . $inner . '</svg>', LIBXML_NONET);
    libxml_clear_errors();
    libxml_use_internal_errors($prev);
    if (!$ok || !$doc->documentElement) {
        return '';
    }

    $clean = function (DOMNode $node) use (&$clean, $allowedTags, $allowedAttrs): void {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMElement) {
                if (!in_array($child->localName, $allowedTags, true)) {
                    $node->removeChild($child);
                    continue;
                }
                foreach (iterator_to_array($child->attributes) as $attr) {
                    $value = $attr->value;
                    if (!in_array($attr->name, $allowedAttrs, true) || preg_match('/(javascript|url\s*\(|expression|data:)/i', $value)) {
                        $child->removeAttribute($attr->name);
                    }
                }
                $clean($child);
            } elseif (!($child instanceof DOMText) || trim($child->textContent) !== '') {
                $node->removeChild($child); // comentários, CDATA, instruções, texto solto
            }
        }
    };
    $clean($doc->documentElement);

    $out = '';
    foreach ($doc->documentElement->childNodes as $child) {
        $out .= $doc->saveXML($child);
    }
    // Remove o namespace que o saveXML repete em cada elemento.
    return trim(str_replace(' xmlns="http://www.w3.org/2000/svg"', '', $out));
}

/* ---------- Coleções (listas repetíveis) ---------- */

function collections(): array
{
    static $c = null;
    if ($c !== null) {
        return $c;
    }
    return $c = [
        'news' => [
            'label' => 'Notícias',
            'singular' => 'notícia',
            'url' => '/news',
            'order' => 'date DESC, id DESC',
            'list' => ['title' => 'Título', 'category' => 'Categoria', 'date' => 'Data'],
            'hint' => 'Ordenadas por data (a mais recente primeiro). Marque uma como "destaque" para aparecer em grande no topo.',
            'fields' => [
                'title' => ['type' => 'text', 'label' => 'Título', 'required' => true],
                'category' => ['type' => 'select', 'label' => 'Categoria', 'default' => 'press',
                    'options' => ['press' => 'Press', 'announcement' => 'Announcement', 'event' => 'Event']],
                'date' => ['type' => 'date', 'label' => 'Data', 'required' => true],
                'excerpt' => ['type' => 'textarea', 'label' => 'Resumo (uma ou duas frases)'],
                'source' => ['type' => 'text', 'label' => 'Fonte (jornal, site, evento)'],
                'url' => ['type' => 'url', 'label' => 'Link da notícia', 'hint' => 'Deixe vazio se ainda não houver link.'],
                'featured' => ['type' => 'check', 'label' => 'Notícia em destaque (só uma; substitui a anterior)'],
                'image' => ['type' => 'image', 'label' => 'Imagem', 'hint' => 'Recomendado: as notícias com imagem são lidas muito mais. Sem imagem usa-se a imagem por omissão (Textos → Notícias).'],
                'image_alt' => ['type' => 'text', 'label' => 'Texto alternativo da imagem'],
                'badge' => ['type' => 'text', 'label' => 'Etiqueta sobre a imagem (só destaque)'],
            ],
        ],
        'solutions' => [
            'label' => 'Soluções',
            'singular' => 'solução',
            'url' => '/#solutions',
            'sortable' => true,
            'slug' => 'name',
            'hint' => 'Cada solução tem a sua página (/solutions/nome), igual à do Orioos, e aparece sozinha no submenu «Solutions» do topo. Os blocos sem conteúdo não aparecem.',
            'list' => ['name' => 'Nome', 'slug' => 'Endereço'],
            'fields' => [
                'name' => ['type' => 'text', 'label' => 'Nome (menu e separador do browser)', 'required' => true],
                'slug' => ['type' => 'text', 'label' => 'Endereço', 'hint' => 'Só letras, números e hífens. Vazio = gerado a partir do nome. A página fica em /solutions/endereço.'],
                'eyebrow' => ['type' => 'text', 'label' => 'Etiqueta por cima do título'],
                'title' => ['type' => 'text', 'label' => 'Título principal', 'required' => true, 'hint' => 'Use *palavra* para destacar a verde.'],
                'subtitle' => ['type' => 'textarea', 'label' => 'Texto de abertura'],
                'cta1' => ['type' => 'text', 'label' => 'Botão principal (envia email)', 'hint' => 'Vazio = «Request a demo».'],
                'image' => ['type' => 'image', 'label' => 'Imagem de topo'],
                'image_alt' => ['type' => 'text', 'label' => 'Texto alternativo da imagem'],
                'video' => ['type' => 'video', 'label' => 'Vídeo (opcional)'],
                'metrics' => ['type' => 'lines', 'label' => 'Métricas (uma por linha: Valor | Legenda)', 'hint' => 'Ex.: 24/7 | Operação contínua'],
                'overview_title' => ['type' => 'text', 'label' => 'Apresentação: título'],
                'overview_text' => ['type' => 'lines', 'label' => 'Apresentação: texto (um parágrafo por linha)'],
                'features_title' => ['type' => 'text', 'label' => 'Funcionalidades: título'],
                'features' => ['type' => 'lines', 'label' => 'Funcionalidades (uma por linha: Título | Texto)'],
                'specs_title' => ['type' => 'text', 'label' => 'Especificações: título'],
                'specs' => ['type' => 'lines', 'label' => 'Especificações (uma por linha: Nome | Valor)'],
                'details_title' => ['type' => 'text', 'label' => 'Faixa escura: título'],
                'details' => ['type' => 'lines', 'label' => 'Faixa escura (uma por linha: Título | Texto)'],
                'cta_title' => ['type' => 'text', 'label' => 'Fecho: título'],
                'cta_text' => ['type' => 'textarea', 'label' => 'Fecho: texto'],
                'cta_btn' => ['type' => 'text', 'label' => 'Fecho: botão'],
                'meta_description' => ['type' => 'textarea', 'label' => 'Descrição para o Google (até 160 caracteres)'],
            ],
        ],
        'team' => [
            'label' => 'Equipa',
            'singular' => 'membro',
            'url' => '/#team',
            'sortable' => true,
            'group' => 'section',
            'order' => 'section DESC, sort, id',
            'hint' => 'A equipa e o Advisory Board editam-se aqui: escolha em «Secção» onde a pessoa aparece. A ordem (↑ ↓) é dentro de cada secção.',
            'list' => ['name' => 'Nome', 'role' => 'Posição', 'section' => 'Secção'],
            'fields' => [
                'section' => ['type' => 'select', 'label' => 'Secção', 'default' => 'team',
                    'options' => ['team' => 'Equipa', 'advisory' => 'Advisory Board']],
                'name' => ['type' => 'text', 'label' => 'Nome', 'required' => true],
                'role' => ['type' => 'text', 'label' => 'Posição (ex.: CEO, CTO)'],
                'photo' => ['type' => 'image', 'label' => 'Fotografia'],
                'url' => ['type' => 'url', 'label' => 'Link ao clicar na fotografia', 'hint' => 'LinkedIn, página pessoal… Deixe vazio para a fotografia não ser clicável.'],
                'bio' => ['type' => 'textarea', 'label' => 'Biografia'],
            ],
        ],
        'tech' => [
            'label' => 'Tecnologia',
            'singular' => 'cartão',
            'url' => '/#technology',
            'sortable' => true,
            'list' => ['title' => 'Título', 'body' => 'Texto'],
            'fields' => [
                'title' => ['type' => 'text', 'label' => 'Título', 'required' => true],
                'icon' => ['type' => 'icon', 'label' => 'Ícone'],
                'body' => ['type' => 'textarea', 'label' => 'Texto'],
            ],
        ],
        'sectors' => [
            'label' => 'Setores',
            'singular' => 'setor',
            'url' => '/#industries',
            'sortable' => true,
            'hint' => 'Mostrados alternadamente (vídeo à esquerda, vídeo à direita).',
            'list' => ['title' => 'Título', 'label' => 'Etiqueta'],
            'fields' => [
                'label' => ['type' => 'text', 'label' => 'Etiqueta (pequena, por cima do título)', 'required' => true],
                'title' => ['type' => 'text', 'label' => 'Título', 'required' => true],
                'body' => ['type' => 'textarea', 'label' => 'Texto'],
                'pills' => ['type' => 'lines', 'label' => 'Etiquetas (uma por linha)'],
                'image' => ['type' => 'image', 'label' => 'Imagem', 'hint' => 'Sozinha, é mostrada como foto. Com vídeo, aparece logo enquanto o vídeo carrega (capa).'],
                'video' => ['type' => 'video', 'label' => 'Vídeo (opcional; MP4 curto e comprimido, em loop e sem som)'],
            ],
        ],
        'metrics' => [
            'label' => 'Orioos: métricas',
            'singular' => 'métrica',
            'url' => '/orioos',
            'sortable' => true,
            'list' => ['value' => 'Valor', 'label' => 'Legenda'],
            'fields' => [
                'value' => ['type' => 'text', 'label' => 'Valor (ex.: 24/7, 8h, <1s)', 'required' => true],
                'label' => ['type' => 'text', 'label' => 'Legenda', 'required' => true],
            ],
        ],
        'caps' => [
            'label' => 'Orioos: capacidades',
            'singular' => 'capacidade',
            'url' => '/orioos',
            'sortable' => true,
            'group' => 'mode',
            'list' => ['title' => 'Título', 'mode' => 'Modo'],
            'fields' => [
                'mode' => ['type' => 'select', 'label' => 'Modo', 'default' => 'agriculture',
                    'options' => ['agriculture' => 'Agricultura', 'surveillance' => 'Vigilância']],
                'title' => ['type' => 'text', 'label' => 'Título', 'required' => true],
                'icon' => ['type' => 'icon', 'label' => 'Ícone'],
                'body' => ['type' => 'textarea', 'label' => 'Texto'],
            ],
        ],
        'specs' => [
            'label' => 'Orioos: especificações',
            'singular' => 'especificação',
            'url' => '/orioos#specifications',
            'sortable' => true,
            'list' => ['name' => 'Nome', 'value' => 'Valor'],
            'fields' => [
                'name' => ['type' => 'text', 'label' => 'Nome (ex.: Battery)', 'required' => true],
                'value' => ['type' => 'text', 'label' => 'Valor (ex.: Li-Ion, 1500 Wh)', 'required' => true],
            ],
        ],
        'sensors' => [
            'label' => 'Orioos: sensores',
            'singular' => 'cartão',
            'url' => '/orioos',
            'sortable' => true,
            'list' => ['title' => 'Título', 'body' => 'Texto'],
            'fields' => [
                'title' => ['type' => 'text', 'label' => 'Título', 'required' => true],
                'body' => ['type' => 'textarea', 'label' => 'Texto'],
            ],
        ],
        'platform' => [
            'label' => 'Orioos: plataforma',
            'singular' => 'cartão',
            'url' => '/orioos',
            'sortable' => true,
            'list' => ['title' => 'Título', 'body' => 'Texto'],
            'fields' => [
                'title' => ['type' => 'text', 'label' => 'Título', 'required' => true],
                'body' => ['type' => 'textarea', 'label' => 'Texto'],
            ],
        ],
        'reqs' => [
            'label' => 'Orioos: requisitos',
            'singular' => 'grupo de requisitos',
            'url' => '/orioos',
            'sortable' => true,
            'list' => ['title' => 'Título'],
            'fields' => [
                'title' => ['type' => 'text', 'label' => 'Título do grupo', 'required' => true],
                'items' => ['type' => 'lines', 'label' => 'Requisitos (um por linha)'],
            ],
        ],
    ];
}

/** Conteúdo inicial das coleções: o que estava no site original. */
function seed_data(): array
{
    $i = 'icon_preset';
    return [
        'news' => [
            ['title' => 'A new INESC TEC spin-off is bringing intelligent robotics to agriculture and industry', 'category' => 'press', 'date' => '2026-07-23',
                'excerpt' => 'ViField is a new Portuguese spin-off focused on bringing to market a robotic platform developed at INESC TEC: ORIOOS.',
                'source' => 'INESC TEC', 'url' => 'https://www.inesctec.pt/en/news/a-new-inesc-tec-spin-off-is-bringing-intelligent-robotics-to-agriculture-and-industry',
                'featured' => 1, 'badge' => 'INESC TEC', 'image' => '/images/team_news_feature.jpg'],
            ['title' => 'New robot uses AI to ‘read’ entire orchards', 'category' => 'press', 'date' => '2026-07-24',
                'excerpt' => 'Developed by Porto-based ViField, Orioos uses cameras and algorithms to predict yields and detect diseases more accurately.',
                'source' => 'Fruitnet', 'url' => 'https://www.fruitnet.com/eurofruit/new-robot-uses-ai-to-read-entire-orchards/272225.article'],
            ['title' => 'INESC TEC spin-off brings intelligent robotics to agriculture and industry', 'category' => 'press', 'date' => '2026-07-23',
                'excerpt' => 'As ViField\'s first commercial product, ORIOOS will provide high-value data to farmers and security professionals, making decision-making faster and more informed.',
                'source' => 'Science|Business', 'url' => 'https://sciencebusiness.net/network-updates/inesc-tec-spin-brings-intelligent-robotics-agriculture-and-industry'],
            ['title' => 'Autonomous AI Robot Helps Farmers Detect Crop Diseases Before Harvest', 'category' => 'press', 'date' => '2026-07-24',
                'excerpt' => 'An electric, modular robot equipped with sensors, capable of moving non-invasively through orchards and vineyards and detecting crop diseases, is now available to farmers and agricultural producers.',
                'source' => 'AZO Robotics', 'url' => 'https://www.azorobotics.com/News.aspx?newsID=16441'],
            ['title' => 'Autonomous AI robot helps farmers', 'category' => 'press', 'date' => '2026-07-29',
                'excerpt' => 'An autonomous robot equipped with cameras and trained with data collected over four years can move autonomously through fields without compacting the soil and provide crucial information.',
                'source' => 'Smart Machines & Factories', 'url' => 'https://smartmachinesandfactories.co.uk/autonomous-ai-robot-helps-farmers/'],
            ['title' => 'INESC TEC spin-off ViField brings intelligent robotics to agriculture', 'category' => 'press', 'date' => '2026-07-29',
                'excerpt' => 'ViField is a new Portuguese spin-off focused on bringing to market a robotic platform developed at INESC TEC: ORIOOS.',
                'source' => 'Agro Pages', 'url' => 'https://news.agropages.com/News/NewsDetail---58406.htm'],
        ],
        'team' => [
            ['name' => 'André Aguiar', 'photo' => '/images/aa.png', 'bio' => 'PhD in Electrical Engineering from UTAD. Expertise in Robotics Navigation, SLAM, and Computer Vision. Participated in +15 National and European projects in agricultural robotics.'],
            ['name' => 'Filipe Santos', 'photo' => '/images/fs.png', 'bio' => 'PhD in Electrical and Computer Engineering from FEUP. Manager of TRIBE LAB@INESCTEC. Assistant Professor at FEUP. +20 years of experience in R&D in robotics.'],
            ['name' => 'Domingos Bento', 'photo' => '/images/db.png', 'bio' => 'M.Sc. in Electrical and Computer Engineering from IST. Expertise in Embedded Systems and Mechatronics. Background in startups and technology ventures.'],
            ['name' => 'Germano Moreira', 'photo' => '/images/gm.png', 'bio' => 'PhD candidate in Agricultural Sciences, FCUP. Expertise in Computer Vision, Crop Monitoring and Phenotyping. Former Invited Auxiliary Professor at FCUP.'],
            ['name' => 'André Baltazar', 'photo' => '/images/ab.png', 'bio' => 'PhD in Electrical Engineering from UTAD. Expertise in Mechatronics and Advanced Mechanics. Invited Auxiliary Professor at FEUP. Participated in +10 R&D projects.'],
        ],
        'tech' => [
            ['title' => 'Modular Payloads', 'icon' => $i('Módulos'), 'body' => 'Swap sensors to match the mission: monitoring, inspection, mapping, or anomaly detection.'],
            ['title' => 'Computer Vision', 'icon' => $i('Olho (visão)'), 'body' => 'Multi-spectral perception for object recognition, spatial mapping, and environment understanding.'],
            ['title' => 'Edge Processing', 'icon' => $i('Chip'), 'body' => 'On-device computation enabling sub-millisecond response times without cloud dependency.'],
            ['title' => 'Dynamic Motion', 'icon' => $i('Raio'), 'body' => 'Advanced actuators and control systems delivering stable mobility and navigation in the field.'],
            ['title' => 'Safety Systems', 'icon' => $i('Escudo'), 'body' => 'Multi-layered safety protocols ensuring reliable operation alongside people and infrastructure.'],
            ['title' => 'Performance Analytics', 'icon' => $i('Indicador'), 'body' => 'Continuous monitoring and optimisation of robotic systems across entire fleet deployments.'],
        ],
        'sectors' => [
            ['label' => 'Agriculture', 'title' => 'Farms & Permanent Crops', 'video' => '/videos/farm.mp4',
                'body' => 'Crop scouting, inspection, and environmental sensing for vineyards, orchards, and open-field operations. Turn observations into decisions.',
                'pills' => "Scouting & phenotyping\nYield Assessment\nDecision-ready insights"],
            ['label' => 'Industrial & Logistics', 'title' => 'Industrial & Logistics Sites', 'video' => '/videos/depot.mp4',
                'body' => 'Persistent monitoring across large outdoor areas — warehouse yards, depots, and storage zones. Detect events early and integrate with your security workflows.',
                'pills' => "Autonomous patrol\nNight operations\nAlert & evidence capture"],
            ['label' => 'Critical Infrastructure', 'title' => 'Critical Infrastructure', 'video' => '/videos/industry.mp4',
                'body' => 'Secure the perimeter of energy, water, telecom, and sensitive facilities. Reliable autonomy designed for high-consequence environments.',
                'pills' => "Perimeter coverage\nMulti-sensor perception\nIntegration-ready"],
        ],
        'metrics' => [
            ['value' => '24/7', 'label' => 'Autonomous patrol capability'],
            ['value' => '8h', 'label' => 'Typical autonomy per charge'],
            ['value' => '<1s', 'label' => 'Alert and stream latency'],
            ['value' => '~70kg', 'label' => 'Platform weight'],
        ],
        'caps' => [
            ['mode' => 'agriculture', 'title' => 'Crop monitoring', 'icon' => $i('Planta'), 'body' => 'Run repeated scouting missions to collect visual field data across rows, orchards, vineyards, and other permanent crops.'],
            ['mode' => 'agriculture', 'title' => 'Yield assessment', 'icon' => $i('Fruta'), 'body' => 'Use image capture and computer vision workflows to count fruits and estimate production potential over time.'],
            ['mode' => 'agriculture', 'title' => 'Disease symptom detection', 'icon' => $i('Gota'), 'body' => 'Support early identification of visible plant stress, disease symptoms, and anomalies for faster agronomic decisions.'],
            ['mode' => 'agriculture', 'title' => 'Phenotyping support', 'icon' => $i('Fenotipagem'), 'body' => 'Collect repeatable visual observations to support crop development tracking and field trials.'],
            ['mode' => 'agriculture', 'title' => 'Field data collection', 'icon' => $i('Indicador'), 'body' => 'Gather geo-referenced imagery, timestamps, route data, and inspection records across the same field over multiple missions.'],
            ['mode' => 'agriculture', 'title' => 'Decision-ready insights', 'icon' => $i('Gráfico'), 'body' => 'Transform field observations into practical outputs for scouting, intervention planning, reporting, and crop management.'],
            ['mode' => 'surveillance', 'title' => 'Autonomous patrol routes', 'icon' => $i('Rota'), 'body' => 'Configure patrol paths and schedules remotely for repeatable perimeter coverage across large outdoor sites.'],
            ['mode' => 'surveillance', 'title' => 'Intrusion detection', 'icon' => $i('Escudo'), 'body' => 'Detect movement anomalies and perimeter events, with alerts transmitted to the ViField cloud platform.'],
            ['mode' => 'surveillance', 'title' => 'Live video and evidence', 'icon' => $i('Vídeo'), 'body' => 'Access live video, event-triggered recordings, still frames, and patrol logs from a web-based dashboard.'],
            ['mode' => 'surveillance', 'title' => 'Fleet monitoring', 'icon' => $i('Indicador'), 'body' => 'Track robot status, battery level, position, and mission progress in real time through the ViField platform.'],
            ['mode' => 'surveillance', 'title' => 'Mixed-terrain operation', 'icon' => $i('Terreno'), 'body' => 'Designed for unstructured outdoor routes, including uneven ground, narrow paths, vegetation, and site perimeters.'],
            ['mode' => 'surveillance', 'title' => 'Edge AI processing', 'icon' => $i('Chip'), 'body' => 'On-device AI inference supports detection, classification, navigation, and real-time processing.'],
        ],
        'specs' => [
            ['name' => 'Dimensions', 'value' => '790 × 750 × 1300 mm'],
            ['name' => 'Weight', 'value' => '~70 kg including battery and payload'],
            ['name' => 'Propulsion', 'value' => 'Fully electric, 4-wheel drive'],
            ['name' => 'Battery', 'value' => 'Li-Ion, 1500 Wh'],
            ['name' => 'Operating time', 'value' => 'Up to 8h under typical patrol conditions'],
            ['name' => 'Maximum speed', 'value' => '1.5 m/s'],
            ['name' => 'Maximum slope', 'value' => '15°'],
            ['name' => 'Ground clearance', 'value' => '100 mm'],
        ],
        'sensors' => [
            ['title' => '360º vision', 'body' => 'A multi-camera vision system provides wide-area visual coverage for video streaming, event detection, crop observation, and image capture.'],
            ['title' => 'Depth & spatial sensing', 'body' => 'Depth and spatial sensing support obstacle detection, terrain awareness, and autonomous navigation in outdoor environments.'],
            ['title' => 'High-accuracy positioning', 'body' => 'High-accuracy positioning enables geo-referenced patrol routes, field missions, and repeatable data collection.'],
            ['title' => 'Onboard intelligence', 'body' => 'Onboard compute handles AI inference, perception, navigation, and real-time processing directly on the robot.'],
            ['title' => 'Mobile connectivity', 'body' => 'Primary cloud connectivity is provided through mobile networks, with remote access via the ViField web platform.'],
            ['title' => 'Cloud dashboard', 'body' => 'Monitor fleet status, patrol progress, live video, alerts, and exports through the ViField platform.'],
        ],
        'platform' => [
            ['title' => 'Fleet management', 'body' => 'View robot status, battery level, position, mission progress, patrol routes, schedules, and operating parameters.'],
            ['title' => 'Live video & alerts', 'body' => 'Access live video streams, event-triggered recordings, and notifications for intrusion events or anomalies.'],
            ['title' => 'Controlled exports', 'body' => 'Export patrol logs, event records, still frames, and video clips with authenticated access and logged activity.'],
            ['title' => 'Integration-ready', 'body' => 'Deployment-specific integrations with client systems can be defined, including REST API options by agreement.'],
        ],
        'reqs' => [
            ['title' => 'Site preparation', 'items' => "Defined patrol perimeter using shapefiles or GPS waypoints.\nGround surface passable for a robot of approximately 70 kg.\nMaximum slope of 15° and minimum path width of 1 m.\nCharging location with 230 VAC / 6 A power access."],
            ['title' => 'Connectivity & operation', 'items' => "5G coverage across the patrol area, with 4G LTE fallback supported.\nNo firewall configuration required, as the robot initiates outbound connections.\nNo robotics expertise required for daily operation.\nWeb browser access with no software installation required."],
        ],
    ];
}
