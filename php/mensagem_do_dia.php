<?php
// Desabilitar exibição de erros
error_reporting(0);
ini_set('display_errors', 0);

// Garantir que a saída será JSON
header('Content-Type: application/json');

// Iniciar buffer de saída para capturar possíveis erros
ob_start();

try {
    require_once 'conexao.php';
    session_start();

    // Verificar se o usuário está logado
    if (!isset($_SESSION['usuario_id'])) {
        throw new Exception('Usuário não está logado');
    }

    // Buscar a última mensagem exibida para o usuário
    $stmt = $conn->prepare("
        SELECT mensagem_id 
        FROM ultima_mensagem 
        WHERE usuario_id = ? 
        AND data_exibicao = CURDATE()
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $ultima_mensagem = $stmt->fetch();

    if ($ultima_mensagem) {
        // Se já existe uma mensagem para hoje, retornar ela
        $stmt = $conn->prepare("SELECT texto FROM mensagens_motivacionais WHERE id = ?");
        $stmt->execute([$ultima_mensagem['mensagem_id']]);
        $mensagem = $stmt->fetch();
        echo json_encode(['success' => true, 'mensagem' => $mensagem['texto']]);
    } else {
        // Buscar uma mensagem aleatória que não foi exibida hoje
        $stmt = $conn->prepare("
            SELECT id, texto 
            FROM mensagens_motivacionais 
            WHERE id NOT IN (
                SELECT mensagem_id 
                FROM ultima_mensagem 
                WHERE usuario_id = ? 
                AND data_exibicao = CURDATE()
            )
            ORDER BY RAND() 
            LIMIT 1
        ");
        $stmt->execute([$_SESSION['usuario_id']]);
        $mensagem = $stmt->fetch();

        if ($mensagem) {
            // Registrar a mensagem escolhida
            $stmt = $conn->prepare("
                INSERT INTO ultima_mensagem (usuario_id, mensagem_id, data_exibicao) 
                VALUES (?, ?, CURDATE())
            ");
            $stmt->execute([$_SESSION['usuario_id'], $mensagem['id']]);
            
            echo json_encode(['success' => true, 'mensagem' => $mensagem['texto']]);
        } else {
            // Se não houver mensagens disponíveis, buscar qualquer uma
            $stmt = $conn->prepare("SELECT texto FROM mensagens_motivacionais ORDER BY RAND() LIMIT 1");
            $stmt->execute();
            $mensagem = $stmt->fetch();
            echo json_encode(['success' => true, 'mensagem' => $mensagem['texto']]);
        }
    }
} catch (Exception $e) {
    // Limpar qualquer saída anterior
    ob_clean();
    
    // Registrar o erro no log
    error_log('Erro em mensagem_do_dia.php: ' . $e->getMessage());
    
    // Retornar erro em formato JSON
    echo json_encode(['success' => false, 'message' => 'Erro ao buscar mensagem: ' . $e->getMessage()]);
}

// Enviar a saída e limpar o buffer
ob_end_flush();
?> 