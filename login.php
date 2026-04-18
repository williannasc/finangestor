<?php
session_start();
require_once 'config.php';

// Se já estiver logado, vai direto pro index
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conexao = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    $email = $conexao->real_escape_string($_POST['email']);
    $senha = $_POST['senha'];

    $sql = "SELECT id, name, password FROM user WHERE email = '$email' LIMIT 1";
    $result = $conexao->query($sql);

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        
        // Verifica a senha (se você usa password_hash no cadastro)
        // Se ainda estiver usando texto puro no banco, use: if ($senha == $usuario['password'])
        if (password_verify($senha, $usuario['password']) || $senha == $usuario['password']) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['name'];
            header("Location: index.php");
            exit();
        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "E-mail não encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinanGestor | Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

</head>
<body class="p-4 body-login flex items-center justify-center min-h-screen">

    <div class="glass-login w-full max-w-md p-10 shadow-2xl">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-500/10 rounded-2xl mb-4 border border-blue-500/20">
                <span class="material-icons text-blue-400 text-3xl">lock</span>
            </div>
            <h1 class="text-3xl font-black text-white tracking-tighter">FinanGestor</h1>
            <p class="text-slate-400 text-sm mt-2 font-medium italic">Acesso Restrito - Bossoroca/RS</p>
        </div>

        <?php if($erro): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-2xl text-sm mb-6 text-center font-bold">
                <?php echo $erro; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <div>
                <label class="text-[10px] uppercase font-black opacity-40 text-white ml-2">E-mail</label>
                <input type="email" name="email" required class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 mt-1 text-white outline-none focus:ring-2 focus:ring-blue-500/40 transition-all">
            </div>

            <div>
                <label class="text-[10px] uppercase font-black opacity-40 text-white ml-2">Senha</label>
                <input type="password" name="senha" required class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 mt-1 text-white outline-none focus:ring-2 focus:ring-blue-500/40 transition-all">
            </div>

            <button type="submit" class="w-full py-4 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-bold transition-all shadow-xl shadow-blue-900/40 mt-4">
                ENTRAR NO SISTEMA
            </button>
        </form>

        <p class="text-center text-[10px] text-slate-500 mt-10 uppercase font-bold tracking-widest italic">
            v1.0 • Desenvolvido por você
        </p>
    </div>

</body>
</html>