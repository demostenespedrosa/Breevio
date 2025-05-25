<?php
require_once 'php/conexao.php';
verificarLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Diário - Parar de Fumar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/diario.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Meu Diário</h1>
            <button id="theme-toggle" class="btn btn-outline-secondary" aria-label="Alternar tema">
                <i class="bi bi-moon-stars"></i>
            </button>
        </div>

        <div id="alert-container"></div>

        <!-- Formulário do Diário -->
        <form id="formDiario" class="needs-validation" novalidate>
            <!-- Humor -->
            <div class="card mb-4" id="step-humor">
                <div class="card-header">
                    <h5 class="mb-0">Como está seu humor hoje?</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="humor-card" data-humor="1">
                                <div class="emoji">😢</div>
                                <div class="label">Muito mal</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="humor-card" data-humor="2">
                                <div class="emoji">😕</div>
                                <div class="label">Mal</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="humor-card" data-humor="3">
                                <div class="emoji">😐</div>
                                <div class="label">OK</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="humor-card" data-humor="4">
                                <div class="emoji">🙂</div>
                                <div class="label">Bem</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="humor-card" data-humor="5">
                                <div class="emoji">😄</div>
                                <div class="label">Ótimo</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gatilhos -->
            <div class="card mb-4" id="step-gatilhos" style="display: none;">
                <div class="card-header">
                    <h5 class="mb-0">Quais foram seus gatilhos hoje?</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button type="button" class="btn btn-outline-primary gatilho-pill" data-gatilho="ansiedade">Ansiedade</button>
                        <button type="button" class="btn btn-outline-primary gatilho-pill" data-gatilho="estresse">Estresse</button>
                        <button type="button" class="btn btn-outline-primary gatilho-pill" data-gatilho="social">Social</button>
                        <button type="button" class="btn btn-outline-primary gatilho-pill" data-gatilho="trabalho">Trabalho</button>
                        <button type="button" class="btn btn-outline-primary gatilho-pill" data-gatilho="fissura">Fissura</button>
                        <button type="button" class="btn btn-outline-primary gatilho-pill" data-gatilho="outro">Outro</button>
                    </div>
                    <div id="outro-gatilho" class="input-group" style="display: none;">
                        <input type="text" class="form-control" id="outro-gatilho-input" maxlength="20" placeholder="Qual gatilho?">
                    </div>
                </div>
            </div>

            <!-- Energia -->
            <div class="card mb-4" id="step-energia" style="display: none;">
                <div class="card-header">
                    <h5 class="mb-0">Como está sua energia hoje?</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="energia-card" data-energia="baixo">
                                <div class="icon">🔋</div>
                                <div class="label">Baixa</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="energia-card" data-energia="medio">
                                <div class="icon">🔋🔋</div>
                                <div class="label">Média</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="energia-card" data-energia="alto">
                                <div class="icon">🔋🔋🔋</div>
                                <div class="label">Alta</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conquista -->
            <div class="card mb-4" id="step-conquista" style="display: none;">
                <div class="card-header">
                    <h5 class="mb-0">Qual sua conquista de hoje?</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="conquista-card" data-conquista="Resisti à fissura">
                                <div class="icon">💪</div>
                                <div class="label">Resisti à fissura</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="conquista-card" data-conquista="Passei sem fumar 2h">
                                <div class="icon">⏰</div>
                                <div class="label">2h sem fumar</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="conquista-card" data-conquista="Outra conquista">
                                <div class="icon">🎯</div>
                                <div class="label">Outra</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Texto Opcional -->
            <div class="card mb-4" id="step-texto" style="display: none;">
                <div class="card-header">
                    <h5 class="mb-0">Quer adicionar uma observação?</h5>
                </div>
                <div class="card-body">
                    <textarea class="form-control" id="texto-opcional" rows="2" maxlength="200" placeholder="Digite sua observação (opcional)"></textarea>
                </div>
            </div>

            <!-- Botões de Navegação -->
            <div class="d-flex justify-content-between mb-4">
                <button type="button" class="btn btn-secondary" id="btn-voltar" style="display: none;">Voltar</button>
                <button type="button" class="btn btn-primary" id="btn-proximo">Próximo</button>
            </div>
        </form>

        <!-- Barra de Navegação Inferior -->
        <nav class="nav-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-3 text-center">
                        <a href="dashboard.php" class="nav-link">
                            <i class="bi bi-house-door"></i>
                            <div>Dashboard</div>
                        </a>
                    </div>
                    <div class="col-3 text-center">
                        <a href="tela_diario.php" class="nav-link active">
                            <i class="bi bi-journal-text"></i>
                            <div>Diário</div>
                        </a>
                    </div>
                    <div class="col-3 text-center">
                        <a href="metas.php" class="nav-link">
                            <i class="bi bi-trophy"></i>
                            <div>Metas</div>
                        </a>
                    </div>
                    <div class="col-3 text-center">
                        <a href="perfil.php" class="nav-link">
                            <i class="bi bi-person"></i>
                            <div>Perfil</div>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
    <script src="js/diario.js"></script>
</body>
</html> 