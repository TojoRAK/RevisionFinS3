/* Takalo-takalo UI minimal JS (Bootstrap 5) */
(() => {
  // Bootstrap validation style
  document.querySelectorAll('form.needs-validation').forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
        toast("Veuillez corriger les erreurs du formulaire.", "warning");
      } else {
        event.preventDefault(); // no backend
        toast("Action simulée (pas de backend).", "info");
      }
      form.classList.add('was-validated');
    }, false);
  });

  // Multi-photo preview
  document.querySelectorAll('[data-tt-multiphoto]').forEach((input) => {
    input.addEventListener('change', () => {
      const targetId = input.getAttribute('data-tt-multiphoto');
      const target = document.getElementById(targetId);
      if (!target) return;

      target.innerHTML = "";
      const files = Array.from(input.files || []);
      if (!files.length) {
        target.innerHTML = `<div class="text-muted-2 small">Aperçu des photos…</div>`;
        return;
      }

      files.slice(0, 8).forEach(file => {
        const url = URL.createObjectURL(file);
        const col = document.createElement('div');
        col.className = "col-6 col-md-3";
        col.innerHTML = `
          <div class="tt-card p-2 h-100">
            <img src="${url}" class="w-100" style="aspect-ratio:1/1;object-fit:cover;border-radius:12px;border:1px solid rgba(148,163,184,.16)" alt="preview">
            <div class="small text-muted-2 mt-2 text-truncate">${file.name}</div>
          </div>`;
        target.appendChild(col);
      });
    });
  });

  // Simple helpers
  window.toast = (message, variant = "info") => {
    const container = document.getElementById('ttToastContainer');
    if (!container) return alert(message);

    const variants = {
      success: "text-bg-success",
      warning: "text-bg-warning",
      danger:  "text-bg-danger",
      info:    "text-bg-info",
      dark:    "text-bg-dark"
    };
    const klass = variants[variant] || variants.info;

    const el = document.createElement('div');
    el.className = `toast align-items-center ${klass} border-0`;
    el.role = "alert";
    el.ariaLive = "assertive";
    el.ariaAtomic = "true";
    el.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>`;
    container.appendChild(el);

    const t = new bootstrap.Toast(el, { delay: 3200 });
    t.show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
  };

  // Demo actions (buttons with data-tt-action)
  document.querySelectorAll('[data-tt-action]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const action = btn.getAttribute('data-tt-action');
      toast(`Action: ${action} (simulée)`, "info");
    });
  });
})();
