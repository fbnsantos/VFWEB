/* ────────────────────────────────────────────────────────────────
   ViField: comportamento comum a todas as páginas
   ----------------------------------------------------------------
   - ano atual no rodapé (#year)
   - formulário de contacto via mailto (#contactForm, só na homepage)
   - alternador Agricultura / Vigilância ([data-capability], só em orioos.html)
   - animação de entrada dos elementos .reveal
     (os filhos .reveal de um elemento com data-stagger="70" entram
      um a um, com 70 ms de intervalo)

   Carregar no fim do <body>, DEPOIS de qualquer script que gere
   conteúdo (ex.: js/news.js), para que a animação o apanhe.
──────────────────────────────────────────────────────────────── */
(function () {
  var forEach = function (list, fn) { Array.prototype.forEach.call(list, fn); };

  // Ano no rodapé
  var year = document.getElementById("year");
  if (year) year.textContent = new Date().getFullYear();

  // Formulário de contacto: envia para o servidor (POST /contact; as mensagens
  // aparecem no backoffice). Se o envio falhar, abre o programa de email do visitante.
  var form = document.getElementById("contactForm");
  if (form) {
    var status = document.getElementById("formStatus");
    var button = form.querySelector("button[type=submit]");
    var say = function (text, ok) {
      if (!status) return;
      status.textContent = text;
      status.hidden = false;
      status.style.color = ok ? "var(--vf-green-800, #166534)" : "#b91c1c";
    };
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var label = button ? button.textContent : "";
      if (button) { button.disabled = true; button.textContent = "Sending…"; }
      fetch(form.getAttribute("action") || "/contact", {
        method: "POST",
        body: new FormData(form),
        headers: { "Accept": "application/json" }
      }).then(function (res) {
        return res.json().then(function (data) { return { status: res.status, data: data }; });
      }).then(function (r) {
        if (r.data && r.data.ok) {
          form.reset();
          say(r.data.message, true);
        } else if (r.status >= 400 && r.status < 500) {
          say((r.data && r.data.message) || "Please check the form and try again.", false);
        } else {
          throw new Error("server");
        }
      }).catch(function () {
        var mailto = form.getAttribute("data-mailto");
        if (!mailto) { say("Sorry, we could not send your message. Please try again later.", false); return; }
        var body = "Name: " + form.elements["name"].value.trim() + "\nEmail: " + form.elements["email"].value.trim() +
          "\n\nMessage:\n" + form.elements["message"].value.trim();
        window.location.href = "mailto:" + mailto + "?subject=" + encodeURIComponent(form.getAttribute("data-subject") || "Contact Request") +
          "&body=" + encodeURIComponent(body);
      }).then(function () {
        if (button) { button.disabled = false; button.textContent = label; }
      });
    });
  }

  // Alternador de modos (orioos.html)
  var capButtons = document.querySelectorAll("[data-capability]");
  var capPanels = document.querySelectorAll("[data-panel]");
  forEach(capButtons, function (button) {
    button.addEventListener("click", function () {
      var target = button.getAttribute("data-capability");
      forEach(capButtons, function (btn) {
        var isActive = btn === button;
        btn.classList.toggle("is-active", isActive);
        btn.setAttribute("aria-selected", isActive ? "true" : "false");
      });
      forEach(capPanels, function (panel) {
        panel.classList.toggle("is-active", panel.getAttribute("data-panel") === target);
      });
    });
  });

  // Atraso escalonado
  forEach(document.querySelectorAll("[data-stagger]"), function (group) {
    var step = parseInt(group.getAttribute("data-stagger"), 10) || 0;
    forEach(group.querySelectorAll(".reveal"), function (el, i) {
      el.style.transitionDelay = (i * step) + "ms";
    });
  });

  // Animação de entrada
  var els = document.querySelectorAll(".reveal");
  if (!("IntersectionObserver" in window)) {
    forEach(els, function (el) { el.classList.add("is-in"); });
    return;
  }
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add("is-in");
        io.unobserve(entry.target);
      }
    });
  }, { rootMargin: "0px 0px -8% 0px", threshold: 0.1 });
  forEach(els, function (el) { io.observe(el); });
})();
