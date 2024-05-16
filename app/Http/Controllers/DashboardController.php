<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleAnalyticsService;
use Carbon\Carbon; // Inclui o uso do Carbon para manipulação de datas
use Illuminate\Support\Facades\Auth;  // Importe o facade Auth corretamente
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $analyticsService;

    public function __construct(GoogleAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index(Request $request)
    {
        $periodo = $request->input('periodo', 15);
    

  // Eventos específicos
// Assumindo que o usuário está logado e você pode acessar o modelo User diretamente
$user = Auth::user();

// Buscando os nomes dos eventos armazenados nos campos do usuário
$events = [
    $user->evento_3 => 'Topo de Funil',
    $user->evento_2 => 'Meio de funil',
    $user->custom_event => 'Conversão Principal'
];

// Filtrando para remover possíveis valores nulos
    $eventNames = array_keys(array_filter($events));
    $labels = array_filter($events);

// Chamada para buscar os totais de eventos específicos
$multipleEventsData = $this->analyticsService->getMultipleEventsTotal($periodo, $eventNames);

  // Chamada para buscar os dados de usuários vindos de campanhas
  $campaignUsersData = $this->analyticsService->getUsersFromCampaigns($periodo);
  


    // Preparando dados para serem exibidos na view
    $displayData = [];
    foreach ($multipleEventsData as $eventName => $data) {
        $displayData[] = [
            'label' => $labels[$eventName],
            'totalEvents' => $data['totalEvents'],
            'eventName' => $eventName, // Nome real do evento como lido no Analytics ou no banco de dados
            'period' => $data['period']
        ];
    }

        // Chamada original para buscar dados do gráfico
        $eventData = $this->analyticsService->getEventDataForGraph($periodo);
    
        return view('dashboard', [
            'campaignUsersData' => $campaignUsersData,
            'multipleEventsData' => $multipleEventsData,
            'eventsData' => $displayData,
            'customEventData' => $eventData,
        'eventNames' => $eventNames,
        'period' => $periodo,
        ]);
    }
}
