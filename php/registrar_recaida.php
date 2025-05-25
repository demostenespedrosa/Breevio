<?php
// Desabilitar exibição de erros antes de qualquer coisa
error_reporting(0);
ini_set('display_errors', 0);

// Garantir que a saída será JSON
header('Content-Type: application/json');

require_once 'conexao.php';
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não está logado']);
    exit;
}

// Receber dados do POST
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log para debug
error_log('Dados recebidos: ' . print_r($data, true));

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos: ' . json_last_error_msg()]);
    exit;
}

if (empty($data['data_recaida']) || empty($data['hora_recaida']) || empty($data['motivo'])) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos. Preencha todos os campos.']);
    exit;
}

try {
    // Iniciar transação
    $conn->beginTransaction();

    // Combinar data e hora
    $data_hora = $data['data_recaida'] . ' ' . $data['hora_recaida'];

    // Registrar a recaída
    $stmt = $conn->prepare("INSERT INTO recaidas (usuario_id, data_recaida, motivo) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['usuario_id'], $data_hora, $data['motivo']]);

    // Atualizar a data e hora de parar do usuário
    $stmt = $conn->prepare("UPDATE usuarios SET data_parar = ?, hora_parar = ? WHERE id = ?");
    $stmt->execute([$data['data_recaida'], $data['hora_recaida'], $_SESSION['usuario_id']]);

    // Confirmar transação
    $conn->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Reverter transação em caso de erro
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Erro ao registrar recaída: ' . $e->getMessage()]);
}
?> 