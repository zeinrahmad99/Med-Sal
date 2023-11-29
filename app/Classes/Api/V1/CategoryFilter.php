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

    // public function searchServicesProductsByLocation($location, $radius = 10)
    // {
    //     [$latitude, $longitude] = explode(',', $location);

    //     // Calculate the bounding box coordinates for the given radius
    //     $earthRadius = 6371; // Earth's radius in kilometers
    //     $maxLatitude = $latitude + rad2deg($radius / $earthRadius);
    //     $minLatitude = $latitude - rad2deg($radius / $earthRadius);
    //     $maxLongitude = $longitude + rad2deg($radius / $earthRadius / cos(deg2rad($latitude)));
    //     $minLongitude = $longitude - rad2deg($radius / $earthRadius / cos(deg2rad($latitude)));

    //     return $this->query->
    //         whereRelation('providers.serviceLocations', 'latitude', '>=', $minLatitude)
    //         ->whereRelation('providers.serviceLocations', 'latitude', '<=', $maxLatitude)
    //         ->whereRelation('providers.serviceLocations', 'longitude', '>=', $minLongitude)
    //         ->whereRelation('providers.serviceLocations', 'longitude', '<=', $maxLongitude)
    //         ->with('services');
    // }

    // public function distance($location)
    // {
    //     [$latitude, $longitude] = explode(',', $location);

    //     $earthRadius = 6371; // Radius of the Earth in kilometers

    //     return $this->query->whereHas('providers.serviceLocations', function ($query) use ($latitude, $longitude, $earthRadius) {
    //         $query->select(DB::raw("*,
    //         ($earthRadius * 2 * ASIN(SQRT(POWER(SIN(($latitude - service_locations.latitude) * pi()/180 / 2), 2) +
    //         COS($latitude * pi()/180) * COS(service_locations.latitude * pi()/180) *
    //         POWER(SIN(($longitude - service_locations.longitude) * pi()/180 / 2), 2)))) AS distance"))
    //             ->having('distance', '<=', 10) // Adjust the distance limit as needed (e.g., 10 kilometers)
    //             ->orderBy('distance', 'asc');
    //     })->with('services');
    // }

    // public function searchServicesProductsByLocation($location)
    // {
    //     [$latitude, $longitude] = explode(',', $location);

    //     return $this->query->
    //         // whereRelation('providers.serviceLocations', 'latitude', (float) $latitude, 'longitude', (float) $longitude)
    //         whereRelation(
    //             'providers.serviceLocations',
    //             'latitude',
    //             'like',
    //             '%' . $latitude . '%',
    //             'longitude',
    //             'like',
    //             '%' . $longitude . '%'
    //         )
    //         ->with('services');
    // }

    // , 'like', '%' . $longitude . '%'
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

    public function searchServicesProductsByLocation($location, $k = 3)
    {
        [$latitude, $longitude] = explode(',', $location);

        // Convert latitude and longitude to float
        $latitude = (float) $latitude;
        $longitude = (float) $longitude;

        // Initialize an array to store the distances and an array to store the corresponding service locations
        $distances = [];
        $serviceLocations = [];

        // Calculate the distance between the given location and each service location
        foreach ($this->query->get() as $serviceLocation) {
            $distance = $this->calculateDistance($latitude, $longitude, $serviceLocation->latitude, $serviceLocation->longitude);

            // Add the distance and service location to their respective arrays
            $distances[] = $distance;
            $serviceLocations[] = $serviceLocation;
        }

        // Find the k-nearest service locations
        array_multisort($distances, SORT_ASC, $serviceLocations);
        $nearestServiceLocations = array_slice($serviceLocations, 0, $k);

        // Filter the query to include only the providers that offer the k-nearest service locations
        $query = $this->query->where(function ($query) use ($nearestServiceLocations) {
            foreach ($nearestServiceLocations as $location) {
                $query->orWhereRelation('providers.serviceLocations', 'id', $location->id);
            }
        })->with('services');

        return $query;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth radius in km

        // Convert latitude and longitude to radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Apply the haversine formula
        $latDiff = $lat2 - $lat1;
        $lonDiff = $lon2 - $lon1;
        $distance = 2 * $earthRadius * asin(sqrt(pow(sin($latDiff / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($lonDiff / 2), 2)));

        return $distance;
    }

    public function searchByNearestDistance($location, $distanceLimit = 10)
    {
        [$latitude, $longitude] = explode(',', $location);
    
        // Convert latitude and longitude to float
        $latitude = (float) $latitude;
        $longitude = (float) $longitude;
    
        // Initialize an array to store the distances and an array to store the corresponding service locations
        $distances = [];
        $serviceLocations = [];
    
        // Calculate the distance between the given location and each service location
        foreach ($this->query->get() as $serviceLocation) {
            $distance = $this->calculateDistance($latitude, $longitude, $serviceLocation->latitude, $serviceLocation->longitude);
    
            // Add the distance and service location to their respective arrays if within the distance limit
            if ($distance <= $distanceLimit) {
                $distances[] = $distance;
                $serviceLocations[] = $serviceLocation;
            }
        }
    
        // Find the nearest service locations
        array_multisort($distances, SORT_ASC, $serviceLocations);
    
        // Filter the query to include only the providers that offer the nearest service locations
        $query = $this->query->where(function ($query) use ($serviceLocations) {
            foreach ($serviceLocations as $location) {
                $query->orWhereRelation('providers.serviceLocations', 'id', $location->id);
            }
        })->with('services');
    
        return $query;
    }
}