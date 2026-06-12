@extends('layouts.admin')

@section('admin-content')

    <div class="mt-4 flex flex-col items-center gap-3">
        <div class="inline-flex bg-white rounded shadow p-2 border border-slate-100 dark:border-slate-800">
            <button data-range="lifetime" class="rangeBtn px-3.5 py-1.5 bg-blue-50 rounded text-xs font-semibold cursor-pointer transition">Lifetime</button>
            <button data-range="6months" class="rangeBtn px-3.5 py-1.5 ml-2 text-xs font-semibold cursor-pointer transition">6 Meses</button>
            <button data-range="month" class="rangeBtn px-3.5 py-1.5 ml-2 text-xs font-semibold cursor-pointer transition">Mês</button>
            <button data-range="week" class="rangeBtn px-3.5 py-1.5 ml-2 text-xs font-semibold cursor-pointer transition">Semana</button>
        </div>

        {{-- Navigator controls --}}
        <div id="navControls" class="flex items-center gap-4 bg-white dark:bg-slate-900 rounded-xl shadow-sm px-4 py-2 border border-slate-200/60 dark:border-slate-800 hidden">
            <button id="prevRangeBtn" class="px-3 py-1.5 text-xs text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 font-bold cursor-pointer transition hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">&larr; Anterior</button>
            <span id="rangeDisplayLabel" class="text-xs font-bold text-slate-700 dark:text-slate-200 min-w-36 text-center"></span>
            <button id="nextRangeBtn" class="px-3 py-1.5 text-xs text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 font-bold cursor-pointer transition hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">Seguinte &rarr;</button>
        </div>
    </div>

    {{-- Main content centered --}}
    <div class="mt-8 flex justify-center">
        <div class="w-full max-w-4xl">
            <div class="bg-white rounded shadow p-6 text-center">
                <h2 class="text-xl font-semibold mb-4">Resumo de Encomendas</h2>
                <p class="text-sm text-gray-600 mb-6">Resumo: número de encomendas (colunas azuis) e dinheiro ganho (linha verde)</p>

                <div class="flex justify-center">
                    <canvas id="summaryChart" width="600" height="320"></canvas>
                </div>
                <div id="statsError" class="mt-4 hidden text-red-600 font-medium"></div>

                <div class="mt-6 grid grid-cols-3 gap-4">
                    <div class="p-4 border rounded">
                        <div class="text-sm text-gray-500">Total Vendas (fechadas)</div>
                        <div class="text-2xl font-bold">€ {{ number_format($totalSales,2) }}</div>
                    </div>
                    <div class="p-4 border rounded">
                        <div class="text-sm text-gray-500">Total Encomendas</div>
                        <div class="text-2xl font-bold">{{ $totalOrders }}</div>
                    </div>
                    <div class="p-4 border rounded">
                        <div class="text-sm text-gray-500">Última Actualização</div>
                        <div class="text-lg">{{ now()->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>

            {{-- Recent orders table centered below --}}
            <div class="mt-8 bg-white rounded shadow p-6">
                <h3 class="font-semibold mb-4">Encomendas Recentes</h3>
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Data</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $o)
                            <tr>
                                <td>{{ $o->id }}</td>
                                <td>{{ $o->customer_id }}</td>
                                <td>{{ $o->date->format('Y-m-d') }}</td>
                                <td>€ {{ number_format($o->total_price,2) }}</td>
                                <td>{{ $o->status }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Top tshirts (will be updated dynamically when range changes) --}}
            <div class="mt-8 bg-white rounded shadow p-6">
                 <h3 class="font-semibold mb-4">Top T-Shirts</h3>
                 <ul id="topTshirtsList" class="space-y-3">
                     @foreach($topTshirts as $t)
                         <li class="flex items-center">
                             @if($t['image'])
                                 <img src="{{ asset('storage/tshirt_images/' . ($t['image']->image_url ?? 'default.png')) }}" class="w-12 h-12 mr-3" alt="">
                                 <div>{{ $t['image']->name ?? 'Design #' . ($t['image']->id ?? '') }}</div>
                             @else
                                 <div>Design #{{ $t['image']->id ?? '' }}</div>
                             @endif
                             <div class="ml-auto">Vendidas: {{ $t['qty'] }}</div>
                         </li>
                     @endforeach
                 </ul>
             </div>
         </div>
     </div>
