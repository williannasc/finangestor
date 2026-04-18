<?php
// Pega o nome do arquivo atual para marcar o link como ativo
$pagina_atual = basename($_SERVER['PHP_SELF']);
include_once 'modals.php';
?>
<aside class="w-64 h-screen sticky top-0 sidebar border-r border-white/10 p-6 flex flex-col justify-between hidden md:flex shrink-0">
    <div>
        <div class="flex items-center gap-3 mb-10 px-2">
            <span class="material-icons text-blue-400 text-3xl">account_balance</span>
            <h1 class="font-black text-xl tracking-tighter text-white">FinanGestor</h1>
        </div>

        <nav class="space-y-2">
            <a href="index.php" class="sidebar-item flex items-center gap-3 p-3 transition-all <?php echo ($pagina_atual == 'index.php') ? 'sidebar-active text-white' : 'opacity-60 hover:opacity-100 text-slate-300'; ?>">
                <span class="material-icons text-xl">dashboard</span> 
                <span class="font-medium">Dashboard</span>
            </a>
            
            <a href="#" onclick="document.getElementById('modalCompra').classList.remove('hidden')" class="sidebar-item flex items-center gap-3 p-3 opacity-60 hover:opacity-100 text-slate-300 transition-all">
                <span class="material-icons text-xl">add_box</span> 
                <span class="font-medium">Novo Gasto</span>
            </a>

            <a href="gerenciar_cartoes.php" class="sidebar-item flex items-center gap-3 p-3 transition-all <?php echo ($pagina_atual == 'gerenciar_cartoes.php') ? 'sidebar-active text-white' : 'opacity-60 hover:opacity-100 text-slate-300'; ?>">
                <span class="material-icons text-xl">credit_card</span> 
                <span class="font-medium">Gerenciar Cartões</span>
            </a>

            <a href="config_conta.php" class="sidebar-item flex items-center gap-3 p-3 transition-all <?php echo ($pagina_atual == 'config_conta.php') ? 'sidebar-active text-white' : 'opacity-60 hover:opacity-100 text-slate-300'; ?>">
                <span class="material-icons text-xl">person</span> 
                <span class="font-medium">Minha Conta</span>
            </a>

            <div class="pt-6 mt-6 border-t border-white/5">
                <p class="text-[10px] uppercase opacity-30 font-bold mb-4 px-3 tracking-widest text-white">Relatórios</p>
                <a href="historico.php" class="sidebar-item flex items-center gap-3 p-3 opacity-60 hover:opacity-100 text-slate-300 transition-all">
                    <span class="material-icons text-xl">history</span> 
                    <span class="font-medium">Histórico Anual</span>
                </a>
            </div>
        </nav>
    </div>

    <a href="logout.php" class="sidebar-item flex items-center gap-3 p-3 opacity-60 hover:opacity-100 text-slate-300 transition-all text-red-400">
        <span class="material-icons text-xl">logout</span>
        <span class="font-medium">Sair</span>
    </a>

    <div class="glass p-4 rounded-2xl bg-blue-500/10 border-blue-500/20">
        <p class="text-[10px] uppercase font-bold opacity-50 mb-1 text-blue-200">wn.dev.br</p>
        <p class="text-xs font-bold truncate text-white opacity-80">v0.1-beta1</p>
    </div>
</aside>