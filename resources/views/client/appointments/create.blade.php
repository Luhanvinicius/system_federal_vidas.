@extends('layouts.app')
@section('title','Solicitar Nova Consulta')
@section('content')
<div class="bg-white rounded-xl shadow">
  <div class="px-6 py-4 border-b"><h2 class="text-xl font-semibold">Solicitar Nova Consulta</h2></div>
  <form action="{{ route('appointments.store') }}" method="POST" class="p-6 space-y-5">@csrf
    <div>
      <label class="block font-semibold mb-1">Especialidade *</label>
      <select name="specialty_id" id="specialty_id" class="w-full border rounded-lg p-2" required>
        <option value="" selected disabled>Selecione</option>
        @foreach($specialties as $s)
          <option value="{{ $s->id }}" data-price="{{ number_format($s->price,2,'.','') }}">{{ $s->name }}</option>
        @endforeach
      </select>
      <p id="priceInfo" class="text-sm text-slate-600 mt-1 hidden">Coparticipação: <span id="priceValue"></span></p>
    </div>
    <div>
      <label class="block font-semibold mb-1">Qual o CEP do local onde você estará para a consulta? *</label>
      <input type="text" id="cep" name="cep" class="w-full border rounded-lg p-2" placeholder="00000-000" inputmode="numeric" maxlength="9" required>
      <p id="cepHelp" class="text-xs text-slate-500 mt-1">Digite o CEP e pressione Tab ou clique fora do campo.</p>
    </div>
    <div class="grid sm:grid-cols-2 gap-4">
      <div><label class="block font-semibold mb-1">Cidade *</label><input type="text" id="city" name="city" class="w-full border rounded-lg p-2" required></div>
      <div>
        <label class="block font-semibold mb-1">Estado *</label>
        <select id="state" name="state" class="w-full border rounded-lg p-2" required>
          <option value="" disabled selected>Selecione</option>
          @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
            <option value="{{ $uf }}">{{ $uf }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div><label class="block font-semibold mb-1">Deseja indicar uma clínica ou médico?</label><input type="text" name="indication" class="w-full border rounded-lg p-2" placeholder="Nome da clínica/médico (opcional)"></div>
    <div><label class="block font-semibold mb-1">Observações</label><textarea name="notes" rows="4" class="w-full border rounded-lg p-2" placeholder="Descreva detalhes úteis (opcional)"></textarea></div>
    <div><button class="px-5 py-2 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700">Solicitar</button></div>
  </form>
</div>

<div class="mt-6 bg-white rounded-xl shadow p-6">
  <div class="flex items-center justify-between mb-3">
    <h3 class="text-lg font-semibold">Clínicas próximas (raio 20 km)</h3>
    <button id="refreshClinics" class="text-sm px-3 py-1 border rounded hover:bg-slate-50">Recarregar</button>
  </div>
  <div id="clinicsList" class="space-y-3 text-sm">
    <p class="text-slate-500">Informe o CEP acima para localizar clínicas próximas.</p>
  </div>
</div>

<script>
const NEARBY_URL = "{{ url('/api/clinics/nearby') }}";

// CEP mask
const cepInput = document.getElementById('cep');
cepInput.addEventListener('input',(e)=>{
  let v = e.target.value.replace(/\D/g,'').slice(0,8);
  if(v.length>5) v = v.slice(0,5)+'-'+v.slice(5);
  e.target.value = v;
});

// ViaCEP
async function buscarViaCep(){
  const raw = cepInput.value.replace(/\D/g,'');
  if(raw.length!==8) return;
  try{
    const r = await fetch('https://viacep.com.br/ws/'+raw+'/json/');
    const d = await r.json();
    if(!d.erro){
      document.getElementById('city').value = d.localidade || '';
      document.getElementById('state').value = d.uf || '';
      document.getElementById('cepHelp').textContent = 'Cidade/UF preenchidas automaticamente. Confira os dados.';
    }else{
      document.getElementById('cepHelp').textContent = 'CEP não encontrado.';
    }
  }catch{ document.getElementById('cepHelp').textContent = 'Falha ao consultar o CEP.'; }
}

// preço
document.getElementById('specialty_id').addEventListener('change', function(){
  const price = this.options[this.selectedIndex].dataset.price;
  if(price){
    document.getElementById('priceValue').textContent = new Intl.NumberFormat('pt-BR',{style:'currency',currency:'BRL'}).format(price);
    document.getElementById('priceInfo').classList.remove('hidden');
  }
});

// nearby
async function fetchNearby(){
  const raw = cepInput.value.replace(/\D/g,'');
  if(raw.length!==8) return;
  const list = document.getElementById('clinicsList');
  list.innerHTML = '<p class="text-slate-500">Buscando clínicas próximas...</p>';
  try{
    const resp = await fetch(`${NEARBY_URL}?cep=${raw}&radius_km=20`,{headers:{'Accept':'application/json'}});
    const text = await resp.text();
    let data = null;
    try{ data = JSON.parse(text); }catch(e){
      list.innerHTML = `<p class="text-red-600">A rota /api/clinics/nearby não está ativa. Verifique bootstrap/app.php (withRouting api) e routes/api.php.</p>`;
      return;
    }
    if(!resp.ok){ list.innerHTML = `<p class="text-red-600">Erro: ${data.message || 'Falha ao consultar'}</p>`; return; }
    if(!data.clinics || data.clinics.length===0){ list.innerHTML = '<p class="text-slate-500">Nenhuma clínica encontrada num raio de 20 km.</p>'; return; }
    list.innerHTML = data.clinics.map(c=>`
      <div class="border rounded-lg p-3">
        <div class="font-semibold">${c.name}</div>
        <div class="text-slate-600">${c.address||''} ${c.city?' - '+c.city:''} ${c.state?'/'+c.state:''}</div>
        <div class="text-slate-500">Distância: ${(c.distance_km??0).toFixed(2)} km</div>
        ${c.phone?`<div class="text-slate-600">Fone: ${c.phone}</div>`:''}
      </div>`).join('');
  }catch(e){ list.innerHTML = '<p class="text-red-600">Erro ao buscar clínicas.</p>'; console.error(e);}
}

cepInput.addEventListener('blur',()=>{buscarViaCep(); fetchNearby();});
document.getElementById('refreshClinics').addEventListener('click',fetchNearby);
</script>
@endsection
