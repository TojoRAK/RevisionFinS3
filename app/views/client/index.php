<!doctype html>
<html lang="fr" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Objets — Takalo-takalo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../assets/css/theme.css" rel="stylesheet">
</head>

<body class="tt-page">

    <!-- components/header_client.html
     Dynamic placeholders (Flight later):
     - {{user.name}} / {{user.avatarUrl}} / {{isLoggedIn}}
-->
    <?php include('inc/header.php') ?>


    <main class="tt-main">
        <div class="container py-4">
            <div class="tt-hero p-3 p-md-4 mb-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h1 class="h4 mb-1">Objets disponibles</h1>
                        <div class="text-muted-2">Découvre les objets des autres utilisateurs et propose un échange.</div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <div class="input-group" style="min-width: 260px;">
                            <span class="input-group-text bg-transparent" style="border-color:var(--tt-border); color:var(--tt-text)"><i class="bi bi-search"></i></span>
                            <input class="form-control" placeholder="Rechercher un objet…">
                        </div>
                        <select class="form-select" name="category_id">
                            <option value="">Toutes catégories</option>

                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>">
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <button class="btn btn-tt-ghost" data-tt-action="filter">Filtrer</button>
                    </div>
                </div>
            </div>

            <div class="tt-surface p-3 p-md-4">
                <div class="row g-3">

                    <?php foreach ($objets as $objet): ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="tt-card p-3 h-100">

                                <div class="fw-semibold">
                                    <?= htmlspecialchars($objet['title']) ?>
                                </div>

                                <div class="text-muted-2 small mb-2">
                                    Catégorie: <?= htmlspecialchars($objet['category_name']) ?>
                                </div>

                                <div class="mb-2">
                                    <?= nl2br(htmlspecialchars(substr($objet['description'], 0, 80))) ?>...
                                </div>

                                <span class="badge badge-soft">
                                    <?= number_format($objet['estimated_value'], 2, ',', ' ') ?> Ar
                                </span>

                                <div class="text-muted-2 small mt-2">
                                    Ajouté le <?= date('d/m/Y', strtotime($objet['created_at'])) ?>
                                </div>

                                <div class="mt-3">
                                    <a class="btn btn-tt-primary w-100"
                                        href="/objet/<?= $objet['id'] ?>">
                                        Voir
                                    </a>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>


                </div>

            </div>
        </div>
    </main>

    <!-- components/footer.html -->
    <?php include('inc/footer.php') ?>
    <script>
        document.getElementById('year') && (document.getElementById('year').textContent = new Date().getFullYear());
    </script>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="ttToastContainer"></div>
    <!-- components/modals.html -->
    <div class="modal fade" id="modalQuickPost" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content tt-card">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Ajouter un objet (démo)</h5>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="modal-body needs-validation" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Titre</label>
                        <input class="form-control" required placeholder="Ex: Casque audio">
                        <div class="invalid-feedback">Titre requis.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valeur estimée</label>
                        <input type="number" class="form-control" required min="1" placeholder="Ex: 120000">
                        <div class="invalid-feedback">Valeur invalide.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catégorie</label>
                        <select class="form-select" required>
                            <option value="">Choisir…</option>
                            <option>Électronique</option>
                            <option>Maison</option>
                            <option>Mode</option>
                            <option>Loisirs</option>
                        </select>
                        <div class="invalid-feedback">Catégorie requise.</div>
                    </div>
                    <button class="btn btn-tt-primary w-100">Enregistrer (simulé)</button>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content tt-card">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="tt-empty">
                        <div class="icon"><i class="bi bi-exclamation-triangle"></i></div>
                        <div class="fw-semibold mt-2">Cette action est irréversible.</div>
                        <div class="text-muted-2 small mt-1">Suppression simulée (pas de backend).</div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button class="btn btn-tt-ghost" data-bs-dismiss="modal">Annuler</button>
                    <button class="btn btn-danger" data-tt-action="delete-confirm">Supprimer</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.js"></script>
</body>

</html>