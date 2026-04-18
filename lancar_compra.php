<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { exit(); }

require_once 'config.php';
$conexao = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_id    = $_SESSION['usuario_id'];
    $descricao     = $conexao->real_escape_string($_POST['descricao']);
    $valor_input   = (float)$_POST['valor'];
    $tipo          = $_POST['tipo']; 
    $categoria     = $_POST['categoria'];
    $id_cartao     = !empty($_POST['id_cartao']) ? $_POST['id_cartao'] : "NULL";
    $data_primeira = $_POST['data_referencia'];
    $metodo_valor  = $_POST['metodo_valor'] ?? 'total';
    
    $qtd_parcelas  = (!empty($_POST['parcelas']) && (int)$_POST['parcelas'] > 0) ? (int)$_POST['parcelas'] : 1;

    // Lógica: se for 'parcela', repete o valor. Se for 'total', divide.
    $valor_final_parcela = ($metodo_valor == 'parcela') ? $valor_input : ($valor_input / $qtd_parcelas);

    for ($i = 0; $i < $qtd_parcelas; $i++) {
        $parcela_atual = $i + 1;
        $data_obj = new DateTime($data_primeira);
        if ($i > 0) { $data_obj->modify("+$i month"); }
        
        $data_formatada = $data_obj->format('Y-m-d H:i:s');

        // Adicionei o campo `date` aqui para evitar o Fatal Error
        $sql = "INSERT INTO transaction 
                (description, amount, type, referenceDate, `date`, cardId, category, installmentCurrent, installmentTotal, userId, createdAt) 
                VALUES 
                ('$descricao', $valor_final_parcela, '$tipo', '$data_formatada', '$data_formatada', $id_cartao, '$categoria', $parcela_atual, $qtd_parcelas, $usuario_id, NOW())";
        
        if (!$conexao->query($sql)) {
            die("Erro no banco: " . $conexao->error);
        }
    }

    header("Location: index.php?sucesso=1");
    exit();
}