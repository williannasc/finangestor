<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit(); }

require_once 'config.php';
$conexao = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$usuario_id = $_SESSION['usuario_id'];
$mensagem = "";

// 1. Lógica de Ações (Adicionar / Excluir / Atualizar Limite)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_card'])) {
        $name = $conexao->real_escape_string($_POST['name']);
        $limit = $_POST['limit'];
        $closing = $_POST['closingDay'];
        $due = $_POST['dueDay'];
        $conexao->query("INSERT INTO card (name, `limit`, closingDay, dueDay, userId) VALUES ('$name', '$limit', '$closing', '$due', '$usuario_id')");
        $mensagem = "Cartão adicionado!";
    }
    
    if (isset($_POST['update_limit'])) {
        $card_id = $_POST['card_id'];
        $new_limit = $_POST['new_limit'];
        $conexao->query("UPDATE card SET `limit` = '$new_limit' WHERE id = $card_id AND userId = $usuario_id");
        $mensagem = "Limite atualizado!";
    }

    if (isset($_POST['delete_card'])) {
        $card_id = $_POST['card_id'];
        $conexao->query("DELETE FROM card WHERE id = $card_id AND userId = $usuario_id");
        $mensagem = "Cartão removido!";
    }
}

$cartoes = $conexao->query("SELECT * FROM card WHERE userId = $usuario_id");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinanGestor | Cartões</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        
    </style>
</head>
<body class="font-sans antialiased flex">

    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-4 md:p-10 max-w-6xl mx-auto w-full">
        <header class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-black flex items-center gap-3 tracking-tighter">
                <span class="material-icons text-blue-400 text-4xl">credit_card</span> GERENCIAR CARTÕES
            </h1>
            <button onclick="document.getElementById('modalNovoCartao').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-500 px-6 py-3 rounded-2xl font-bold flex items-center gap-2 transition-all shadow-lg shadow-blue-900/40">
                <span class="material-icons text-sm">add</span> NOVO CARTÃO
            </button>
        </header>

        <?php if($mensagem): ?>
            <div class="glass p-4 mb-8 border-blue-500/30 bg-blue-500/10 text-blue-200 font-bold"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while($c = $cartoes->fetch_assoc()): ?>
            <div class="glass p-6 flex flex-col justify-between group">
                <div>
                    <div class="flex justify-between items-start mb-6">
                        <span class="font-black text-lg tracking-widest opacity-80"><?php echo strtoupper($c['name']); ?></span>
                        <span class="material-icons opacity-20">contactless</span>
                    </div>
                    
                    <form action="" method="POST" class="space-y-4">
                        <input type="hidden" name="card_id" value="<?php echo $c['id']; ?>">
                        <div>
                            <label class="text-[10px] uppercase font-black opacity-30">Limite Total (R$)</label>
                            <input type="number" name="new_limit" value="<?php echo $c['limit']; ?>" class="w-full bg-white/5 border border-white/10 rounded-xl p-3 mt-1 font-mono font-bold outline-none focus:ring-1 focus:ring-blue-500/50">
                        </div>
                        <div class="flex justify-between text-[10px] font-bold opacity-40 uppercase">
                            <span>Fecha dia: <?php echo $c['closingDay']; ?></span>
                            <span>Vence dia: <?php echo $c['dueDay']; ?></span>
                        </div>
                        <div class="flex gap-2 pt-4">
                            <button type="submit" name="update_limit" class="flex-1 bg-white/5 hover:bg-white/10 py-2 rounded-lg text-[10px] font-black transition-all">ATUALIZAR LIMITE</button>
                            <button type="submit" name="delete_card" onclick="return confirm('Apagar cartão?')" class="px-3 bg-red-500/10 hover:bg-red-500/20 text-red-400 py-2 rounded-lg transition-all">
                                <span class="material-icons text-sm">delete</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>

    
</body>
</html>