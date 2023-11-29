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
        return $this->query->where('name', 'like', '%' . $name . '%');
    }

    public function searchProductsByCategoryName($name)
    {
        return $this->query->where('name', $name)->with('products');
    }


    public function searchServicesByCategoryName($name)
    {
        return $this->query->where('name', $name)->with('services');
    }

    public function searchServicesProductsByCategoryName($name)
    {
        return $this->query->where('name', $name)->with(['products', 'services']);
    }

    public function searchDoctorsByServiceName($serviceName)
    {
        return $this->query
            ->whereHas('services', function ($query) use ($serviceName) {
                $query->where('name', $serviceName);
            })
            ->with('providers');
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