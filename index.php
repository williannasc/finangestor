<?php
require_once 'auth.php';
require_once 'config.php';

$conexao = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conexao->connect_error) {
    die("Erro: " . $conexao->connect_error);
}

$mes_atual = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$ano_atual = isset($_GET['ano']) ? $_GET['ano'] : date('Y');
$usuario_id = $_SESSION['usuario_id'];

// Configura o primeiro dia do mês que você está visualizando
$primeiro_dia_visualizado = "$ano_atual-$mes_atual-01";

// 1. Busca o Salário Base do cadastro
$sql_user = "SELECT salary FROM user WHERE id = $usuario_id";
$salario_base = $conexao->query($sql_user)->fetch_assoc()['salary'] ?? 0;

// 2. SALDO ACUMULADO (Passado)
$sql_passado = "SELECT 
    (SUM(CASE WHEN type = 'INCOME' THEN amount ELSE 0 END) - 
     SUM(CASE WHEN type = 'EXPENSE' THEN amount ELSE 0 END)) as saldo_acumulado
    FROM transaction 
    WHERE referenceDate < '$primeiro_dia_visualizado' AND userId = $usuario_id";
$saldo_anterior = $conexao->query($sql_passado)->fetch_assoc()['saldo_acumulado'] ?? 0;

// 3. MOVIMENTAÇÃO DO MÊS ATUAL (KPIS)
$sql_mes = "SELECT 
    SUM(CASE WHEN type = 'INCOME' THEN amount ELSE 0 END) as entradas_extras,
    SUM(CASE WHEN category = 'Contas Fixas' AND type = 'EXPENSE' THEN amount ELSE 0 END) as despesas_fixas,
    SUM(CASE WHEN cardId IS NOT NULL AND type = 'EXPENSE' THEN amount ELSE 0 END) as total_cartao,
    SUM(CASE WHEN type = 'EXPENSE' THEN amount ELSE 0 END) as despesas_totais
    FROM transaction 
    WHERE MONTH(referenceDate) = '$mes_atual' 
    AND YEAR(referenceDate) = '$ano_atual' 
    AND userId = $usuario_id";

$res_mes = $conexao->query($sql_mes)->fetch_assoc();

$entradas_extras = $res_mes['entradas_extras'] ?? 0;
$total_cartao    = $res_mes['total_cartao'] ?? 0;
$total_fixo      = $res_mes['despesas_fixas'] ?? 0;
$despesas_totais = $res_mes['despesas_totais'] ?? 0;

// 4. CÁLCULO FINAL CORRIGIDO
$receita_mensal = $salario_base + $entradas_extras;
$saldo_livre = ($saldo_anterior + $receita_mensal) - $despesas_totais;

