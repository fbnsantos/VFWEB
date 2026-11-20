/* ────────────────────────────────────────────────────────────────
   ViField: filtros da página de notícias
   ----------------------------------------------------------------
   As notícias e os botões de filtro são gerados no servidor
   (app/views/site/news.php, a partir do backoffice). Este ficheiro
   só mostra/esconde os cartões conforme a categoria escolhida.
──────────────────────────────────────────────────────────────── */
(function () {
  var grid = document.getElementById("newsGrid");
  var buttons = Array.prototype.slice.call(document.querySelectorAll("#filterToggle [data-filter]"));
  if (!grid || !buttons.length) return;

  var cards = Array.prototype.slice.call(grid.querySelectorAll(".news-card"));
  var countEl = document.getElementById("newsCount");
  var emptyEl = document.getElementById("newsEmpty");

  function apply(filter) {
    var shown = 0;
    cards.forEach(function (card) {
      var match = filter === "all" || card.getAttribute("data-category") === filter;
      card.classList.toggle("is-hidden", !match);
      if (match) shown++;
    });
    if (countEl) countEl.textContent = shown;
    if (emptyEl) emptyEl.classList.toggle("is-shown", shown === 0);
  }

  buttons.forEach(function (btn) {
    btn.addEventListener("click", function () {
      buttons.forEach(function (b) {
        var active = b === btn;
        b.classList.toggle("is-active", active);
        b.setAttribute("aria-selected", active ? "true" : "false");
      });
      apply(btn.getAttribute("data-filter"));
    });
  });
})();
