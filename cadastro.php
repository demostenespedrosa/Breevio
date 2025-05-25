<?php
require_once 'php/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    $cigarros_por_dia = filter_input(INPUT_POST, 'cigarros_por_dia', FILTER_VALIDATE_INT);
    $preco_carteira = filter_input(INPUT_POST, 'preco_carteira', FILTER_VALIDATE_FLOAT);
    $data_parar = $_POST['data_parar'];
    $hora_parar = $_POST['hora_parar'];

    if ($nome && $email && $senha && $cigarros_por_dia && $preco_carteira && $data_parar && $hora_parar) {
        try {
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, cigarros_por_dia, preco_carteira, data_parar, hora_parar) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt->execute([$nome, $email, $senha_hash, $cigarros_por_dia, $preco_carteira, $data_parar, $hora_parar]);
            
            $_SESSION['usuario_id'] = $conn->lastInsertId();
            $_SESSION['usuario_nome'] = $nome;
            
            header("Location: dashboard.php");
            exit();
        } catch (PDOException $e) {
            $erro = "Erro ao cadastrar: " . $e->getMessage();
        }
    } else {
        $erro = "Por favor, preencha todos os campos corretamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Parar de Fumar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">Criar Conta</h2>
                        
                        <?php if (isset($erro)): ?>
                            <div class="alert alert-danger"><?php echo $erro; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="senha" name="senha" required>
                            </div>

                            <div class="mb-3">
                                <label for="cigarros_por_dia" class="form-label">Cigarros por Dia</label>
                                <input type="number" class="form-control" id="cigarros_por_dia" name="cigarros_por_dia" min="1" required>
                            </div>

                            <div class="mb-3">
                                <label for="preco_carteira" class="form-label">Preço da Carteira (R$)</label>
                                <input type="number" class="form-control" id="preco_carteira" name="preco_carteira" step="0.01" min="0" required>
                            </div>

                            <div class="mb-4">
                                <label for="data_parar" class="form-label">Data que Parou de Fumar</label>
                                <input type="date" class="form-control" id="data_parar" name="data_parar" required>
                            </div>

                            <div class="mb-4">
                                <label for="hora_parar" class="form-label">Hora que Parou de Fumar</label>
                                <input type="time" class="form-control" id="hora_parar" name="hora_parar" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Criar Conta</button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="login.php" class="text-decoration-none">Já tem uma conta? Faça login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html> 