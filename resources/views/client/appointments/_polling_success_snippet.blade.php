{{-- Polling: confirma sucesso e trata cancelamento --}}
<script>
(function(){
  const statusUrl = "{{ route('appointments.status', $appointment) }}";
  const successUrl = "{{ route('appointments.success', $appointment) }}";
  const paymentUrl = "{{ route('appointments.payment', $appointment) }}";
  async function tick(){
    try {
      const res = await fetch(statusUrl, {headers: {'X-Requested-With':'XMLHttpRequest'}});
      if(res.ok){
        const data = await res.json();
        const s = (data.status || '').toLowerCase();
        if (s === 'confirmed' || s === 'paid') {
          window.location.href = successUrl;
          return;
        }
        if (s === 'canceled') {
          alert('Pagamento cancelado. Você será redirecionado para gerar um novo PIX.');
          window.location.href = paymentUrl;
          return;
        }
      }
    } catch(e) { /* ignore */ }
    setTimeout(tick, 3000);
  }
  setTimeout(tick, 3000);
})();
</script>
