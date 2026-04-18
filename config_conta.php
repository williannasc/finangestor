<?php
// Proteção da página
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';
$conexao = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$mensagem = "";
$usuario_id = $_SESSION['usuario_id'];

// Lógica de Atualização (Salário e Nome)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $novo_nome = $conexao->real_escape_string($_POST['name']);
    $novo_salario = str_replace(',', '.', $_POST['salary']); // Garante formato decimal

    $sql_update = "UPDATE user SET name = '$novo_nome', salary = '$novo_salario' WHERE id = $usuario_id";
    
    if ($conexao->query($sql_update)) {
        $_SESSION['usuario_nome'] = $novo_nome; // Atualiza nome na sessão
        $mensagem = "Dados atualizados com sucesso!";
    } else {
        $mensagem = "Erro ao atualizar: " . $conexao->error;
    }
}

// Busca dados atuais do banco
$user = $conexao->query("SELECT * FROM user WHERE id = $usuario_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinanGestor | Minha Conta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="font-sans antialiased flex">

    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-4 md:p-10 max-w-5xl mx-auto w-full">
        <header class="mb-10">
            <h1 class="text-3xl font-black flex items-center gap-3 tracking-tighter">
                <span class="material-icons text-blue-400 text-4xl">person_settings</span> 
                CONFIGURAÇÕES DA CONTA
            </h1>
            <p class="opacity-50 text-sm ml-12 italic">Gerencie o seu perfil e o seu salário base</p>
        </header>

        <?php if($mensagem): ?>
            <div class="glass p-5 mb-8 border-blue-500/30 bg-blue-500/10 text-blue-200 flex items-center gap-3 animate-pulse">
                <span class="material-icons text-blue-400">check_circle</span>
                <span class="font-bold"><?php echo $mensagem; ?></span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2 glass p-8 shadow-2xl">
                <form action="" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] uppercase font-black opacity-40 ml-2 tracking-widest">Nome Completo</label>
                            <input type="text" name="name" value="<?php echo $user['name']; ?>" required 
                                   class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 mt-1 outline-none focus:ring-2 focus:ring-blue-500/40 transition-all font-medium">
                        </div>
                        <div>
                            <label class="text-[10px] uppercase font-black opacity-40 ml-2 tracking-widest">E-mail (Login)</label>
                            <input type="email" value="<?php echo $user['email']; ?>" disabled 
                                   class="w-full bg-white/5 border border-white/5 rounded-2xl p-4 mt-1 opacity-30 cursor-not-allowed font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-black opacity-40 ml-2 tracking-widest">Salário Mensal Atual (R$)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 opacity-30 font-bold">R$</span>
                            <input type="number" step="0.01" name="salary" value="<?php echo number_format($user['salary'], 2, '.', ''); ?>" required 
                                   class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 pl-12 mt-1 outline-none focus:ring-2 focus:ring-blue-500/40 transition-all font-mono text-lg font-bold">
                        </div>
                        <p class="text-[10px] opacity-30 mt-2 ml-2 italic">* Este valor é usado para calcular o seu Saldo Livre no Dashboard.</p>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="group flex items-center gap-3 px-10 py-4 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-bold transition-all shadow-xl shadow-blue-900/40">
                            SALVAR ALTERAÇÕES
                            <span class="material-icons text-sm group-hover:translate-x-1 transition-transform">send</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="space-y-6">
                <div class="glass p-6 border-l-4 border-blue-500/50">
                    <h3 class="font-bold text-sm opacity-80 mb-4 flex items-center gap-2">
                        <span class="material-icons text-xs">info</span> Dica do Sistema
                    </h3>
                    <p class="text-xs opacity-60 leading-relaxed">
                        Ao atualizar o seu **salário**, o cálculo de "Saldo Livre" no Dashboard será recalculado instantaneamente com base nos gastos já lançados este mês.
                    </p>
                </div>

                <div class="glass p-6 border-l-4 border-orange-500/50">
                    <h3 class="font-bold text-sm opacity-80 mb-2">Segurança</h3>
                    <p class="text-[10px] opacity-40 uppercase font-black tracking-tighter mb-4">Sessão Ativa</p>
                    <a href="logout.php" class="inline-flex items-center gap-2 text-xs font-bold text-red-400 hover:text-red-300 transition-all">
                        <span class="material-icons text-sm">logout</span> Encerrar Sessão
                    </a>
                </div>
            </div>
        </div>
    </main>

</body>
</html>