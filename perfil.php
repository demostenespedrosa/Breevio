<?php
require_once 'php/conexao.php';
verificarLogin();

// Buscar dados do usuário
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

// Calcular dias sem fumar
$data_parar = new DateTime($usuario['data_parar'] . ' ' . $usuario['hora_parar']);
$hoje = new DateTime();
$intervalo = $hoje->diff($data_parar);
$dias_sem_fumar = $intervalo->days;

// Processar atualização do perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['atualizar_perfil'])) {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $cigarros_por_dia = filter_input(INPUT_POST, 'cigarros_por_dia', FILTER_VALIDATE_INT);
        $preco_carteira = filter_input(INPUT_POST, 'preco_carteira', FILTER_VALIDATE_FLOAT);
        $data_parar = $_POST['data_parar'];
        $hora_parar = $_POST['hora_parar'];

        if ($nome && $cigarros_por_dia && $preco_carteira && $data_parar && $hora_parar) {
            try {
                $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, cigarros_por_dia = ?, preco_carteira = ?, data_parar = ?, hora_parar = ? WHERE id = ?");
                $stmt->execute([$nome, $cigarros_por_dia, $preco_carteira, $data_parar, $hora_parar, $_SESSION['usuario_id']]);
                $sucesso = "Perfil atualizado com sucesso!";
                
                // Atualizar dados da sessão
                $_SESSION['usuario_nome'] = $nome;
                
                // Recarregar dados do usuário
                $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
                $stmt->execute([$_SESSION['usuario_id']]);
                $usuario = $stmt->fetch();
            } catch (PDOException $e) {
                $erro = "Erro ao atualizar perfil: " . $e->getMessage();
            }
        } else {
            $erro = "Por favor, preencha todos os campos corretamente.";
        }
    } elseif (isset($_POST['alterar_senha'])) {
        $senha_atual = $_POST['senha_atual'];
        $nova_senha = $_POST['nova_senha'];
        $confirmar_senha = $_POST['confirmar_senha'];

        if ($senha_atual && $nova_senha && $confirmar_senha) {
            if ($nova_senha === $confirmar_senha) {
                if (password_verify($senha_atual, $usuario['senha'])) {
                    try {
                        $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
                        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                        $stmt->execute([$senha_hash, $_SESSION['usuario_id']]);
                        $sucesso = "Senha alterada com sucesso!";
                    } catch (PDOException $e) {
                        $erro = "Erro ao alterar senha: " . $e->getMessage();
                    }
                } else {
                    $erro = "Senha atual incorreta.";
                }
            } else {
                $erro = "As senhas não coincidem.";
            }
        } else {
            $erro = "Por favor, preencha todos os campos.";
        }
    } elseif (isset($_POST['registrar_recaida'])) {
        $motivo = filter_input(INPUT_POST, 'motivo', FILTER_SANITIZE_STRING);
        
        if ($motivo) {
            try {
                // Iniciar transação
                $conn->beginTransaction();
                
                // Registrar a recaída
                $stmt = $conn->prepare("INSERT INTO recaidas (usuario_id, data_recaida, motivo) VALUES (?, NOW(), ?)");
                $stmt->execute([$_SESSION['usuario_id'], $motivo]);
                
                // Atualizar data e hora de parar
                $stmt = $conn->prepare("UPDATE usuarios SET data_parar = CURDATE(), hora_parar = CURTIME() WHERE id = ?");
                $stmt->execute([$_SESSION['usuario_id']]);
                
                // Confirmar transação
                $conn->commit();
                
                $sucesso = "Recaída registrada. A contagem foi reiniciada.";
                
                // Recarregar dados do usuário
                $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
                $stmt->execute([$_SESSION['usuario_id']]);
                $usuario = $stmt->fetch();
            } catch (PDOException $e) {
                // Reverter transação em caso de erro
                $conn->rollBack();
                $erro = "Erro ao registrar recaída: " . $e->getMessage();
            }
        } else {
            $erro = "Por favor, informe o motivo da recaída.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Perfil - Parar de Fumar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/perfil.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Header Fixo -->
    <header class="app-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0">Meu Perfil</h1>
                <button id="theme-toggle" class="btn btn-icon" aria-label="Alternar tema">
                    <i class="bi bi-moon-stars"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <main class="app-content">
        <?php if (isset($sucesso)): ?>
            <div class="alert alert-success"><?php echo $sucesso; ?></div>
        <?php endif; ?>

        <?php if (isset($erro)): ?>
            <div class="alert alert-danger"><?php echo $erro; ?></div>
        <?php endif; ?>

        

        <!-- Menu de Opções -->
        <div class="profile-menu" style="margin-top: 20px;">
            <button class="menu-item active" data-section="dados">
                <i class="bi bi-person"></i>
                <span>Dados Pessoais</span>
            </button>
            <button class="menu-item" data-section="senha">
                <i class="bi bi-key"></i>
                <span>Alterar Senha</span>
            </button>
            <button class="menu-item" data-section="recaidas">
                <i class="bi bi-calendar-x"></i>
                <span>Recaídas</span>
            </button>
        </div>

        <!-- Seção de Dados Pessoais -->
        <section id="dados" class="profile-section active">
            <form method="POST" action="" class="profile-form">
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="cigarros_por_dia">Cigarros por Dia</label>
                    <input type="number" class="form-control" id="cigarros_por_dia" name="cigarros_por_dia" value="<?php echo $usuario['cigarros_por_dia']; ?>" min="1" required>
                </div>

                <div class="form-group">
                    <label for="preco_carteira">Preço da Carteira (R$)</label>
                    <input type="number" class="form-control" id="preco_carteira" name="preco_carteira" value="<?php echo $usuario['preco_carteira']; ?>" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label for="data_parar">Data que Parou de Fumar</label>
                    <input type="date" class="form-control" id="data_parar" name="data_parar" value="<?php echo $usuario['data_parar']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="hora_parar">Hora que Parou de Fumar</label>
                    <input type="time" class="form-control" id="hora_parar" name="hora_parar" value="<?php echo $usuario['hora_parar']; ?>" required>
                </div>

                <button type="submit" name="atualizar_perfil" class="btn btn-primary w-100">Atualizar Perfil</button>
            </form>
        </section>

        <!-- Seção de Alterar Senha -->
        <section id="senha" class="profile-section">
            <form method="POST" action="" class="profile-form">
                <div class="form-group">
                    <label for="senha_atual">Senha Atual</label>
                    <input type="password" class="form-control" id="senha_atual" name="senha_atual" required>
                </div>

                <div class="form-group">
                    <label for="nova_senha">Nova Senha</label>
                    <input type="password" class="form-control" id="nova_senha" name="nova_senha" required>
                </div>

                <div class="form-group">
                    <label for="confirmar_senha">Confirmar Nova Senha</label>
                    <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
                </div>

                <button type="submit" name="alterar_senha" class="btn btn-primary w-100">Alterar Senha</button>
            </form>
        </section>

        <!-- Seção de Recaídas -->
        <section id="recaidas" class="profile-section">
            <div class="section-header">
                <h2>Histórico de Recaídas</h2>
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#recaidaModal">
                    <i class="bi bi-plus-lg"></i> Nova Recaída
                </button>
            </div>

            <?php
            $stmt = $conn->prepare("
                SELECT id, data_recaida, motivo 
                FROM recaidas 
                WHERE usuario_id = ? 
                ORDER BY data_recaida DESC, id DESC
            ");
            $stmt->execute([$_SESSION['usuario_id']]);
            $recaidas = $stmt->fetchAll();

            if (count($recaidas) > 0): ?>
                <div class="relapse-list">
                    <?php foreach ($recaidas as $recaida): ?>
                        <div class="relapse-item">
                            <div class="relapse-date">
                                <i class="bi bi-calendar"></i>
                                <?php echo date('d/m/Y', strtotime($recaida['data_recaida'])); ?>
                            </div>
                            <div class="relapse-reason">
                                <?php echo htmlspecialchars($recaida['motivo']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">Nenhuma recaída registrada.</p>
            <?php endif; ?>
        </section>
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
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="motivo">Motivo da Recaída</label>
                            <textarea class="form-control" id="motivo" name="motivo" rows="3" required></textarea>
                        </div>
                        <button type="submit" name="registrar_recaida" class="btn btn-primary w-100">Registrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
        <a href="metas.php" class="nav-item">
            <i class="bi bi-trophy"></i>
            <span>Metas</span>
        </a>
        <a href="perfil.php" class="nav-item active">
            <i class="bi bi-person"></i>
            <span>Perfil</span>
        </a>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
    <script>
        // Navegação entre seções
        document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', () => {
                // Remove active de todos os itens
                document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
                document.querySelectorAll('.profile-section').forEach(s => s.classList.remove('active'));
                
                // Adiciona active no item clicado
                item.classList.add('active');
                const section = item.dataset.section;
                document.getElementById(section).classList.add('active');
            });
        });
    </script>
</body>
</html> 