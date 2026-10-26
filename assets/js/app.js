document.addEventListener('click', function (e) {
  var confirmEl = e.target.closest('[data-confirm]');
  if (confirmEl && !confirm(confirmEl.getAttribute('data-confirm'))) {
    e.preventDefault();
    return;
  }
  var toggle = e.target.closest('[data-toggle-order]');
  if (!toggle) return;
  var row = toggle.closest('tr');
  var detail = row && row.nextElementSibling;
  if (!detail || !detail.classList.contains('order-detail-row')) return;
  var open = detail.hidden;
  detail.hidden = !open;
  toggle.textContent = open ? 'بستن' : 'جزئیات';
});

(function () {
  var wrap = document.querySelector('.lg-orbs');
  if (!wrap || window.matchMedia('(pointer: coarse)').matches || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    return;
  }
  window.addEventListener('pointermove', function (e) {
    var x = (e.clientX / window.innerWidth - 0.5) * 28;
    var y = (e.clientY / window.innerHeight - 0.5) * 20;
    wrap.style.transform = 'translate(' + x + 'px,' + y + 'px)';
  }, { passive: true });
})();

(function () {
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  document.querySelectorAll('.data-table tbody tr').forEach(function (tr, i) {
    if (tr.classList.contains('order-detail-row')) return;
    tr.style.animationDelay = (30 + i * 32) + 'ms';
  });
  document.querySelectorAll('.stat, .price-package, .pkg-add').forEach(function (el, i) {
    el.style.animationDelay = (80 + i * 45) + 'ms';
  });
})();
