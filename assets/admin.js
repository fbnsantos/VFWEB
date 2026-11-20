(function () {
    'use strict';

    // Confirmação antes de ações destrutivas.
    document.addEventListener('click', (ev) => {
        const el = ev.target.closest('[data-confirm]');
        if (el && !confirm(el.dataset.confirm)) ev.preventDefault();
    });

    // Copiar caminho na biblioteca.
    document.addEventListener('click', async (ev) => {
        const btn = ev.target.closest('[data-copy]');
        if (!btn) return;
        try {
            await navigator.clipboard.writeText(btn.dataset.copy);
            const old = btn.textContent;
            btn.textContent = 'Copiado!';
            setTimeout(() => (btn.textContent = old), 1200);
        } catch (_) {
            prompt('Copie o caminho:', btn.dataset.copy);
        }
    });

    // Contadores de caracteres (títulos e descrições de SEO).
    document.querySelectorAll('[data-count]').forEach((el) => {
        const ideal = +el.dataset.count;
        const out = document.createElement('span');
        out.className = 'counter';
        el.insertAdjacentElement('afterend', out);
        const upd = () => {
            out.textContent = `${el.value.length} / ${ideal} caracteres`;
            out.classList.toggle('over', el.value.length > ideal);
        };
        el.addEventListener('input', upd);
        upd();
    });

    // Ícones: escolher um da lista preenche o código SVG e a pré-visualização.
    document.querySelectorAll('.iconfield').forEach((box) => {
        const select = box.querySelector('[data-icon-preset]');
        const code = box.querySelector('[data-icon-code]');
        const prev = box.querySelector('.iconprev');
        select.addEventListener('change', () => {
            if (!select.value) return;
            code.value = select.value;
            prev.innerHTML = select.value;
        });
        code.addEventListener('input', () => { prev.innerHTML = code.value; });
    });

    // Campos de imagem/vídeo: janela "Biblioteca" para escolher um ficheiro já enviado.
    document.addEventListener('click', async (ev) => {
        const btn = ev.target.closest('[data-pick]');
        if (!btn) return;
        const field = btn.closest('.filefield');
        const input = field.querySelector('input[type=text], input:not([type])');
        const kind = field.dataset.kind;
        const res = await fetch('/admin/media.json?kind=' + kind, { credentials: 'same-origin' });
        const items = await res.json();

        const overlay = document.createElement('div');
        overlay.className = 'picker';
        const box = document.createElement('div');
        box.className = 'picker-box';
        box.innerHTML = '<div class="head"><h2>Biblioteca</h2><button type="button" class="btn small" data-close>Fechar</button></div>'
            + (items.length ? '<div class="picker-grid"></div>' : '<p class="muted">Ainda não há ficheiros. Envie em «Imagens e vídeos».</p>');
        overlay.appendChild(box);
        document.body.appendChild(overlay);
        const grid = box.querySelector('.picker-grid');
        items.forEach((it) => {
            const b = document.createElement('button');
            b.type = 'button';
            const media = document.createElement(kind === 'video' ? 'video' : 'img');
            media.src = it.value;
            if (kind === 'video') { media.muted = true; media.preload = 'metadata'; } else { media.loading = 'lazy'; media.alt = ''; }
            b.appendChild(media);
            b.appendChild(document.createTextNode(it.title));
            b.addEventListener('click', () => {
                input.value = it.value;
                const preview = field.querySelector('.filepreview');
                preview.innerHTML = '';
                const m = document.createElement(kind === 'video' ? 'video' : 'img');
                m.src = it.value;
                if (kind === 'video') { m.muted = true; m.controls = true; m.preload = 'metadata'; } else { m.alt = ''; }
                preview.appendChild(m);
                overlay.remove();
            });
            grid.appendChild(b);
        });
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay || e.target.closest('[data-close]')) overlay.remove();
        });
    });
})();