@endsection
 
 @push('scripts')
     {{-- Chart.js from CDN --}}
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
     <script>
         document.addEventListener('DOMContentLoaded', function() {
             // Main chart only
             const summaryEl = document.getElementById('summaryChart');
             if (!summaryEl) {
                 console.error('summaryChart element missing');
                 const errEl = document.getElementById('statsError'); if (errEl) { errEl.textContent = 'Elemento do gráfico principal em falta.'; errEl.classList.remove('hidden'); }
                 return;
             }
             const summaryCtx = summaryEl.getContext('2d');

             let summaryChart = null;
             try {
                 if (typeof Chart === 'undefined') throw new Error('Chart.js not loaded');
                 const safeFormat = function(v){ try { let num = (typeof v === 'object') ? (v.value ?? v.raw ?? v) : v; num = Number(num); if (isNaN(num)) return '€0'; return '€' + num.toLocaleString(); } catch(e){ return '€' + String(v); } };
                  summaryChart = new Chart(summaryCtx, {
                      type: 'bar',
                      data: { labels: [], datasets: [ { type: 'line', label: 'Vendas (€)', data: [], borderColor: '#10b981', backgroundColor: '#10b981', tension: 0.2, yAxisID: 'y-sales', order: 1 }, { type: 'bar', label: 'Encomendas', data: [], backgroundColor: '#60a5fa', yAxisID: 'y-orders', order: 2 } ] },
                      options: { responsive:true, animation: { duration: 700, easing: 'easeOutQuart' }, plugins:{ legend:{ position:'bottom' } }, scales: { 'y-orders':{ beginAtZero:true, position:'left', title:{ display:true, text:'Encomendas' } }, 'y-sales':{ beginAtZero:true, position:'right', title:{ display:true, text:'Vendas (€)' }, ticks:{ callback: safeFormat } } } }
                  });
             } catch (err) {
                 console.error('Failed to initialize main chart', err);
                 const errEl = document.getElementById('statsError'); if (errEl) { errEl.textContent = 'Erro ao inicializar o gráfico principal: ' + err.message; errEl.classList.remove('hidden'); }
                 summaryChart = { data:{labels:[],datasets:[{data:[]},{data:[]}] }, update:function(){} };
             }

             function updateTopList(labels, values, images){
                 const list = document.getElementById('topTshirtsList');
                 if (!list) return;
                 list.innerHTML = '';
                 for (let i=0;i<labels.length;i++){
                     const li = document.createElement('li'); li.className = 'flex items-center transition-opacity duration-500 opacity-0';
                     // image
                     const imgWrap = document.createElement('div');
                     imgWrap.className = 'mr-3';
                     const img = document.createElement('img'); img.className = 'w-12 h-12 object-cover rounded';
                     const imgFile = (images && images[i]) ? images[i] : 'default.png';
                     img.src = '/storage/tshirt_images/' + imgFile;
                     img.alt = labels[i] || 'Design';
                     imgWrap.appendChild(img);
                     li.appendChild(imgWrap);

                     const nameDiv = document.createElement('div'); nameDiv.textContent = labels[i] || 'Design';
                     const countDiv = document.createElement('div'); countDiv.className = 'ml-auto'; countDiv.textContent = 'Vendidas: ' + (values[i] || 0);
                     li.appendChild(nameDiv); li.appendChild(countDiv);
                     list.appendChild(li);
                     // trigger fade-in
                     (function(el){ requestAnimationFrame(()=>{ el.classList.remove('opacity-0'); }); })(li);
                 }
             }

             let currentRange = 'lifetime';
             let currentOffset = 0;

             function loadRange(range, offset = 0){
                 currentRange = range;
                 currentOffset = offset;

                 const errEl = document.getElementById('statsError'); if (errEl) { errEl.classList.add('hidden'); errEl.textContent = ''; }
                 fetch("{{ route('admin.dashboard.stats') }}?range="+range+"&offset="+offset, { credentials:'include', headers:{ 'X-Requested-With':'XMLHttpRequest' } })
                     .then(r=>{
                         if (r.redirected || r.status===401 || r.status===302 || r.status===403){ if (errEl) { errEl.textContent = 'Sessão expirada ou sem permissão. Recarregue e faça login.'; errEl.classList.remove('hidden'); } return Promise.reject(new Error('Unauthenticated')); }
                         return r.json();
                     })
                      .then(json=>{
                          summaryChart.data.labels = json.labels || [];
                          summaryChart.data.datasets[0].data = json.sums || [];
                          summaryChart.data.datasets[1].data = json.counts || [];
                          summaryChart.update();
 
                         // update top t-shirts list (with images)
                         updateTopList(json.topLabels || [], json.topValues || [], json.topImages || []);

                         // update range navigation display
                         const navCtrl = document.getElementById('navControls');
                         const lbl = document.getElementById('rangeDisplayLabel');
                         if (navCtrl && lbl) {
                             if (range === 'lifetime') {
                                 navCtrl.classList.add('hidden');
                             } else {
                                 navCtrl.classList.remove('hidden');
                                 lbl.textContent = json.rangeLabel || '';
                             }
                         }
                     })
                     .catch(e=>{ console.error('Stats load error', e); if (errEl && !errEl.textContent) { errEl.textContent = 'Erro ao carregar estatísticas. Verifique a consola.'; errEl.classList.remove('hidden'); } });
             }
 
              const rangeButtons = Array.from(document.querySelectorAll('.rangeBtn'));
              rangeButtons.forEach(b=>{ b.addEventListener('click', function(){ rangeButtons.forEach(x=>x.classList.remove('bg-blue-50')); this.classList.add('bg-blue-50'); loadRange(this.dataset.range, 0); }); });

              // Bind navigator buttons
              const prevBtn = document.getElementById('prevRangeBtn');
              const nextBtn = document.getElementById('nextRangeBtn');
              if (prevBtn) {
                  prevBtn.addEventListener('click', function() {
                      loadRange(currentRange, currentOffset - 1);
                  });
              }
              if (nextBtn) {
                  nextBtn.addEventListener('click', function() {
                      loadRange(currentRange, currentOffset + 1);
                  });
              }

              const defaultBtn = rangeButtons.find(b=>b.dataset.range==='lifetime') || rangeButtons[0]; if (defaultBtn) { defaultBtn.classList.add('bg-blue-50'); loadRange(defaultBtn.dataset.range, 0); }
         });
     </script>
 @endpush
