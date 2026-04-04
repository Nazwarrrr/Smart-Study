/**
 * Smart Study Planner — validasi form, animasi, modal congratulations, typing login
 */
(function () {
  'use strict';

  /* --- Fade / progress / stagger ringan untuk card --- */
  function initProgressBar() {
    var fill = document.querySelector('.progress-bar__fill');
    if (!fill) return;
    var target = fill.getAttribute('data-width') || '0';
    requestAnimationFrame(function () {
      fill.style.width = target + '%';
    });
  }

  function initTaskCardStagger() {
    var cards = document.querySelectorAll('.task-list .task-card');
    cards.forEach(function (card, i) {
      card.style.animationDelay = Math.min(i * 0.06, 0.48) + 's';
    });
  }

  /* --- Login: efek mengetik subtitle --- */
  function initLoginTyping() {
    var el = document.getElementById('typing-text');
    if (!el) return;
    var full = el.getAttribute('data-text') || '';
    if (!full) return;

    var i = 0;
    function tick() {
      if (i <= full.length) {
        el.textContent = full.slice(0, i);
        i++;
        window.setTimeout(tick, 42);
      }
    }
    window.setTimeout(tick, 400);
  }

  /* --- Form tambah tugas: judul, deskripsi, deadline, kategori wajib --- */
  function initTambahForm() {
    var form = document.getElementById('form-tambah');
    if (!form) return;

    var titleInput = document.getElementById('title');
    var descInput = document.getElementById('description');
    var deadlineInput = document.getElementById('deadline');
    var catInput = document.getElementById('category');
    var titleErr = document.getElementById('err-title');
    var descErr = document.getElementById('err-description');
    var deadlineErr = document.getElementById('err-deadline');
    var catErr = document.getElementById('err-category');

    function showErr(el, show) {
      if (!el) return;
      el.classList.toggle('is-visible', show);
    }

    function validate() {
      var ok = true;
      if (!titleInput || !titleInput.value.trim()) {
        if (titleInput) titleInput.classList.add('error');
        showErr(titleErr, true);
        ok = false;
      } else {
        titleInput.classList.remove('error');
        showErr(titleErr, false);
      }

      if (!descInput || !descInput.value.trim()) {
        if (descInput) descInput.classList.add('error');
        showErr(descErr, true);
        ok = false;
      } else {
        descInput.classList.remove('error');
        showErr(descErr, false);
      }

      if (!deadlineInput || !deadlineInput.value) {
        if (deadlineInput) deadlineInput.classList.add('error');
        showErr(deadlineErr, true);
        ok = false;
      } else {
        deadlineInput.classList.remove('error');
        showErr(deadlineErr, false);
      }

      if (!catInput || !catInput.value) {
        if (catInput) catInput.classList.add('error');
        showErr(catErr, true);
        ok = false;
      } else {
        catInput.classList.remove('error');
        showErr(catErr, false);
      }

      return ok;
    }

    form.addEventListener('submit', function (e) {
      if (!validate()) {
        e.preventDefault();
      }
    });

    if (titleInput) {
      titleInput.addEventListener('input', function () {
        if (titleInput.value.trim()) {
          titleInput.classList.remove('error');
          showErr(titleErr, false);
        }
      });
    }
    if (descInput) {
      descInput.addEventListener('input', function () {
        if (descInput.value.trim()) {
          descInput.classList.remove('error');
          showErr(descErr, false);
        }
      });
    }
    if (deadlineInput) {
      deadlineInput.addEventListener('change', function () {
        if (deadlineInput.value) {
          deadlineInput.classList.remove('error');
          showErr(deadlineErr, false);
        }
      });
    }
    if (catInput) {
      catInput.addEventListener('change', function () {
        if (catInput.value) {
          catInput.classList.remove('error');
          showErr(catErr, false);
        }
      });
    }
  }

  /* --- Konfirmasi hapus + animasi card --- */
  function initDeleteConfirm() {
    document.querySelectorAll('form[data-confirm]').forEach(function (f) {
      f.addEventListener('submit', function (e) {
        var msg = f.getAttribute('data-confirm');
        if (msg && !window.confirm(msg)) {
          e.preventDefault();
          return;
        }
        var card = f.closest('.task-card');
        if (card) {
          card.classList.add('removing');
        }
      });
    });
  }

  /* --- Modal: Congratulations (query ?done=1) --- */
  function initCongratsModal() {
    var body = document.body;
    if (!body || body.getAttribute('data-show-congrats') !== '1') return;

    var modal = document.getElementById('modal-congrats');
    if (!modal) return;

    function openModal() {
      modal.removeAttribute('hidden');
      modal.setAttribute('aria-hidden', 'false');
    }

    function closeModal() {
      modal.setAttribute('hidden', '');
      modal.setAttribute('aria-hidden', 'true');
      cleanUrl();
    }

    function cleanUrl() {
      try {
        var u = new URL(window.location.href);
        if (u.searchParams.has('done')) {
          u.searchParams.delete('done');
          window.history.replaceState({}, '', u.pathname + u.search + u.hash);
        }
      } catch (err) {
        /* IE / edge lama: abaikan */
      }
    }

    openModal();

    modal.querySelectorAll('[data-close-modal]').forEach(function (b) {
      b.addEventListener('click', closeModal);
    });

    modal.querySelectorAll('.modal-congrats-close').forEach(function (btn) {
      btn.addEventListener('click', closeModal);
    });
  }

  initProgressBar();
  initTaskCardStagger();
  initLoginTyping();
  initTambahForm();
  initDeleteConfirm();
  initCongratsModal();
})();
