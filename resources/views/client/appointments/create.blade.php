@extends('layouts.app')

@section('title', 'Solicitar Nova Consulta')

@section('content')
<div class="bg-white rounded-xl shadow">
    <div class="px-6 py-4 border-b">
        <h2 class="text-xl font-semibold">Solicitar Nova Consulta</h2>
    </div>

    @if(session('success'))
        <div class="px-6 pt-4">
            <div class="bg-emerald-50 text-emerald-700 border border-emerald-200 rounded p-3 mb-2">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <form action="{{ route('appointments.store') }}" method="POST" class="p-6 space-y-5">
        @csrf

        <div>
            <label class="block font-semibold mb-1">Especialidade <span class="text-red-600">*</span></label>
            <select name="specialty_id" id="specialty_id" class="w-full border rounded-lg p-2" required>
                <option value="" selected disabled>Selecione</option>
                @foreach($specialties as $s)
                    <option value="{{ $s->id }}" data-price="{{ number_format($s->price,2,'.','') }}">{{ $s->name }}</option>
                @endforeach
            </select>
            @error('specialty_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            <p id="priceInfo" class="text-sm text-slate-600 mt-1 hidden">Coparticipação: <span id="priceValue"></span></p>
        </div>

        <div>
            <label class="block font-semibold mb-1">Qual o CEP do local onde você estará para a consulta? <span class="text-red-600">*</span></label>
            <input type="text" id="cep" name="cep" class="w-full border rounded-lg p-2" placeholder="00000-000" inputmode="numeric" maxlength="9" required>
            <p id="cepHelp" class="text-xs text-slate-500 mt-1">Digite o CEP e pressione Tab ou clique fora do campo.</p>
            @error('cep') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold mb-1">Cidade <span class="text-red-600">*</span></label>
                <input type="text" id="city" name="city" class="w-full border rounded-lg p-2" placeholder="Digite a cidade" required>
                @error('city') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-semibold mb-1">Estado <span class="text-red-600">*</span></label>
                <select id="state" name="state" class="w-full border rounded-lg p-2" required>
                    <option value="" disabled selected>Selecione</option>
                    @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                        <option value="{{ $uf }}">{{ $uf }}</option>
                    @endforeach
                </select>
                @error('state') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block font-semibold mb-1">Deseja indicar uma clínica ou médico?</label>
            <input type="text" name="indication" class="w-full border rounded-lg p-2" placeholder="Nome da clínica/médico (opcional)">
            @error('indication') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block font-semibold mb-1">Observações</label>
            <textarea name="notes" rows="4" class="w-full border rounded-lg p-2" placeholder="Descreva detalhes úteis (opcional)"></textarea>
        </div>

        <div>
            <button class="px-5 py-2 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700">
                Solicitar
            </button>
        </div>
    </form>
</div>

<script>
// Máscara simples de CEP
const cepInput = document.getElementById('cep');
cepInput.addEventListener('input', (e) => {
    let v = e.target.value.replace(/\D/g, '').slice(0,8);
    if (v.length > 5) v = v.slice(0,5) + '-' + v.slice(5);
    e.target.value = v;
});

// Busca CEP na ViaCEP ao sair do campo (blur)
cepInput.addEventListener('blur', buscarCep);
cepInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); buscarCep(); }});

async function buscarCep(){
    const raw = cepInput.value.replace(/\D/g,'');
    if (raw.length !== 8) {
        hint('CEP inválido. Use 8 dígitos.');
        return;
    }
    try{
        hint('Consultando CEP...', 'info');
        const resp = await fetch('https://viacep.com.br/ws/' + raw + '/json/');
        const data = await resp.json();
        if (data.erro){
            hint('CEP não encontrado.' , 'error');
            return;
        }
        document.getElementById('city').value = data.localidade || '';
        const stateSel = document.getElementById('state');
        stateSel.value = data.uf || '';
        if (!stateSel.value) { hint('UF não identificada. Selecione manualmente.', 'warn'); }
        else { hint('Cidade/UF preenchidas automaticamente. Confira os dados.','ok'); }
    } catch(err){
        hint('Falha ao consultar o CEP. Tente novamente.', 'error');
        console.error(err);
    }
}

function hint(msg, type=''){
    const el = document.getElementById('cepHelp');
    const colors = {
        ok: 'text-emerald-600',
        error: 'text-red-600',
        warn: 'text-amber-600',
        info: 'text-slate-500'
    };
    el.className = 'text-xs mt-1 ' + (colors[type] || 'text-slate-500');
    el.textContent = msg;
}

// Preço dinâmico
document.getElementById('specialty_id').addEventListener('change', function() {
    const price = this.options[this.selectedIndex].dataset.price;
    if (price) {
        document.getElementById('priceValue').textContent = new Intl.NumberFormat('pt-BR', {style:'currency', currency:'BRL'}).format(price);
        document.getElementById('priceInfo').classList.remove('hidden');
    }
});
</script>
@endsection
