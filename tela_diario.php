<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'php/conexao.php';
verificarLogin();

// Buscar dados do usuário
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

// Buscar dados do diário dos últimos 30 dias
$stmt = $conn->prepare("
    SELECT * FROM diario 
    WHERE usuario_id = ? 
    AND data_registro >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ORDER BY data_registro DESC
");
$stmt->execute([$_SESSION['usuario_id']]);
$registros = $stmt->fetchAll();

// Calcular médias e estatísticas
$total_registros = count($registros);
$media_humor = 0;
$media_energia = 0;
$gatilhos_frequentes = [];
$conquistas_frequentes = [];

if ($total_registros > 0) {
    $soma_humor = 0;
    $soma_energia = 0;
    
    foreach ($registros as $registro) {
        $soma_humor += (int)$registro['humor'];
        $soma_energia += (int)$registro['energia'];
        
        // Contar gatilhos
        $gatilhos = explode(',', $registro['gatilhos']);
        foreach ($gatilhos as $gatilho) {
            $gatilho = trim($gatilho);
            if (!isset($gatilhos_frequentes[$gatilho])) {
                $gatilhos_frequentes[$gatilho] = 0;
            }
            $gatilhos_frequentes[$gatilho]++;
        }
        
        // Contar conquistas
        if (!isset($conquistas_frequentes[$registro['conquista']])) {
            $conquistas_frequentes[$registro['conquista']] = 0;
        }
        $conquistas_frequentes[$registro['conquista']]++;
    }
    
    $media_humor = round($soma_humor / $total_registros, 1);
    $media_energia = round($soma_energia / $total_registros, 1);
}

// Ordenar gatilhos e conquistas por frequência
arsort($gatilhos_frequentes);
arsort($conquistas_frequentes);

// Verificar se já registrou o diário hoje
$diario_hoje = false;
if ($total_registros > 0 && $registros[0]['data_registro'] == date('Y-m-d')) {
    $diario_hoje = true;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Meu Progresso - Parar de Fumar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/diario.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Header Fixo -->
    <header class="app-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0">Meu Progresso</h1>
                <button id="theme-toggle" class="btn btn-icon" aria-label="Alternar tema">
                    <i class="bi bi-moon-stars"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <main class="app-content">
        <div id="alert-container"></div>

        <?php if ($diario_hoje): ?>
        <div class="alert alert-success mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle me-2"></i>
                <div>
                    <strong>Ótimo!</strong> Você já registrou seu diário hoje. 
                    <div class="mt-1 text-muted">Volte amanhã para registrar como foi seu dia.</div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-info mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-info-circle me-2"></i>
                <div>
                    <strong>Lembrete:</strong> Você ainda não registrou seu diário hoje.
                    <div class="mt-2">
                        <a href="diario.php" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i>
                            Registrar Agora
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Resumo do Progresso -->
        <div class="progress-summary mb-4">
            <h2 class="h5 mb-3">Resumo dos Últimos 30 Dias</h2>
            <div class="row g-3">
                <div class="col-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-emoji-smile"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $media_humor; ?></div>
                            <div class="stat-label">Média de Humor</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-lightning"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $media_energia; ?></div>
                            <div class="stat-label">Média de Energia</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gatilhos Frequentes -->
        <div class="triggers-summary mb-4">
            <h2 class="h5 mb-3">Gatilhos Mais Frequentes</h2>
            <div class="triggers-list">
                <?php 
                $count = 0;
                foreach ($gatilhos_frequentes as $gatilho => $frequencia): 
                    if ($count >= 3) break;
                ?>
                <div class="trigger-item">
                    <span class="trigger-name"><?php echo htmlspecialchars($gatilho); ?></span>
                    <span class="trigger-count"><?php echo $frequencia; ?>x</span>
                </div>
                <?php 
                $count++;
                endforeach; 
                ?>
            </div>
        </div>

        <!-- Conquistas Frequentes -->
        <div class="achievements-summary mb-4">
            <h2 class="h5 mb-3">Conquistas Mais Frequentes</h2>
            <div class="achievements-list">
                <?php 
                $count = 0;
                foreach ($conquistas_frequentes as $conquista => $frequencia): 
                    if ($count >= 3) break;
                ?>
                <div class="achievement-item">
                    <span class="achievement-name"><?php echo htmlspecialchars($conquista); ?></span>
                    <span class="achievement-count"><?php echo $frequencia; ?>x</span>
                </div>
                <?php 
                $count++;
                endforeach; 
                ?>
            </div>
        </div>

        <!-- Histórico de Registros -->
        <div class="history-section">
            <h2 class="h5 mb-3">Histórico de Registros</h2>
            <div class="history-list">
                <?php foreach ($registros as $registro): ?>
                <div class="history-item">
                    <div class="history-date">
                        <?php echo date('d/m/Y', strtotime($registro['data_registro'])); ?>
                    </div>
                    <div class="history-content">
                        <div class="history-mood">
                            <i class="bi bi-emoji-smile"></i>
                            Humor: <?php echo $registro['humor']; ?>/5
                        </div>
                        <div class="history-energy">
                            <i class="bi bi-lightning"></i>
                            Energia: <?php echo ucfirst($registro['energia']); ?>
                        </div>
                        <?php if ($registro['texto']): ?>
                        <div class="history-note">
                            <i class="bi bi-chat-quote"></i>
                            <?php echo htmlspecialchars($registro['texto']); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <!-- Barra de Navegação Inferior -->
    <nav class="bottom-nav">
        <a href="dashboard.php" class="nav-item">
            <i class="bi bi-house-door"></i>
            <span>Início</span>
        </a>
        <a href="diario.php" class="nav-item">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html> 