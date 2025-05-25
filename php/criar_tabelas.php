<?php
require_once 'conexao.php';

try {
    // Ler o arquivo SQL
    $sql = file_get_contents('../sql/tabelas.sql');
    
    // Executar o SQL
    $conn->exec($sql);
    
    echo "Tabelas criadas com sucesso!";
} catch(PDOException $e) {
    die("Erro ao criar tabelas: " . $e->getMessage());
}
?> 