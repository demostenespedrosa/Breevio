<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

// Obter dados do corpo da requisição
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validar dados
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

// Validar campos obrigatórios
$campos_obrigatorios = ['humor', 'gatilhos', 'energia', 'conquista'];
foreach ($campos_obrigatorios as $campo) {
    if (!isset($data[$campo]) || empty($data[$campo])) {
        echo json_encode(['success' => false, 'message' => "Campo obrigatório não preenchido: $campo"]);
        exit;
    }
}

try {
    // Preparar dados
    $usuario_id = $_SESSION['usuario_id'];
    $humor = $data['humor'];
    $gatilhos = implode(',', $data['gatilhos']);
    $energia = $data['energia'];
    $conquista = $data['conquista'];
    $texto = isset($data['texto']) ? $data['texto'] : '';
    $data_registro = date('Y-m-d H:i:s');

    // Verificar se já existe registro para hoje
    $stmt = $pdo->prepare("SELECT id FROM diario WHERE usuario_id = ? AND DATE(data_registro) = CURDATE()");
    $stmt->execute([$usuario_id]);
    $registro_existente = $stmt->fetch();

    if ($registro_existente) {
        // Atualizar registro existente
        $stmt = $pdo->prepare("
            UPDATE diario 
            SET humor = ?, gatilhos = ?, energia = ?, conquista = ?, texto = ?, data_registro = ?
            WHERE id = ?
        ");
        $stmt->execute([$humor, $gatilhos, $energia, $conquista, $texto, $data_registro, $registro_existente['id']]);
    } else {
        // Inserir novo registro
        $stmt = $pdo->prepare("
            INSERT INTO diario (usuario_id, humor, gatilhos, energia, conquista, texto, data_registro)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$usuario_id, $humor, $gatilhos, $energia, $conquista, $texto, $data_registro]);
    }

    echo json_encode(['success' => true, 'message' => 'Diário salvo com sucesso']);

} catch (PDOException $e) {
    error_log("Erro ao salvar diário: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar diário']);
} 