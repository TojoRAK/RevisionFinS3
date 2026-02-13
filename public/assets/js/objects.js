(() => {
    async function apiFetch(url, { method = 'GET', body = null } = {}) {
        const opts = {
            method,
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }
        };
        if (body) opts.body = JSON.stringify(body);
        const res = await fetch(url, opts);
        return await res.json();
    }

    function formatDate(dateString) {
        const d = new Date(dateString);
        return d.toLocaleDateString('fr-FR');
    }

    let objectsData = [];
    let modal = null;

    async function loadCategories() {
        const select = document.getElementById('objectCategory');
        if (!select) return;

        const res = await apiFetch('/admin/categories/list');

        if (!res.ok || !res.data.length) {
            select.innerHTML = `<option value="">Aucune catégorie</option>`;
            return;
        }


        select.querySelectorAll('option:not([value=""])').forEach(o => o.remove());

        res.data.forEach(cat => {
            const opt = document.createElement('option');
            opt.value = cat.id;
            opt.textContent = cat.name;
            select.appendChild(opt);
        });
    }

    async function loadObjects() {
        const tbody = document.getElementById('objectsTableBody');
        if (!tbody) return;

        const res = await apiFetch('/objets/list');

        if (!res.ok || !res.data.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted">Aucun objet trouvé</td>
                </tr>`;
            objectsData = [];
            return;
        }

        objectsData = res.data; 

        tbody.innerHTML = '';

        res.data.forEach(obj => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="fw-semibold">${obj.title}</div>
                    <div class="small text-muted">${obj.description ?? ''}</div>
                </td>
                <td>${obj.category_name}</td>
                <td>${obj.estimated_value} Ar</td>
                <td>${formatDate(obj.created_at)}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-tt-ghost edit-btn" data-id="${obj.id}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${obj.id}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>`;
            tbody.appendChild(tr);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('objectForm');
        const modalEl = document.getElementById('modalItemForm');
        modal = modalEl ? new bootstrap.Modal(modalEl) : null;

        loadCategories();
        loadObjects();

     
        form?.addEventListener('submit', async e => {
            e.preventDefault();
            const id = document.getElementById('objectId').value;
            const title = document.getElementById('objectTitle').value;
            const value = document.getElementById('objectValue').value;
            const category = document.getElementById('objectCategory').value;
            const description = document.getElementById('objectDescription').value;

            const body = { title, estimated_value: value, category_id: category, description };
            if (id) {
                await apiFetch(`/objets/${id}`, { method: 'POST', body });
            } else {
                await apiFetch('/objets', { method: 'POST', body });
            }

            form.reset();
            document.getElementById('objectId').value = '';
            modal?.hide();
            loadObjects();
        });

       
        let deleteId = null;
        const deleteModalEl = document.getElementById('modalConfirmDelete');
        const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

        document.addEventListener('click', e => {
            const editBtn = e.target.closest('.edit-btn');
            const deleteBtn = e.target.closest('.delete-btn');

       
            if (editBtn) {
                const id = editBtn.dataset.id;
                const obj = objectsData.find(o => o.id == id);
                if (!obj) return;

                document.getElementById('objectId').value = obj.id;
                document.getElementById('objectTitle').value = obj.title;
                document.getElementById('objectValue').value = obj.estimated_value;
                document.getElementById('objectCategory').value = obj.category_id;
                document.getElementById('objectDescription').value = obj.description ?? '';

                modal?.show();
            }

          
            if (deleteBtn) {
                deleteId = deleteBtn.dataset.id;
                deleteModal?.show();
            }
        });

        confirmDeleteBtn?.addEventListener('click', async () => {
            if (!deleteId) return;
            await apiFetch(`/objets/${deleteId}`, { method: 'DELETE' });
            deleteModal?.hide();
            deleteId = null;
            loadObjects();
        });
    });
})();
