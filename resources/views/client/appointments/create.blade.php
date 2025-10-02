@extends('layouts.app')
@section('title','Solicitar Nova Consulta')

@section('content')
<div class="bg-white rounded-xl shadow">
  <div class="px-6 py-4 border-b">
    <h2 class="text-xl font-semibold">Solicitar Nova Consulta</h2>
  </div>

  <form id="appointmentForm" action="{{ route('appointments.store') }}" method="POST" class="p-6 space-y-5">
    @csrf

    <div>
      <label class="block font-semibold mb-1">Especialidade <span class="text-red-600">*</span></label>
      <select name="specialty_id" id="specialty_id" class="w-full border rounded-lg p-2" required>
        <option value="" selected disabled>Selecione</option>
        @foreach($specialties as $s)
          <option value="{{ $s->id }}" data-price="{{ number_format($s->price,2,'.','') }}">{{ $s->name }}</option>
        @endforeach
      </select>
      <p id="priceInfo" class="text-sm text-slate-600 mt-1 hidden">
        Coparticipação: <span id="priceValue"></span>
      </p>
    </div>

    <div>
      <label class="block font-semibold mb-1">Qual o CEP do local onde você estará para a consulta? <span class="text-red-600">*</span></label>
      <input type="text" id="cep" name="cep" class="w-full border rounded-lg p-2" placeholder="00000-000" inputmode="numeric" maxlength="9" required autocomplete="postal-code">
      <p id="cepHelp" class="text-xs text-slate-500 mt-1">Digite o CEP (8 dígitos). Cidade/UF e clínicas serão preenchidas automaticamente.</p>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label class="block font-semibold mb-1">Cidade <span class="text-red-600">*</span></label>
        <input type="text" id="city" name="city" class="w-full border rounded-lg p-2" required>
      </div>
      <div>
        <label class="block font-semibold mb-1">Estado <span class="text-red-600">*</span></label>
        <select id="state" name="state" class="w-full border rounded-lg p-2" required>
          <option value="" disabled selected>Selecione</option>
          @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
            <option value="{{ $uf }}">{{ $uf }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div id="clinicsWrapper" class="rounded-lg border p-4">
      <div class="flex items-center justify-between mb-2">
        <h3 class="font-semibold">Clínicas próximas (raio 20 km) <span class="text-red-600">*</span></h3>
        <button type="button" id="refreshClinics" class="text-sm px-3 py-1 border rounded hover:bg-slate-50">Recarregar</button>
      </div>

      <input type="hidden" name="clinic_id" id="clinic_id" value="">
      <p id="clinicsError" class="text-sm text-red-600 hidden">Selecione uma clínica para continuar.</p>

      <div id="clinicsList" class="space-y-3 text-sm">
        <p class="text-slate-500">Informe o CEP acima para localizar clínicas próximas.</p>
      </div>
    </div>

    <!-- NOVO BLOCO: disponibilidade de dias + melhor horário -->
    <div class="grid sm:grid-cols-2 gap-6">
      <!-- Dias disponíveis -->
      <div>
        <label class="block font-semibold mb-2">
          Em quais dias da semana você está disponível? <span class="text-red-600">*</span>
        </label>

        <label class="flex items-start gap-2 mb-2 cursor-pointer">
          <input type="radio" name="available_days" value="any" class="mt-1" required checked>
          <span>Pode ser em qualquer dia entre segunda e sexta-feira</span>
        </label>

        <label class="flex items-start gap-2 mb-2 cursor-pointer">
          <input type="radio" name="available_days" value="mon_wed_fri" class="mt-1" required>
          <span>Segunda, Quarta, Sexta</span>
        </label>

        <label class="flex items-start gap-2 cursor-pointer">
          <input type="radio" name="available_days" value="tue_thu" class="mt-1" required>
          <span>Terça ou Quinta</span>
        </label>
      </div>

      <!-- Melhor horário -->
      <div>
        <label class="block font-semibold mb-2">
          Qual o melhor horário? <span class="text-red-600">*</span>
        </label>
        <select name="preferred_time" id="preferred_time" class="w-full border rounded-lg p-2" required>
          <option value="08-18" selected>Qualquer horário: 08h às 18h</option>
          <option value="08-13">Manhã: 08h às 13h</option>
          <option value="13-18">Tarde: 13h às 18h</option>
        </select>
      </div>
    </div>
    <!-- FIM NOVO BLOCO -->

    <div>
      <label class="block font-semibold mb-1">Deseja indicar uma clínica ou médico?</label>
      <input type="text" name="indication" class="w-full border rounded-lg p-2" placeholder="Nome da clínica/médico (opcional)">
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
// const NEARBY_URL = "{{ url('/api/clinics/nearby') }}";
const NEARBY_URL = "/api/clinics/nearby";


const cepInput   = document.getElementById('cep');
const cityInput  = document.getElementById('city');
const stateSelect= document.getElementById('state');
const listEl     = document.getElementById('clinicsList');
const clinicsErr = document.getElementById('clinicsError');
const clinicIdEl = document.getElementById('clinic_id');

// ---- CEP mask SEM duplicar '-' ----
let cepTimer = null;
function formatCep(val){
  const digits = val.replace(/\D/g,'').slice(0,8);
  return digits.length > 5 ? digits.slice(0,5)+'-'+digits.slice(5) : digits;
}
cepInput.addEventListener('keydown', (e)=>{ if (e.key === '-') e.preventDefault(); });
cepInput.addEventListener('input', (e)=>{
  e.target.value = formatCep(e.target.value);
  if (cepTimer) clearTimeout(cepTimer);
  cepTimer = setTimeout(()=>handleCepChange(), 300);
});
cepInput.addEventListener('paste', (e)=>{
  e.preventDefault();
  const text = (e.clipboardData || window.clipboardData).getData('text');
  cepInput.value = formatCep(text);
  handleCepChange();
});
cepInput.addEventListener('blur', handleCepChange);

// ---- ViaCEP + Nearby ----
async function handleCepChange(){
  const raw = cepInput.value.replace(/\D/g,'');
  if (raw.length !== 8){
    listEl.innerHTML = '<p class="text-slate-500">Informe um CEP válido (8 dígitos).</p>';
    return;
  }
  await preencherCidadeUfViaCep(raw);
  await fetchNearby(raw);
}

async function preencherCidadeUfViaCep(rawCep){
  try{
    const url = `https://viacep.com.br/ws/${rawCep}/json/`;
    const r   = await fetch(url, { cache: 'no-store' });
    const d   = await r.json();
    if (!d.erro){
      cityInput.value     = d.localidade || '';
      stateSelect.value   = d.uf || '';
      document.getElementById('cepHelp').textContent = 'Cidade/UF preenchidas automaticamente. Confira os dados.';
    } else {
      document.getElementById('cepHelp').textContent = 'CEP não encontrado no ViaCEP.';
    }
  }catch(e){
    console.error('ViaCEP error', e);
    document.getElementById('cepHelp').textContent = 'Falha ao consultar ViaCEP.';
  }
}

function renderClinics(items){
  if (!items || items.length === 0){
    listEl.innerHTML = '<p class="text-slate-500">Nenhuma clínica encontrado num raio de 20 km.</p>';
    return;
  }
  listEl.innerHTML = items.map(c => `
    <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer hover:bg-slate-50">
      <input type="radio" name="clinic_radio" value="${c.id}" class="mt-1 clinic-radio">
      <div>
        <div class="font-semibold">${c.name}</div>
        <div class="text-slate-600">
          ${c.address ? c.address : ''}${c.city ? ' - ' + c.city : ''}${c.state ? '/' + c.state : ''}
        </div>
        <div class="text-slate-500">Distância: ${(c.distance_km ?? 0).toFixed(2)} km</div>
        ${c.phone ? `<div class="text-slate-600">Fone: ${c.phone}</div>` : ''}
      </div>
    </label>
  `).join('');

  document.querySelectorAll('.clinic-radio').forEach(r => {
    r.addEventListener('change', () => {
      clinicIdEl.value = r.value;
      clinicsErr.classList.add('hidden');
    });
  });
}

async function fetchNearby(rawCep){
  listEl.innerHTML = '<p class="text-slate-500">Buscando clínicas próximas...</p>';
  try{
    const url  = `${NEARBY_URL}?cep=${rawCep}&radius_km=20`;
    const resp = await fetch(url, { method:'GET', headers:{ 'Accept':'application/json' }, cache:'no-store' });
    const text = await resp.text();

    let data;
    try { data = JSON.parse(text); } catch(e) {
      console.warn('Resposta não-JSON da API:', text);
      listEl.innerHTML = '<p class="text-red-600">Erro ao buscar clínicas (resposta inesperada da API).</p>';
      return;
    }

    if (!resp.ok){
      listEl.innerHTML = `<p class="text-red-600">Erro: ${data.message || 'Falha ao consultar'}</p>`;
      return;
    }

    renderClinics(data.clinics || []);
  }catch(e){
    console.error('fetchNearby error', e);
    listEl.innerHTML = '<p class="text-red-600">Erro ao buscar clínicas.</p>';
  }
}

document.getElementById('refreshClinics').addEventListener('click', () => {
  const raw = cepInput.value.replace(/\D/g,'');
  if (raw.length === 8) fetchNearby(raw);
});

document.getElementById('specialty_id')?.addEventListener('change', function(){
  const price = this.options[this.selectedIndex]?.dataset?.price;
  if (price){
    document.getElementById('priceValue').textContent = new Intl.NumberFormat('pt-BR',{style:'currency',currency:'BRL'}).format(price);
    document.getElementById('priceInfo').classList.remove('hidden');
  } else {
    document.getElementById('priceInfo').classList.add('hidden');
  }
});

document.getElementById('appointmentForm').addEventListener('submit', (e) => {
  if (!clinicIdEl.value){
    e.preventDefault();
    clinicsErr.classList.remove('hidden');
    document.getElementById('clinicsWrapper').scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
});
</script>
@endsection
