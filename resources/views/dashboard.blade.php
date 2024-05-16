<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Painel') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Seja bem vindo!") }}
                    <!-- Formulário para seleção de período -->
                    <form action="{{ route('dashboard') }}" method="GET">
    <select name="periodo" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        @php $selectedPeriod = request('periodo', '15'); @endphp
        <option value="7" {{ $selectedPeriod == '7' ? 'selected' : '' }}>Últimos 7 dias</option>
        <option value="15" {{ $selectedPeriod == '15' ? 'selected' : '' }}>Últimos 15 dias</option>
        <option value="30" {{ $selectedPeriod == '30' ? 'selected' : '' }}>Último mês</option>
        <option value="60" {{ $selectedPeriod == '60' ? 'selected' : '' }}>Últimos 60 dias</option>
        <!-- Adicione mais opções conforme necessário -->
    </select>
    <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Selecionar período</button>
</form>
<h4 class="text-2xl font-bold text-center mb-4">Período Selecionado: {{ $period }} dias</h4>

 <!-- Exibição dos eventos e suas conversões -->
 <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 grid grid-cols-10 gap-4"> <!-- Alterado para 10 colunas para maior flexibilidade -->

                <!-- Coluna para o funil de eventos (70% da área) -->
                <div class="col-span-7">
                    <h2 class="font-bold text-lg">Funil de Eventos</h2>
                    @foreach ($eventsData as $event)
                        <div class="text-center p-4 rounded-lg shadow mx-auto" style="width: {{ 100 - $loop->index * 20 }}%; background-color: {{ $loop->iteration === 1 ? '#e5e5e5' : ($loop->iteration === 2 ? '#8275dd' : '#20c997') }}; color: {{ $loop->iteration === 1 ? 'black' : 'white' }};">
                            <p class="text-lg">{{ $event['eventName'] }}: {{ $event['totalEvents'] }} conversões</p>
                        </div>
                    @endforeach
                </div>

                <!-- Coluna para dados de campanhas (30% da área) -->
                <h2 class="font-bold text-lg" style="font-size: large; font-weight: bold;">Dados de Campanhas </h2>
                <div class="col-span-3 bg-#d7c6c5 text-#5f4241" style="background-color: #d7c6c5; color: #5f4241; border-radius: 0.5rem;">
                    @if ($campaignUsersData->isNotEmpty())
                        @foreach ($campaignUsersData as $data)
                        <p>
        Campanha: {{ $data['campaign'] }} | Usuários: {{ $data['users'] }} | Custo: R${{ number_format($data['adCost'], 2) }}
    </p>
                        @endforeach
                    @else
                        <p class="text-xl font-bold" style="font-size: x-large; font-weight: bold;">Nenhum dado disponível</p>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>


        <!-- Gráfico -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div id="custom_event_chart" style="width: 900px; height: 500px"></div>
        </div>

        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(drawCustomEventChart);

function drawCustomEventChart() {
    var data = new google.visualization.DataTable();
    data.addColumn('date', 'Data');
    data.addColumn('number', 'Ocorrências');

    console.log('Dados ordenados:');
    @foreach($customEventData as $event)
        console.log('{{ $event['date'] }}', parseInt('{{ $event['eventCount'] }}'));
    @endforeach

    @foreach($customEventData as $event)
        data.addRow([new Date('{{ $event['date'] }}'), parseInt('{{ $event['eventCount'] }}')]);
    @endforeach

    var options = {
        title: 'Evolução de Eventos',
        curveType: 'none',
        legend: { position: 'bottom' },
        hAxis: {
            format: 'yyyy-MM-dd',
            title: 'Data'
        },
        vAxis: {
            title: 'Número de Eventos'
        }
    };

    var chart = new google.visualization.LineChart(document.getElementById('custom_event_chart'));
    chart.draw(data, options);
}


        </script>
    </div>
</x-app-layout>
