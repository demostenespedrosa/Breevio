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
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
    exit;
}

try {
    // Verificar se a recaída pertence ao usuário
    $stmt = $conn->prepare("SELECT id FROM recaidas WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$data['id'], $_SESSION['usuario_id']]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Recaída não encontrada']);
        exit;
    }

    // Excluir a recaída
    $stmt = $conn->prepare("DELETE FROM recaidas WHERE id = ?");
    $stmt->execute([$data['id']]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao excluir recaída: ' . $e->getMessage()]);
} 