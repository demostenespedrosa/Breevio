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

// Verificar se todos os campos obrigatórios foram enviados
if (!isset($_POST['humor']) || !isset($_POST['gatilhos']) || !isset($_POST['energia']) || !isset($_POST['conquista'])) {
    echo json_encode(['success' => false, 'message' => 'Campos obrigatórios não preenchidos']);
    exit;
}

try {
    // Preparar os dados
    $usuario_id = $_SESSION['usuario_id'];
    $data_registro = date('Y-m-d');
    $humor = intval($_POST['humor']);
    $gatilhos = $_POST['gatilhos'];
    $energia = $_POST['energia'];
    $conquista = $_POST['conquista'];
    $texto_opcional = isset($_POST['texto_opcional']) ? $_POST['texto_opcional'] : '';

    // Verificar se já existe um registro para hoje
    $stmt = $conn->prepare("SELECT id FROM diario WHERE usuario_id = ? AND data_registro = ?");
    $stmt->execute([$usuario_id, $data_registro]);
    $registro_existente = $stmt->fetch();

    if ($registro_existente) {
        // Atualizar registro existente
        $stmt = $conn->prepare("
            UPDATE diario 
            SET humor = ?, gatilhos = ?, energia = ?, conquista = ?, texto_opcional = ?
            WHERE id = ?
        ");
        $stmt->execute([$humor, $gatilhos, $energia, $conquista, $texto_opcional, $registro_existente['id']]);
    } else {
        // Inserir novo registro
        $stmt = $conn->prepare("
            INSERT INTO diario (usuario_id, data_registro, humor, gatilhos, energia, conquista, texto_opcional)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$usuario_id, $data_registro, $humor, $gatilhos, $energia, $conquista, $texto_opcional]);
    }

    echo json_encode(['success' => true, 'message' => 'Diário salvo com sucesso']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar diário: ' . $e->getMessage()]);
} 