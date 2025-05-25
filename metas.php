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
$dias_sem_fumar = $intervalo->days + ($intervalo->h / 24) + ($intervalo->i / 1440);

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

// Buscar todas as metas do banco de dados
$stmt = $conn->prepare("
    SELECT m.*, 
           CASE 
               WHEN ? >= m.dias THEN 'conquista-alcancada'
               ELSE 'conquista-pendente'
           END as status
    FROM metas m
    ORDER BY m.dias ASC
");
$stmt->execute([$dias_sem_fumar]);
$metas = $stmt->fetchAll();

// Agrupar metas por categoria
$categorias = [
    'Primeiras Horas' => [],
    'Primeiro Dia' => [],
    'Primeira Semana' => [],
    'Duas Semanas' => [],
    'Um Mês' => [],
    'Dois Meses' => [],
    'Três Meses' => [],
    'Seis Meses' => [],
    'Um Ano' => [],
    'Metas de Quantidade' => [],
    'Metas de Economia' => [],
    'Metas de Saúde' => [],
    'Metas de Bem-estar' => [],
    'Metas de Superação' => [],
    'Metas de Estilo de Vida' => [],
    'Metas de Tempo' => [],
    'Metas de Superação Pessoal' => [],
    'Metas de Qualidade de Vida' => [],
    'Metas de Relacionamento' => [],
    'Metas de Ambiente' => [],
    'Metas de Saúde Mental' => [],
    'Metas de Produtividade' => [],
    'Metas de Longevidade' => [],
    'Metas de Superação Final' => []
];

// Função para formatar dias em texto amigável
function formatarTempoMeta($dias) {
    $diasInt = floor($dias);
    $horas = floor(($dias - $diasInt) * 24);
    $minutos = floor((($dias - $diasInt) * 24 - $horas) * 60);
    
    $texto = '';
    if ($diasInt > 0) {
        $texto .= $diasInt . ' ' . ($diasInt === 1 ? 'dia' : 'dias');
        if ($horas > 0 || $minutos > 0) {
            $texto .= ', ';
        }
    }
    if ($horas > 0) {
        $texto .= $horas . ' ' . ($horas === 1 ? 'hora' : 'horas');
        if ($minutos > 0) {
            $texto .= ' e ';
        }
    }
    if ($minutos > 0) {
        $texto .= $minutos . ' ' . ($minutos === 1 ? 'minuto' : 'minutos');
    }
    return $texto;
}

// Organizar metas em categorias
foreach ($metas as $meta) {
    $meta['tempo_formatado'] = formatarTempoMeta($meta['dias']);
    
    if ($meta['dias'] <= 0.208) {
        $categorias['Primeiras Horas'][] = $meta;
    } elseif ($meta['dias'] <= 1.5) {
        $categorias['Primeiro Dia'][] = $meta;
    } elseif ($meta['dias'] <= 7.5) {
        $categorias['Primeira Semana'][] = $meta;
    } elseif ($meta['dias'] <= 15) {
        $categorias['Duas Semanas'][] = $meta;
    } elseif ($meta['dias'] <= 31) {
        $categorias['Um Mês'][] = $meta;
    } elseif ($meta['dias'] <= 61) {
        $categorias['Dois Meses'][] = $meta;
    } elseif ($meta['dias'] <= 91) {
        $categorias['Três Meses'][] = $meta;
    } elseif ($meta['dias'] <= 181) {
        $categorias['Seis Meses'][] = $meta;
    } elseif ($meta['dias'] <= 366) {
        $categorias['Um Ano'][] = $meta;
    } elseif (strpos($meta['titulo'], 'Milhar') !== false || strpos($meta['titulo'], 'Mil') !== false) {
        $categorias['Metas de Quantidade'][] = $meta;
    } elseif (strpos($meta['titulo'], 'Economia') !== false || strpos($meta['titulo'], 'Carteira') !== false) {
        $categorias['Metas de Economia'][] = $meta;
    } elseif (strpos($meta['titulo'], 'Pulmão') !== false || strpos($meta['titulo'], 'Coração') !== false) {
        $categorias['Metas de Saúde'][] = $meta;
    } elseif (strpos($meta['titulo'], 'Olfato') !== false || strpos($meta['titulo'], 'Paladar') !== false) {
        $categorias['Metas de Bem-estar'][] = $meta;
    } elseif (strpos($meta['titulo'], 'Desafio') !== false || strpos($meta['titulo'], 'Força') !== false) {
        $categorias['Metas de Superação'][] = $meta;
    } elseif (strpos($meta['titulo'], 'Energia') !== false || strpos($meta['titulo'], 'Pele') !== false) {
        $categorias['Metas de Estilo de Vida'][] = $meta;
    } elseif (strpos($meta['titulo'], 'Hora') !== false || strpos($meta['titulo'], 'Dia') !== false) {
        $categorias['Metas de Tempo'][] = $meta;
    } elseif (strpos($meta['titulo'], 'Autocontrole') !== false || strpos($meta['titulo'], 'Disciplina') !== false) {
        $categorias['Metas de Superação Pessoal'][] = $meta;
    } elseif (strpos($meta['titulo'], 'Sono') !== false || strpos($meta['titulo'], 'Exercícios') !== false) {
        $categorias['Metas de Qualidade de Vida'][] = $meta;
    } elseif (strpos($meta['titulo'], 'Família') !== false || strpos($meta['titulo'], 'Amigos') !== false) {
        $categorias['Metas de Relacionamento'][] = $meta;
    } elseif (strpos($meta['titulo'], 'Ar') !== false || strpos($meta['titulo'], 'Natureza') !== false) {
        $categorias['Metas de Ambiente'][] = $meta;
    } elseif (strpos($meta['titulo'], 'Mente') !== false || strpos($meta['titulo'], 'Estresse') !== false) {
        $categorias['Metas de Saúde Mental'][] = $meta;
    } elseif (strpos($meta['titulo'], 'Foco') !== false || strpos($meta['titulo'], 'Produtividade') !== false) {
        $categorias['Metas de Produtividade'][] = $meta;
    } elseif (strpos($meta['titulo'], 'Vida') !== false || strpos($meta['titulo'], 'Qualidade') !== false) {
        $categorias['Metas de Longevidade'][] = $meta;
    } else {
        $categorias['Metas de Superação Final'][] = $meta;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Metas - Parar de Fumar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/metas.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Header Fixo -->
    <header class="app-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0">Suas Metas</h1>
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
                <h2>Seu Progresso</h2>
                <div class="progress-time">
                    <span><?php echo $tempo_texto; ?></span>
                </div>
                <p class="progress-subtitle">sem fumar</p>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar" role="progressbar" style="width: <?php echo min(($dias_sem_fumar / 30) * 100, 100); ?>%"></div>
            </div>
            <p class="progress-goal">Meta: 30 dias</p>
        </div>

        <!-- Menu de Categorias -->
        <div class="categories-menu">
            <button class="category-item active" data-category="todas">
                <i class="bi bi-grid"></i>
                <span>Todas</span>
            </button>
            <button class="category-item" data-category="proximas">
                <i class="bi bi-clock"></i>
                <span>Próximas</span>
            </button>
            <button class="category-item" data-category="alcancadas">
                <i class="bi bi-trophy"></i>
                <span>Conquistadas</span>
            </button>
        </div>

        <!-- Lista de Metas -->
        <div class="goals-container">
            <?php foreach ($categorias as $categoria => $metas_categoria): ?>
                <?php if (!empty($metas_categoria)): ?>
                <section class="goals-section" data-category="<?php echo strtolower(str_replace(' ', '-', $categoria)); ?>">
                    <div class="section-header">
                        <h3 class="section-title"><?php echo $categoria; ?></h3>
                        <span class="section-count"><?php echo count($metas_categoria); ?></span>
                    </div>
                    <div class="goals-grid">
                        <?php foreach ($metas_categoria as $meta): ?>
                        <div class="goal-card <?php echo $meta['status']; ?>" 
                             data-dias="<?php echo $meta['dias']; ?>"
                             data-titulo="<?php echo htmlspecialchars($meta['titulo']); ?>"
                             data-descricao="<?php echo htmlspecialchars($meta['descricao']); ?>"
                             data-icone="<?php echo $meta['icone']; ?>">
                            <div class="goal-icon">
                                <i class="bi <?php echo $meta['icone']; ?>"></i>
                            </div>
                            <div class="goal-content">
                                <h4><?php echo $meta['titulo']; ?></h4>
                                <p><?php echo $meta['descricao']; ?></p>
                                <div class="goal-progress">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo min(($dias_sem_fumar / $meta['dias']) * 100, 100); ?>%"></div>
                                </div>
                                <div class="goal-time">
                                    <span class="current-time"><?php echo $tempo_texto; ?></span>
                                    <span class="separator">de</span>
                                    <span class="target-time"><?php echo $meta['tempo_formatado']; ?></span>
                                </div>
                            </div>
                            <?php if ($meta['status'] == 'conquista-alcancada'): ?>
                            <div class="goal-badge">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Barra de Navegação Inferior -->
    <nav class="bottom-nav">
        <a href="dashboard.php" class="nav-item">
            <i class="bi bi-house-door"></i>
            <span>Início</span>
        </a>
        <a href="tela_diario.php" class="nav-item">
            <i class="bi bi-journal-text"></i>
            <span>Diário</span>
        </a>
        <a href="metas.php" class="nav-item active">
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
    <script>
        // Navegação entre categorias
        document.querySelectorAll('.category-item').forEach(item => {
            item.addEventListener('click', () => {
                // Remove active de todos os itens
                document.querySelectorAll('.category-item').forEach(i => i.classList.remove('active'));
                
                // Adiciona active no item clicado
                item.classList.add('active');
                
                const category = item.dataset.category;
                const sections = document.querySelectorAll('.goals-section');
                
                sections.forEach(section => {
                    if (category === 'todas') {
                        section.style.display = 'block';
                    } else if (category === 'proximas') {
                        const hasPendingGoals = section.querySelector('.conquista-pendente');
                        section.style.display = hasPendingGoals ? 'block' : 'none';
                    } else if (category === 'alcancadas') {
                        const hasAchievedGoals = section.querySelector('.conquista-alcancada');
                        section.style.display = hasAchievedGoals ? 'block' : 'none';
                    }
                });
            });
        });

        // Atualizar tempo em tempo real
        function atualizarTempo() {
            const dataParar = new Date('<?php echo $usuario['data_parar']; ?> <?php echo $usuario['hora_parar']; ?>');
            const agora = new Date();
            const diff = agora - dataParar;
            
            const dias = Math.floor(diff / (1000 * 60 * 60 * 24));
            const horas = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutos = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            
            let tempoTexto = '';
            if (dias > 0) {
                tempoTexto += dias + ' ' + (dias === 1 ? 'dia' : 'dias');
                if (horas > 0 || minutos > 0) {
                    tempoTexto += ', ';
                }
            }
            if (horas > 0) {
                tempoTexto += horas + ' ' + (horas === 1 ? 'hora' : 'horas');
                if (minutos > 0) {
                    tempoTexto += ' e ';
                }
            }
            if (minutos > 0) {
                tempoTexto += minutos + ' ' + (minutos === 1 ? 'minuto' : 'minutos');
            }
            
            document.querySelector('.progress-time span').textContent = tempoTexto;
            document.querySelectorAll('.current-time').forEach(el => {
                el.textContent = tempoTexto;
            });
        }

        // Atualizar a cada minuto
        setInterval(atualizarTempo, 60000);
        atualizarTempo();
    </script>
</body>
</html> 