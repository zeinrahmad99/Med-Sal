<?php

namespace App\Classes\Api\V1;

use Illuminate\Http\Request;

use App\Traits\Api\V1\Filters;
use Illuminate\Contracts\Database\Query\Builder;

abstract class QueryFilter
{
    use Filters;
    protected $request;

    protected $query;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query)
    {
        $this->query = $query;
        foreach ($this->filters() as $name => $value) {
            if (method_exists($this, $name)) {
                call_user_func_array([$this, $name], array_filter([$value]));
            }
        }
        return $this->query;
    }

    public function filters()
    {
        return $this->request->all();
    }



    public function buildQueryForLocationSearch($latitude, $longitude, $distance)
    {
        $haversineFormula = $this->calculateHaversineDistance($latitude, $longitude, 'service_locations.latitude', 'service_locations.longitude');

        return $this->query
            ->with([
                'services' => function ($query) {
                    $query->active();
                },
                'providers' => function ($query) {
                    $query->active();
                },
                'providers.serviceLocations'
            ])
            ->whereHas('providers.serviceLocations', function ($query) use ($haversineFormula, $distance) {
                $query->selectRaw("service_locations.*, $haversineFormula AS distance")
                    ->having('distance', '<=', $distance)
                    ->orderBy('distance');
            });
    }

    public function buildQueryForNearestServices($latitude, $longitude, $distance, $sortByDistance)
    {
        $query = $this->query
            ->select('categories.*')
            ->with([
                'services' => function ($query) {
                    $query->active(); // Apply the active() filter to services
                },
                'providers' => function ($query) {
                    $query->active(); // Apply the active() filter to providers
                }
            ])
            ->join('providers', 'categories.id', '=', 'providers.service_type_id')
            ->join('service_locations as sl', 'providers.id', '=', 'sl.provider_id')
            ->selectRaw($this->calculateHaversineDistance($latitude, $longitude, 'sl.latitude', 'sl.longitude') . ' AS distance')
            ->where('providers.deleted_at', null)
            ->having('distance', '<=', $distance);

        if ($sortByDistance) {
            $query->orderByRaw('distance');
        }

        return $query;
    }
}