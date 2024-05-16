<?php

namespace App\Services;

use Google\Client;
use Google\Service\AnalyticsData;
use Google\Service\AnalyticsData\DateRange;
use Google\Service\AnalyticsData\Dimension;
use Google\Service\AnalyticsData\Filter;
use Google\Service\AnalyticsData\FilterExpression;
use Google\Service\AnalyticsData\Metric;
use Google\Service\AnalyticsData\RunReportRequest;
use Google\Service\AnalyticsData\StringFilter;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GoogleAnalyticsService
{
    protected $analyticsData;

    public function __construct()
    {
        $client = new Client();
        $client->setAuthConfig(base_path(env('GOOGLE_APPLICATION_CREDENTIALS')));
        $client->addScope(AnalyticsData::ANALYTICS_READONLY);
        $this->analyticsData = new AnalyticsData($client);
    }
    
// Get Event Data For Graph
public function getEventDataForGraph($periodo)
{
    $eventData = collect();

    if (!Auth::check() || !Auth::user()->googleid || !Auth::user()->custom_event) {
        Log::info("User not logged in, googleid or custom_event name not set.");
        return $eventData; // Retorna uma coleção vazia.
    }

    $propertyId = 'properties/' . Auth::user()->googleid;
    $eventName = Auth::user()->custom_event;

    $startDate = Carbon::now()->subDays($periodo)->format('Y-m-d');
    $endDate = Carbon::now()->format('Y-m-d');

    $dateRange = new DateRange([
        'startDate' => $startDate,
        'endDate' => $endDate,
    ]);

    $dateDimension = new Dimension(['name' => 'date']);
    $eventCountMetric = new Metric(['name' => 'eventCount']);

    $filter = new Filter([
        'fieldName' => 'eventName',
        'stringFilter' => new StringFilter([
            'value' => $eventName,
            'matchType' => 'EXACT',
        ]),
    ]);

    // Cria o objeto RunReportRequest
    $request = new RunReportRequest([
        'dateRanges' => [$dateRange],
        'dimensions' => [$dateDimension],
        'metrics' => [$eventCountMetric],
        'dimensionFilter' => new FilterExpression(['filter' => $filter]),
    ]);

    try {
        $response = $this->analyticsData->properties->runReport($propertyId, $request);

        if ($response->getRows()) {
            foreach ($response->getRows() as $row) {
                $eventData->push([
                    'date' => Carbon::createFromFormat('Ymd', $row->getDimensionValues()[0]->getValue())->format('Y-m-d'),
                    'eventCount' => (int) $row->getMetricValues()[0]->getValue(),
                ]);
            }
            $eventData = $eventData->sortBy('date')->values(); // Assegurar que os dados são ordenados
        }
    } catch (\Exception $e) {
        Log::error("Error fetching Google Analytics data for {$propertyId} and {$eventName}: " . $e->getMessage());
    }

    return $eventData;
}
//Função pega eventos em string
public function getMultipleEventsTotal($periodo, array $eventNames)
{
    if (!Auth::check()) {
        Log::info("User not logged in.");
        return [];
    }

    $results = [];
    $propertyId = 'properties/' . Auth::user()->googleid;
    $startDate = Carbon::now()->subDays($periodo)->format('Y-m-d');
    $endDate = Carbon::now()->format('Y-m-d');

    foreach ($eventNames as $eventName) {
        $dateRange = new DateRange(['startDate' => $startDate, 'endDate' => $endDate]);
        $eventCountMetric = new Metric(['name' => 'eventCount']);
        $filter = new Filter([
            'fieldName' => 'eventName',
            'stringFilter' => new StringFilter(['value' => $eventName, 'matchType' => 'EXACT'])
        ]);

        $request = new RunReportRequest([
            'dateRanges' => [$dateRange],
            'metrics' => [$eventCountMetric],
            'dimensionFilter' => new FilterExpression(['filter' => $filter]),
        ]);

        try {
            $response = $this->analyticsData->properties->runReport($propertyId, $request);
            $totalEvents = 0;
            if ($response->getRows()) {
                foreach ($response->getRows() as $row) {
                    $totalEvents += (int) $row->getMetricValues()[0]->getValue();
                }
            }
            $results[$eventName] = ['totalEvents' => $totalEvents, 'period' => $periodo];
        } catch (\Exception $e) {
            Log::error("Error fetching Google Analytics data for {$propertyId} and {$eventName}: " . $e->getMessage());
            $results[$eventName] = ['totalEvents' => 0, 'period' => $periodo];
        }
    }

    return $results;
}
public function getUsersFromCampaigns($periodo)
{
    if (!Auth::check()) {
        Log::info("User not logged in.");
        return [];
    }

    $propertyId = 'properties/' . Auth::user()->googleid;
    $startDate = Carbon::now()->subDays($periodo)->format('Y-m-d');
    $endDate = Carbon::now()->format('Y-m-d');

    $dateRange = new DateRange(['startDate' => $startDate, 'endDate' => $endDate]);
    $campaignDimension = new Dimension(['name' => 'campaignName']);
    $totalUsersMetric = new Metric(['name' => 'totalUsers']);
    $adCostMetric = new Metric(['name' => 'advertiserAdCost']);

    $request = new RunReportRequest([
        'dateRanges' => [$dateRange],
        'dimensions' => [$campaignDimension],
        'metrics' => [$totalUsersMetric, $adCostMetric],
    ]);

    try {
        $response = $this->analyticsData->properties->runReport($propertyId, $request);
        $campaignData = collect();
        if ($response->getRows()) {
            foreach ($response->getRows() as $row) {
                $campaignData->push([
                    'campaign' => $row->getDimensionValues()[0]->getValue(),
                    'users' => (int) $row->getMetricValues()[0]->getValue(),
                    'adCost' => (float) $row->getMetricValues()[1]->getValue(),
                ]);
            }
        }
        return $campaignData;
    } catch (\Exception $e) {
        Log::error("Error fetching campaign data: " . $e->getMessage());
        return collect();  // Returning an empty collection on failure
    }
}




}
