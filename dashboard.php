<?php
require_once 'php/conexao.php';
verificarLogin();

// Configurar fuso horário
date_default_timezone_set('America/Sao_Paulo');

// Buscar dados do usuário
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

// Calcular dias sem fumar
$data_parar = new DateTime($usuario['data_parar'] . ' ' . $usuario['hora_parar']);
$hoje = new DateTime();
$intervalo = $hoje->diff($data_parar);
$dias_sem_fumar = $intervalo->days + ($intervalo->h / 24) + ($intervalo->i / 1440); // Convertendo horas e minutos para fração de dia

// Formatar o texto para exibição
$tempo_texto = '';
if ($intervalo->days > 0) {
    $tempo_texto .= $intervalo->days . ' ' . ($intervalo->days === 1 ? 'dia' : 'dias');
    if ($intervalo->h > 0 || $intervalo->i > 0) {
        $tempo_texto .= ', ';
    }
}
if ($intervalo->h > 0) {
    $tempo_texto .= $intervalo->h . ' ' . ($intervalo->h === 1 ? 'hora' : 'horas');
    if ($intervalo->i > 0) {
        $tempo_texto .= ' e ';
    }
}
if ($intervalo->i > 0) {
    $tempo_texto .= $intervalo->i . ' ' . ($intervalo->i === 1 ? 'minuto' : 'minutos');
}

// Calcular economia e cigarros evitados
$cigarros_por_carteira = 20;
$economia_por_dia = ($usuario['cigarros_por_dia'] / $cigarros_por_carteira) * $usuario['preco_carteira'];
$economia_total = $economia_por_dia * $dias_sem_fumar;
$cigarros_evitados = $usuario['cigarros_por_dia'] * $dias_sem_fumar;

// Buscar última recaída
$stmt = $conn->prepare("SELECT * FROM recaidas WHERE usuario_id = ? ORDER BY data_recaida DESC LIMIT 1");
$stmt->execute([$_SESSION['usuario_id']]);
$ultima_recaida = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard - Parar de Fumar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="app-container">
        <!-- Header Fixo -->
        <header class="app-header">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="user-greeting">
                        <h1 class="h4 mb-0">Olá, <?php echo htmlspecialchars($usuario['nome']); ?>!</h1>
                        <p class="greeting-subtitle">Continue firme na sua jornada</p>
                    </div>
                    <button id="theme-toggle" class="btn btn-icon" aria-label="Alternar tema">
                        <i class="bi bi-moon-stars"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Conteúdo Principal -->
        <main class="app-content">
            <!-- Card de Progresso -->
            <div class="progress-card">
                <div class="progress-header">
                    <div class="progress-title">
                        <h2>Seu Progresso</h2>
                        <p class="progress-subtitle">sem fumar</p>
                    </div>
                    <div id="tempo-sem-fumar" class="progress-time" 
                         data-data-parar="<?php echo $usuario['data_parar']; ?>"
                         data-hora-parar="<?php echo $usuario['hora_parar']; ?>">
                        <span id="dias-sem-fumar"><?php echo $tempo_texto; ?></span>
                    </div>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo min(($dias_sem_fumar / 30) * 100, 100); ?>%"></div>
                </div>
                <div class="progress-footer">
                    <p class="progress-goal">Meta: 30 dias</p>
                    <div class="progress-percentage"><?php echo number_format(min(($dias_sem_fumar / 30) * 100, 100), 0); ?>%</div>
                </div>
            </div>

            <!-- Cards de Estatísticas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $intervalo->days; ?></div>
                        <div class="stat-label">Dias sem fumar</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo number_format(floor($cigarros_evitados), 0, ',', '.'); ?></div>
                        <div class="stat-label">Cigarros evitados</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">R$ <?php echo number_format($economia_total, 2, ',', '.'); ?></div>
                        <div class="stat-label">Economizados</div>
                    </div>
                </div>
            </div>

            <!-- Mensagem Motivacional -->
            <div class="message-card">
                <div class="message-header">
                    <i class="bi bi-stars"></i>
                    <h3>Mensagem do Dia</h3>
                </div>
                <div class="message-content">
                    <p id="mensagem-motivacional">Carregando...</p>
                </div>
            </div>

            <?php if ($ultima_recaida): ?>
            <!-- Última Recaída -->
            <div class="relapse-card">
                <div class="relapse-header">
                    <i class="bi bi-exclamation-circle"></i>
                    <h3>Última Recaída</h3>
                </div>
                <div class="relapse-content">
                    <div class="relapse-date">
                        <i class="bi bi-calendar"></i>
                        <?php echo date('d/m/Y', strtotime($ultima_recaida['data_recaida'])); ?>
                    </div>
                    <div class="relapse-reason">
                        <?php echo htmlspecialchars($ultima_recaida['motivo']); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Botão de Recaída -->
            
        </main>

        <!-- Modal de Recaída -->
        <div class="modal fade" id="recaidaModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Recaída</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formRecaida">
                            <div class="form-group">
                                <label for="motivo">Motivo da Recaída</label>
                                <textarea class="form-control" id="motivo" rows="3" required 
                                        placeholder="Descreva o que levou você a fumar novamente..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-lg"></i>
                                Registrar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra de Navegação Inferior -->
        <nav class="bottom-nav">
            <a href="dashboard.php" class="nav-item active">
                <i class="bi bi-house-door"></i>
                <span>Início</span>
            </a>
            <a href="tela_diario.php" class="nav-item">
                <i class="bi bi-journal-text"></i>
                <span>Diário</span>
            </a>
            <a href="metas.php" class="nav-item">
                <i class="bi bi-trophy"></i>
                <span>Metas</span>
            </a>
            <a href="perfil.php" class="nav-item">
                <i class="bi bi-person"></i>
                <span>Perfil</span>
            </a>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
    <script src="js/tempo.js"></script>
</body>
</html> 