<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inscription — Takalo-takalo</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="../assets/css/theme.css" rel="stylesheet">
</head>
<body class="tt-page">

  <div class="container py-4">
    <nav class="navbar tt-navbar rounded-4 px-3">
      <a class="navbar-brand text-white d-flex align-items-center gap-2" href="../client/index.html">
        <span class="badge badge-soft-info">TT</span><span class="fw-semibold">Takalo-takalo</span>
      </a>
      <div class="d-flex gap-2">
        <a class="btn btn-tt-ghost btn-sm" href="/">Connexion</a>
      </div>
    </nav>

    <main class="tt-main d-flex align-items-center justify-content-center py-5">
      <div class="tt-surface p-4 p-md-5 w-100" style="max-width: 620px;">
        <div class="mb-3">
          <h1 class="h4 mb-1">Créer un compte</h1>
          <div class="text-muted-2">Rejoins la communauté et échange tes objets.</div>
        </div>

        <form class="needs-validation" novalidate>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nom</label>
              <input class="form-control" required placeholder="Rakot">
              <div class="invalid-feedback">Nom requis.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Prénom</label>
              <input class="form-control" required placeholder="Lucas">
              <div class="invalid-feedback">Prénom requis.</div>
            </div>
            <div class="col-12">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" required placeholder="lucas@mail.com">
              <div class="invalid-feedback">Email invalide.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Mot de passe</label>
              <input type="password" class="form-control" required minlength="6" placeholder="Minimum 6 caractères">
              <div class="invalid-feedback">Minimum 6 caractères.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirmer</label>
              <input type="password" class="form-control" required minlength="6" placeholder="Répéter le mot de passe">
              <div class="invalid-feedback">Confirmation requise.</div>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" required id="terms">
                <label class="form-check-label" for="terms">
                  J’accepte les conditions (démo).
                </label>
                <div class="invalid-feedback">Tu dois accepter.</div>
              </div>
            </div>
          </div>

          <button class="btn btn-tt-primary w-100 py-2 mt-3">Créer mon compte</button>

          <div class="text-center mt-3 text-muted-2 small">
            Déjà inscrit ? <a class="text-white" href="/">Se connecter</a>
          </div>
        </form>
      </div>
    </main>
  </div>

  <div class="toast-container position-fixed bottom-0 end-0 p-3" id="ttToastContainer"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/app.js"></script>
</body>
</html>
