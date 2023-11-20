<?php

namespace App\Classes\Api\V1;

use App\Models\Api\V1\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CategoryFilter extends QueryFilter
{
    public function search($name)
    {
        return $this->query->where('name', 'like', '%' . $name . '%');
    }

    public function searchProducts($name)
    {
        return $this->query->where('name', $name)
            ->with('products');
    }


    public function searchServices($name)
    {
        return $this->query->where('name', $name)
            ->with('services');
    }

    public function searchServicesProductsByName($name)
    {
        return $this->query->where('name', $name)
            ->with(['products', 'services']);
    }

    public function searchDoctorsBySpecialty($serviceName)
    {
        return $this->query
            ->whereHas('services', function ($query) use ($serviceName) {
                $query->where('name', $serviceName);
            })
            ->with('providers');
    }

    public function searchServicesProductsByLocation($location, $radius = 10)
    {
        [$latitude, $longitude] = explode(',', $location);

        // Calculate the bounding box coordinates for the given radius
        $earthRadius = 6371; // Earth's radius in kilometers
        $maxLatitude = $latitude + rad2deg($radius / $earthRadius);
        $minLatitude = $latitude - rad2deg($radius / $earthRadius);
        $maxLongitude = $longitude + rad2deg($radius / $earthRadius / cos(deg2rad($latitude)));
        $minLongitude = $longitude - rad2deg($radius / $earthRadius / cos(deg2rad($latitude)));

        return $this->query->
            whereRelation('providers.serviceLocations', 'latitude', '>=', $minLatitude)
            ->whereRelation('providers.serviceLocations', 'latitude', '<=', $maxLatitude)
            ->whereRelation('providers.serviceLocations', 'longitude', '>=', $minLongitude)
            ->whereRelation('providers.serviceLocations', 'longitude', '<=', $maxLongitude)
            ->with('services');
    }

    public function distance($location)
    {
        [$latitude, $longitude] = explode(',', $location);

        $earthRadius = 6371; // Radius of the Earth in kilometers

        return $this->query->whereHas('providers.serviceLocations', function ($query) use ($latitude, $longitude, $earthRadius) {
            $query->select(DB::raw("*,
            ($earthRadius * 2 * ASIN(SQRT(POWER(SIN(($latitude - service_locations.latitude) * pi()/180 / 2), 2) +
            COS($latitude * pi()/180) * COS(service_locations.latitude * pi()/180) *
            POWER(SIN(($longitude - service_locations.longitude) * pi()/180 / 2), 2)))) AS distance"))
                ->having('distance', '<=', 10) // Adjust the distance limit as needed (e.g., 10 kilometers)
                ->orderBy('distance', 'asc');
        })->with('services');
    }

    // public function searchServicesProductsByLocation($location)
    // {
    //     [$latitude, $longitude] = explode(',', $location);

    //     return $this->query->
    //         whereRelation('providers.serviceLocations', 'latitude', (float) $latitude, 'longitude', (float) $longitude)
    //         ->with('services');
    // }

    // public function searchServicesProductsByLocation($location)
    // {
    //     [$latitude, $longitude] = explode(',', $location);

    //     $latitudeRange = [$latitude - 0.01, $latitude + 0.01];
    //     $longitudeRange = [$longitude - 0.01, $longitude + 0.01];

    //     return $this->query->whereHas('providers.serviceLocations', function ($query) use ($latitudeRange, $longitudeRange) {
    //         $query->whereBetween('latitude', $latitudeRange)
    //             ->whereBetween('longitude', $longitudeRange);
    //     })->with('services');
    // }

}