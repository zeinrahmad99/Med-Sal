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

    // Create a new QueryFilter instance.
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    // Apply the filters to the query.
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

    // Get the filters from the request.
    public function filters()
    {
        return $this->request->all();
    }

    public function buildQueryForLocationSearch($latitude, $longitude, $distance)
    {
        $haversineFormula = $this->calculateHaversineDistance($latitude, $longitude, 'service_locations.latitude', 'service_locations.longitude');

        if (app()->getLocale() == 'en') {
            return $this->query->select('id', 'admin_id', 'name', 'description', 'status')
                ->with([
                    'services' => fn($query) => $query->active()->select('id', 'category_id', 'service_location_id', 'name', 'description', 'price', 'discount', 'status', 'created_at', 'updated_at'),
                    'providers' => fn($query) => $query->active(),
                    'providers.serviceLocations'
                ])
                ->whereHas(
                    'providers.serviceLocations',
                    fn($query) => $query
                        ->selectRaw("service_locations.*, $haversineFormula AS distance")
                        ->having('distance', '<=', $distance)
                        ->orderBy('distance')
                );
        } else {
            return $this->query->select('id', 'admin_id', 'name_ar', 'description_ar', 'status')
                ->with([
                    'services' => fn($query) => $query->active()->select('id', 'category_id', 'service_location_id', 'name_ar', 'description_ar', 'price', 'discount', 'status', 'created_at', 'updated_at'),
                    'providers' => fn($query) => $query->active(),
                    'providers.serviceLocations'
                ])
                ->whereHas(
                    'providers.serviceLocations',
                    fn($query) => $query
                        ->selectRaw("service_locations.*, $haversineFormula AS distance")
                        ->having('distance', '<=', $distance)
                        ->orderBy('distance')
                );
        }
    }


    // Build the query for searching nearest services.
    public function buildQueryForNearestServices($latitude, $longitude, $distance, $sortByDistance)
    {
        if (app()->getLocale() == 'en') {

            $query = $this->query
                // ->select('categories.*')
                ->select('categories.id', 'categories.admin_id', 'categories.name', 'categories.description', 'categories.status')
                ->with([
                    'services' => fn($query) => $query->active()->select('id', 'category_id', 'service_location_id', 'name', 'description', 'price', 'discount', 'status', 'created_at', 'updated_at'),
                    'providers' => fn($query) => $query->active(),
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
        } else {

            $query = $this->query
                // ->select('categories.*')
                ->select('categories.id', 'categories.admin_id', 'categories.name_ar', 'categories.description_ar', 'categories.status')
                ->with([
                    'services' => fn($query) => $query->active()->select('id', 'category_id', 'service_location_id', 'name_ar', 'description_ar', 'price', 'discount', 'status', 'created_at', 'updated_at'),
                    'providers' => fn($query) => $query->active(),
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
}
