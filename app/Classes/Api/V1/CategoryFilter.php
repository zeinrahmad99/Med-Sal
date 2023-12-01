<?php

namespace App\Classes\Api\V1;

use App\Models\Api\V1\Category;
use Illuminate\Support\Facades\DB;
use App\Traits\Api\V1\Filters;

class CategoryFilter extends QueryFilter
{
    use Filters;
    public function searchByCategoryName($name)
    {
        return $this->query
            ->where('name', 'like', '%' . $name . '%')
            ->active();
    }

    public function searchProductsByCategoryName($name)
    {
        return $this->query
            ->where('name', $name)
            ->with([
                'products' => function ($query) {
                    $query->active();
                }
            ]);
    }


    public function searchServicesByCategoryName($name)
    {
        return $this->query
            ->where('name', $name)
            ->with([
                'services' => function ($query) {
                    $query->active();
                }
            ]);
        ;
    }

    public function searchServicesProductsByCategoryName($name)
    {
        return $this->query
            ->where('name', $name)
            ->with([
                'services' => function ($query) {
                    $query->active();
                },
                'products' => function ($query) {
                    $query->active();
                }
            ]);
    }

    public function searchDoctorsByServiceName($serviceName)
    {
        return $this->query
            ->whereHas('services', function ($query) use ($serviceName) {
                $query->where('name', $serviceName)->active();
            })
            ->with([
                'providers' => function ($query) {
                    $query->active();
                }
            ])
            ->active();
    }

    public function searchServicesByLocation($location, $distance = 1)
    {
        [$latitude, $longitude] = explode(',', $location);

        return $this->buildQueryForLocationSearch($latitude, $longitude, $distance);
    }

    public function searchServicesByNearestDistance($location, $distance = 1, $sortByDistance = true)
    {
        [$latitude, $longitude] = explode(',', $location);

        return $this->buildQueryForNearestServices($latitude, $longitude, $distance, $sortByDistance);
    }

}