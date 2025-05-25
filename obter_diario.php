<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

try {
    // Obter registro do dia
    $stmt = $pdo->prepare("
        SELECT * FROM diario 
        WHERE usuario_id = ? AND DATE(data_registro) = CURDATE()
        ORDER BY data_registro DESC 
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $diario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obter histórico dos últimos 30 dias
    $stmt = $pdo->prepare("
        SELECT * FROM diario 
        WHERE usuario_id = ? 
        AND data_registro >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        ORDER BY data_registro DESC
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $historico = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Processar dados para o formato esperado
    if ($diario) {
        $diario['gatilhos'] = explode(',', $diario['gatilhos']);
    }

    foreach ($historico as &$registro) {
        $registro['gatilhos'] = explode(',', $registro['gatilhos']);
    }

    // Calcular estatísticas
    $total_registros = count($historico);
    $gatilhos_frequentes = [];
    $conquistas_frequentes = [];

    foreach ($historico as $registro) {
        // Contar gatilhos
        foreach ($registro['gatilhos'] as $gatilho) {
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

    // Ordenar por frequência
    arsort($gatilhos_frequentes);
    arsort($conquistas_frequentes);

    echo json_encode([
        'success' => true,
        'diario' => $diario,
        'historico' => $historico,
        'total_registros' => $total_registros,
        'gatilhos_frequentes' => $gatilhos_frequentes,
        'conquistas_frequentes' => $conquistas_frequentes
    ]);

} catch (PDOException $e) {
    error_log("Erro ao obter diário: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro ao obter dados do diário']);
} 