$meses_pt = ['01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'];
$nome_mes = $meses_pt[$mes_atual];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinanGestor | Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body class="font-sans antialiased flex">

    <?php include 'sidebar.php'; ?>

    <main class="flex-1 min-h-screen p-4 md:p-10 overflow-y-auto">

        <div class="max-w-6xl mx-auto">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight flex items-center gap-2">
                        <span class="material-icons text-blue-400 text-4xl">account_balance</span> FinanGestor
                    </h1>
                    <p class="opacity-50 text-sm ml-11 italic">Gestão Independente - <?php echo $nome_mes ?> de <?php echo $ano_atual ?></p>
                </div>
                <div class="glass px-8 py-4 border-blue-500/20 text-right">
                    <p class="text-[10px] uppercase opacity-40 font-black tracking-widest">Saldo Livre Estimado</p>
                    <p class="text-3xl font-mono font-bold text-blue-400">R$ <?php echo number_format($saldo_livre, 2, ',', '.'); ?></p>
                </div>
            </header>

            <div class="flex items-center gap-4 mb-6 bg-white/5 p-4 rounded-2xl border border-white/10">
                <a href="?mes=<?php echo date('m', strtotime($primeiro_dia_visualizado . " -1 month")); ?>&ano=<?php echo date('Y', strtotime($primeiro_dia_visualizado . " -1 month")); ?>"
                    class="p-2 hover:bg-white/10 rounded-lg transition-all">
                    <span class="material-icons text-white">chevron_left</span>
                </a>

                <h2 class="text-white font-bold text-lg min-w-[150px] text-center uppercase tracking-widest">
                    <?php echo $nome_mes . " " . $ano_atual; ?>
                </h2>

                <a href="?mes=<?php echo date('m', strtotime($primeiro_dia_visualizado . " +1 month")); ?>&ano=<?php echo date('Y', strtotime($primeiro_dia_visualizado . " +1 month")); ?>"
                    class="p-2 hover:bg-white/10 rounded-lg transition-all">
                    <span class="material-icons text-white">chevron_right</span>
                </a>

                <a href="index.php" class="ml-auto text-[10px] font-bold opacity-50 hover:opacity-100 text-white border border-white/20 px-3 py-1 rounded-md">VOLTAR PARA HOJE</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-10">
                <div class="glass p-6 glass-card border-l-4 border-green-500/40">
                    <p class="text-[10px] opacity-50 font-bold uppercase">Receita Mensal</p>
                    <h3 class="text-2xl font-bold mt-1 text-green-400">R$ <?php echo number_format($receita_mensal, 2, ',', '.'); ?></h3>
                </div>
                <div class="glass p-6 glass-card border-l-4 border-orange-500/40">
                    <p class="text-[10px] opacity-50 font-bold uppercase">Cartão de Crédito</p>
                    <h3 class="text-2xl font-bold mt-1 text-orange-200">R$ <?php echo number_format($total_cartao, 2, ',', '.'); ?></h3>
                </div>
                <div class="glass p-6 glass-card border-l-4 border-blue-500/40">
                    <p class="text-[10px] opacity-50 font-bold uppercase">Despesas Fixas</p>
                    <h3 class="text-2xl font-bold mt-1 text-blue-200">R$ <?php echo number_format($total_fixo, 2, ',', '.'); ?></h3>
                </div>
                <div class="glass p-6 glass-card border-l-4 border-red-500/40">
                    <p class="text-[10px] opacity-50 font-bold uppercase">Despesas Totais</p>
                    <h3 class="text-2xl font-bold mt-1 text-red-400">R$ <?php echo number_format($despesas_totais, 2, ',', '.'); ?></h3>
                </div>
                <div class="glass p-6 glass-card border-l-4 border-gray-500/40">
                    <p class="text-[10px] opacity-50 font-bold uppercase">Saldo Livre Mês</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-300">R$ <?php echo number_format(($saldo_anterior + $receita_mensal) - $despesas_totais, 2, ',', '.'); ?></h3>
                </div>
            </div>

            <div class="glass p-6 mb-10 border border-white/10">
                <p class="text-[10px] uppercase font-bold opacity-30 mb-4 ml-2 tracking-widest">Lanamento Rápido de Despesa</p>
                <form action="lancar_compra.php" method="POST" class="flex flex-wrap md:flex-nowrap items-end gap-4">
                    <input type="hidden" name="tipo" value="EXPENSE">
                    <input type="hidden" name="categoria" value="VARIÁVEL">
                    <input type="hidden" name="parcelas" value="1">
                    <input type="hidden" name="metodo_valor" value="total">
                    <input type="hidden" name="data_referencia" value="<?php echo date('Y-m-d'); ?>">
                    <div class="flex-1">
                        <label class="text-[10px] font-bold opacity-30 ml-2 uppercase">Descrição</label>
                        <input type="text" name="descricao" placeholder="O que compraste?" required class="w-full bg-white/5 border border-white/10 rounded-xl p-3 mt-1 outline-none focus:ring-1 focus:ring-blue-500 text-sm">
                    </div>
                    <div class="w-32">
                        <label class="text-[10px] font-bold opacity-30 ml-2 uppercase">Valor</label>
                        <input type="number" step="0.01" name="valor" placeholder="0,00" required class="w-full bg-white/5 border border-white/10 rounded-xl p-3 mt-1 outline-none text-sm">
                    </div>
                    <button type="submit" class="bg-blue-600 px-8 h-[50px] rounded-xl font-bold hover:bg-blue-500 transition-all text-sm uppercase">Lançar</button>
                </form>
            </div>

            <h2 class="text-sm font-black opacity-30 uppercase tracking-[0.2em] mb-4 ml-2">Meus Cartões e Limites</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <?php
                $sql_cards = "SELECT c.id, c.name, c.`limit`, 
                            (SELECT SUM(amount) FROM transaction t WHERE t.cardId = c.id AND MONTH(t.referenceDate) = '$mes_atual' AND YEAR(t.referenceDate) = '$ano_atual') as gasto_real 
                            FROM card c WHERE c.userId = $usuario_id";
                $res_cards = $conexao->query($sql_cards);
                while ($card = $res_cards->fetch_assoc()):
                    $gasto = $card['gasto_real'] ?? 0;
                    $limite = $card['limit'];
                    $percent = ($limite > 0) ? min(($gasto / $limite) * 100, 100) : 0;
                ?>
                    <div class="glass p-5 glass-card">
                        <div class="flex justify-between items-start mb-4">
                            <span class="font-bold text-sm opacity-80"><?php echo strtoupper($card['name']); ?></span>
                            <span class="material-icons opacity-20">credit_card</span>
                        </div>
                        <div class="flex justify-between items-end mb-2">
                            <p class="text-lg font-mono font-bold">R$ <?php echo number_format($gasto, 2, ',', '.'); ?></p>
                            <p class="text-[10px] opacity-40 italic">Limite: R$ <?php echo number_format($limite, 0, ',', '.'); ?></p>
                        </div>
                        <div class="w-full bg-white/5 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-blue-500/50 h-full transition-all duration-1000" style="width: <?php echo $percent; ?>%"></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="glass overflow-hidden shadow-2xl mb-10">
                <div class="p-6 border-b border-white/5 flex justify-between items-center bg-white/5">
                    <h2 class="text-lg font-bold flex items-center gap-2">
                        <span class="material-icons text-blue-400">format_list_bulleted</span> Extrato Geral
                    </h2>
                    <button onclick="document.getElementById('modalCompra').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded-xl text-xs font-black flex items-center gap-2 transition-all shadow-lg shadow-blue-900/40">
                        <span class="material-icons text-sm">add_circle</span> NOVO LANÇAMENTO
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] uppercase tracking-widest opacity-30 border-b border-white/5">
                                <th class="p-6">Descrição / Data</th>
                                <th class="p-6">Categoria</th>
                                <th class="p-6 text-center">Parcela</th>
                                <th class="p-6 text-right">Valor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <?php
                            $sql_lista = "SELECT t.*, c.name as card_name FROM transaction t 
                LEFT JOIN card c ON t.cardId = c.id
                WHERE MONTH(referenceDate) = '$mes_atual' AND YEAR(referenceDate) = '$ano_atual' 
                AND t.userId = $usuario_id
                ORDER BY referenceDate ASC";
                            $res_lista = $conexao->query($sql_lista);
                            while ($item = $res_lista->fetch_assoc()):
                                $is_income = ($item['type'] == 'INCOME');
                            ?>
                                <tr class="hover:bg-white/[0.02] transition">
                                    <td class="p-6">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-200"><?php echo $item['description']; ?></span>
                                            <span class="text-[10px] opacity-30">
                                                <?php echo date('d/m/Y', strtotime($item['referenceDate'])); ?>
                                                • <?php echo $item['card_name'] ? '💳 ' . $item['card_name'] : '💵 Dinheiro/Pix'; ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="p-6">
                                        <span class="text-[9px] px-2 py-1 rounded bg-white/5 border border-white/10 opacity-60 font-bold uppercase"><?php echo $item['category']; ?></span>
                                    </td>
                                    <td class="p-6 text-center text-xs opacity-40"><?php echo $item['installmentTotal'] > 1 ? $item['installmentCurrent'] . '/' . $item['installmentTotal'] : '-'; ?></td>
                                    <td class="p-6 text-right font-mono font-bold <?php echo $is_income ? 'text-green-400' : 'text-blue-100'; ?>">
                                        <?php echo $is_income ? '+' : '-'; ?> R$ <?php echo number_format($item['amount'], 2, ',', '.'); ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include 'modals.php'; ?>

</body>

</html>