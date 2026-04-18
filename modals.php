<div id="modalCompra" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md hidden flex items-center justify-center z-50 p-4">
    <div class="glass p-8 w-full max-w-lg border border-white/20 shadow-2xl" style="border-radius: 1.5rem;">
        <h2 class="text-2xl font-bold mb-6 flex items-center gap-3 italic text-white">
            <span class="material-icons text-blue-400">swap_vert</span> NOVO MOVIMENTO
        </h2>
        <form action="lancar_compra.php" method="POST" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <div class="md:col-span-2 flex gap-4 p-1 bg-white/5 rounded-xl border border-white/10">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="tipo" value="EXPENSE" checked class="hidden peer">
                        <div class="peer-checked:bg-red-500/20 peer-checked:text-red-400 p-3 text-center rounded-lg font-bold text-xs opacity-50 peer-checked:opacity-100 transition-all text-white text-white">DESPESA</div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="tipo" value="INCOME" class="hidden peer">
                        <div class="peer-checked:bg-green-500/20 peer-checked:text-green-400 p-3 text-center rounded-lg font-bold text-xs opacity-50 peer-checked:opacity-100 transition-all text-white text-white">RECEITA</div>
                    </label>
                </div>

                <div class="md:col-span-2 flex gap-4 mb-2">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="metodo_valor" value="total" checked class="hidden peer">
                        <div class="peer-checked:bg-blue-500/20 p-2 text-center rounded-lg border border-white/10 text-[10px] font-bold opacity-50 peer-checked:opacity-100 transition-all text-white">VALOR É O TOTAL</div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="metodo_valor" value="parcela" class="hidden peer">
                        <div class="peer-checked:bg-blue-500/20 p-2 text-center rounded-lg border border-white/10 text-[10px] font-bold opacity-50 peer-checked:opacity-100 transition-all text-white">VALOR É DA PARCELA</div>
                    </label>
                </div>

                <div class="md:col-span-2">
                    <label class="text-[10px] uppercase font-black opacity-40 ml-2 text-white text-white">Descrição</label>
                    <input type="text" name="descricao" required class="w-full bg-white/5 border border-white/10 rounded-xl p-4 mt-1 outline-none text-white focus:ring-2 focus:ring-blue-500/40">
                </div>
                
                <div>
                    <label class="text-[10px] uppercase font-black opacity-40 ml-2 text-white text-white">Valor</label>
                    <input type="number" step="0.01" name="valor" required class="w-full bg-white/5 border border-white/10 rounded-xl p-4 mt-1 outline-none text-white">
                </div>
                
                <div>
                    <label class="text-[10px] uppercase font-black opacity-40 ml-2 text-white text-white">Qtd. Parcelas</label>
                    <input type="number" name="parcelas" value="1" min="1" required class="w-full bg-white/5 border border-white/10 rounded-xl p-4 mt-1 outline-none text-white">
                </div>

                <div>
                    <label class="text-[10px] uppercase font-black opacity-40 ml-2 text-white text-white">Cartão</label>
                    <select name="id_cartao" class="w-full bg-slate-800 border border-white/10 rounded-xl p-4 mt-1 outline-none text-white">
                        <option value="">Dinheiro/Pix</option>
                        <?php 
                        $res_c = $conexao->query("SELECT id, name FROM card WHERE userId = ".$_SESSION['usuario_id']);
                        while($c = $res_c->fetch_assoc()) echo "<option value='{$c['id']}'>{$c['name']}</option>";
                        ?>
                    </select>
                </div>

                <div>
                    <label class="text-[10px] uppercase font-black opacity-40 ml-2 text-white text-white text-white">Data Inicial</label>
                    <input type="date" name="data_referencia" value="<?php echo date('Y-m-d'); ?>" class="w-full bg-white/5 border border-white/10 rounded-xl p-4 mt-1 outline-none text-white">
                </div>

                <div class="md:col-span-2">
                    <label class="text-[10px] uppercase font-black opacity-40 ml-2 text-white text-white">Categoria</label>
                    <select name="categoria" class="w-full bg-slate-800 border border-white/10 rounded-xl p-4 mt-1 outline-none text-white">
                        <option value="Variável">Variável</option>
                        <option value="Contas Fixas">Contas Fixas</option>
                        <option value="Lazer">Lazer</option>
                        <option value="Mercado">Mercado</option>
                        <option value="Extra">Extra (Receita)</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-4 mt-4">
                <button type="button" onclick="document.getElementById('modalCompra').classList.add('hidden')" class="flex-1 p-4 font-bold opacity-50 text-white text-white text-white">CANCELAR</button>
                <button type="submit" class="flex-1 bg-blue-600 py-4 rounded-2xl font-bold shadow-lg shadow-blue-900/40 text-white text-white text-white">SALVAR</button>
            </div>
        </form>
    </div>
</div>

<div id="modalNovoCartao" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md hidden flex items-center justify-center z-50 p-4">
    <div class="glass p-8 w-full max-w-md border border-white/20">
        <h2 class="text-xl font-bold mb-6 italic tracking-tighter">ADICIONAR NOVO BANCO</h2>
        <form action="" method="POST" class="space-y-4">
            <input type="hidden" name="add_card" value="1">
            <input type="text" name="name" placeholder="Nome do Banco (Ex: Inter)" required class="w-full bg-white/5 border border-white/10 rounded-xl p-4 outline-none">
            <input type="number" name="limit" placeholder="Limite Inicial" required class="w-full bg-white/5 border border-white/10 rounded-xl p-4 outline-none">
            <div class="grid grid-cols-2 gap-4">
                <input type="number" name="closingDay" placeholder="Dia Fechamento" required class="w-full bg-white/5 border border-white/10 rounded-xl p-4 outline-none">
                <input type="number" name="dueDay" placeholder="Dia Vencimento" required class="w-full bg-white/5 border border-white/10 rounded-xl p-4 outline-none">
            </div>
            <div class="flex gap-4 pt-6">
                <button type="button" onclick="document.getElementById('modalNovoCartao').classList.add('hidden')" class="flex-1 py-4 font-bold opacity-50">CANCELAR</button>
                <button type="submit" class="flex-1 bg-blue-600 py-4 rounded-2xl font-bold shadow-lg shadow-blue-900/40">SALVAR</button>
            </div>
        </form>
    </div>
</div>