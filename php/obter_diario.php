<?php
require_once 'conexao.php';
session_start();

// Desabilitar exibição de erros
error_reporting(0);
ini_set('display_errors', 0);

// Definir cabeçalho para JSON
header('Content-Type: application/json');

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não está logado']);
    exit;
}

try {
    // Buscar o registro do diário para hoje
    $stmt = $conn->prepare("
        SELECT * FROM diario 
        WHERE usuario_id = ? 
        AND data_registro = CURDATE()
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $diario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($diario) {
        echo json_encode(['success' => true, 'diario' => $diario]);
    } else {
        echo json_encode(['success' => true, 'diario' => null]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao buscar diário: ' . $e->getMessage()]);
} 