<?php

namespace App\Traits\Api\V1;

trait Filters
{
    // Haversine formula to calculate the distance between two points on a sphere (Earth)
    public function calculateHaversineDistance($latitude, $longitude, $targetLatitude, $targetLongitude)
    {
        $haversine = "(
        6371 * acos(
            cos(radians(" . $latitude . "))
            * cos(radians(" . $targetLatitude . "))
            * cos(radians(" . $targetLongitude . ") - radians(" . $longitude . "))
            + sin(radians(" . $latitude . ")) * sin(radians(" . $targetLatitude . "))
        )
    )";

        return $haversine;
    }


}
// Do not remove , its for test later
// bquery base deed

// public function buildQueryForNearestServices($latitude, $longitude, $distance, $sortByDistance)
// {
//     $query = $this->query
//         ->select('categories.*')
//         ->with(['services', 'providers.serviceLocations'])
//         ->join('providers', 'categories.id', '=', 'providers.service_type_id')
//         ->join('service_locations as sl', 'providers.id', '=', 'sl.provider_id')
//         ->selectRaw($this->calculateHaversineDistance($latitude, $longitude, 'sl.latitude', 'sl.longitude') . ' AS distance')
//         ->where('providers.deleted_at', null)
//         ->having('distance', '<=', $distance);

//     if ($sortByDistance) {
//         $query->orderByRaw('distance');
//     }

//     return $query;
// }

// bquery base deed
// public function buildQueryForLocationSearch($latitude, $longitude, $distance)
// {
//     $haversineFormula = $this->calculateHaversineDistance($latitude, $longitude, 'service_locations.latitude', 'service_locations.longitude');

//     return $this->query
//         ->with(['services', 'providers.serviceLocations'])
//         ->whereHas('providers.serviceLocations', function ($query) use ($haversineFormula, $distance) {
//             $query->selectRaw("service_locations.*, $haversineFormula AS distance")
//                 ->having('distance', '<=', $distance)
//                 ->orderBy('distance');
//         });
// }

// base last
// public function searchServicesByLocation($location, $distance = 1)
// {
//     [$latitude, $longitude] = explode(',', $location);
//     $haversine = $this->calculateHaversineDistance($latitude, $longitude, 'service_locations.latitude', 'service_locations.longitude');

//     return $this->query
//         ->with(['services', 'providers.serviceLocations'])
//         ->whereHas('providers.serviceLocations', function ($query) use ($haversine, $distance) {
//             $query->selectRaw("service_locations.*, $haversine AS distance")
//                 ->having('distance', '<=', $distance)
//                 ->orderBy('distance');
//         });
// }

// base last
// public function searchServicesByNearestDistance($location, $distance = 1, $sortByDistance = true)
// {
//     [$latitude, $longitude] = explode(',', $location);
//     $haversine = $this->calculateHaversineDistance($latitude, $longitude, 'sl.latitude', 'sl.longitude');

//     $query = $this->query
//         ->select('categories.*')
//         ->with(['services', 'providers.serviceLocations'])
//         ->join('providers', 'categories.id', '=', 'providers.service_type_id')
//         ->join('service_locations as sl', 'providers.id', '=', 'sl.provider_id')
//         ->selectRaw("$haversine AS distance")
//         ->where('providers.deleted_at', null)
//         ->having('distance', '<=', $distance);

//     if ($sortByDistance) {
//         $query->orderByRaw('distance');
//     }

//     return $query;
// }
// base last dood
// public function searchServicesByLocation($location, $distance = 1)
// {
//     [$latitude, $longitude] = explode(',', $location);

//     $haversine = "(
//     6371 * acos(
//         cos(radians(" . $latitude . "))
//         * cos(radians(service_locations.latitude))
//         * cos(radians(service_locations.longitude) - radians(" . $longitude . "))
//         + sin(radians(" . $latitude . ")) * sin(radians(service_locations.latitude))
//     )
// )";

//     return $this->query
//         ->with(['services', 'providers.serviceLocations'])
//         ->whereHas('providers.serviceLocations', function ($query) use ($haversine, $distance) {
//             $query->selectRaw("service_locations.*, $haversine AS distance")
//                 ->having('distance', '<=', $distance)
//                 ->orderBy('distance');
//         });
// }

// base last dood
// public function searchServicesByNearestDistance($location, $distance = 1, $sortByDistance = true)
// {
//     [$latitude, $longitude] = explode(',', $location);

//     // To search by miles instead of kilometers, replace 6371 with 3959.
//     $haversine = "(
//         6371 * acos(
//             cos(radians(" . $latitude . "))
//             * cos(radians(sl.latitude))
//             * cos(radians(sl.longitude) - radians(" . $longitude . "))
//             + sin(radians(" . $latitude . ")) * sin(radians(sl.latitude))
//         )
//     )";

//     $query = $this->query
//         ->select('categories.*')
//         ->with(['services', 'providers.serviceLocations'])
//         ->join('providers', 'categories.id', '=', 'providers.service_type_id')
//         ->join('service_locations as sl', 'providers.id', '=', 'sl.provider_id')
//         ->selectRaw("$haversine AS distance")
//         ->where('providers.deleted_at', null)
//         ->having('distance', '<=', $distance);

//     if ($sortByDistance) {
//         $query->orderByRaw('distance');
//     }

//     return $query;
// }

// old base
// public function searchServicesByLocation($location)
// {
//     [$latitude, $longitude] = explode(',', $location);

//     return $this->query->
//         whereRelation('providers.serviceLocations', 'latitude', (float) $latitude, 'longitude', (float) $longitude)
//         // whereRelation(
//         //     'providers.serviceLocations',
//         //     'latitude',
//         //     'like',
//         //     '%' . $latitude . '%',
//         //     'longitude',
//         //     'like',
//         //     '%' . $longitude . '%'
//         // )
//         ->with('services');
// }

// base
// public function searchServicesByLocation($location, $distance = 1)
// {
//     [$latitude, $longitude] = explode(',', $location);

//     $haversine = "(
//     6371 * acos(
//         cos(radians(" . $latitude . "))
//         * cos(radians(service_locations.latitude))
//         * cos(radians(service_locations.longitude) - radians(" . $longitude . "))
//         + sin(radians(" . $latitude . ")) * sin(radians(service_locations.latitude))
//     )
// )";

//     return $this->query
//         ->with(['services', 'providers.serviceLocations'])
//         ->whereHas('providers.serviceLocations', function ($query) use ($haversine, $distance) {
//             $query->selectRaw("*, $haversine AS distance")
//                 ->having('distance', '<=', $distance)
//                 ->orderBy('distance');
//         });
// }

// old base
// public function searchServicesByLocation($location, $maxDistance = 10)
// {
//     [$latitude, $longitude] = explode(',', $location);

//     $circleRadius = 6371; // Earth radius in kilometers

//     return $this->query
//         ->whereHas('providers.serviceLocations', function ($query) use ($latitude, $longitude, $circleRadius, $maxDistance) {
//             $query->selectRaw('service_locations.*, (' . $circleRadius . ' * acos(cos(radians(' . $latitude . ')) * cos(radians(service_locations.latitude)) *
//                 cos(radians(service_locations.longitude) - radians(' . $longitude . ')) +
//                 sin(radians(' . $latitude . ')) * sin(radians(service_locations.latitude)))) AS distance')
//                 ->having('distance', '<', $maxDistance)
//                 ->orderBy('distance');
//         })
//         ->with('services');
// }
// old deed
// public function searchServicesByLocation($location, $maxDistance = 100)
// {
//     [$latitude, $longitude] = explode(',', $location);

//     $circleRadius = 6371; // Earth radius in kilometers

//     return $this->query
//         ->whereHas('providers.serviceLocations', function ($query) use ($latitude, $longitude, $circleRadius, $maxDistance) {
//             $query->selectRaw('service_locations.*, (' . $circleRadius . ' * acos(cos(radians(' . $latitude . ')) * cos(radians(service_locations.latitude)) *
//             cos(radians(service_locations.longitude) - radians(' . $longitude . ')) +
//             sin(radians(' . $latitude . ')) * sin(radians(service_locations.latitude)))) AS distance')
//                 ->having('distance', '<', $maxDistance)
//                 ->orderBy('distance');
//         })
//         ->with([
//             'services',
//             'providers.serviceLocations' => function ($query) use ($latitude, $longitude) {
//                 $query->select(['latitude', 'longitude']);
//             }
//         ]);
// }

// old deed
// public function searchServicesByLocation($location)
// {
//     [$latitude, $longitude] = explode(',', $location);

//     return $this->query
//         ->whereHas('providers.serviceLocations', function ($query) use ($latitude, $longitude) {
//             $query->where('latitude', 'LIKE', '%' . $latitude . '%')
//                 ->where('longitude', 'LIKE', '%' . $longitude . '%');
//         })
//         ->with('services');
// }

// old base
// public function searchServicesByLocation($location, $maxDistance = 10)
// {
//     [$latitude, $longitude] = explode(',', $location);

//     $earthRadius = 6371; // Approximate radius of the Earth in kilometers

//     return $this->query
//         ->whereHas('providers.serviceLocations', function ($query) use ($latitude, $longitude, $earthRadius, $maxDistance) {
//             $query->select('service_locations.*')
//                 ->selectRaw(
//                     '(? * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
//                     [$earthRadius, $latitude, $longitude, $latitude]
//                 )
//                 ->having('distance', '<=', $maxDistance)
//                 ->orderBy('distance', 'asc');
//         })
//         ->with('services');
// }

// old base
// public function searchServicesByLocation($location, $radius = 2)
// {
//     [$latitude, $longitude] = explode(',', $location);

//     $services = $this->query
//         ->whereHas('providers.serviceLocations', function ($query) use ($latitude, $longitude, $radius) {
//             $query->selectRaw(
//                 '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) as distance',
//                 [$latitude, $longitude, $latitude]
//             )
//             ->having('distance', '<', $radius);
//         })
//         ->with('services')
//         ->get();

//     return $services;
// }

// old base
// public function searchServicesByLocation($location)
// {
//     [$latitude, $longitude] = explode(',', $location);

//     // Services located at the exact location
//     $servicesAtLocation = $this->query
//         ->whereRelation('providers.serviceLocations', 'latitude', (float) $latitude)
//         ->whereRelation('providers.serviceLocations', 'longitude', (float) $longitude)
//         ->with('services')
//         ->get();

//     // Services near the given location
//     $servicesNearLocation = $this->query
//         ->whereHas('providers.serviceLocations', function ($query) use ($latitude, $longitude) {
//             $radius = 10; // Define the radius in kilometers for nearby services
//             $query->whereRaw(
//                 "ST_Distance_Sphere(point(longitude, latitude), point(?, ?)) * 0.001 <= ?",
//                 [$longitude, $latitude, $radius]
//             );
//         })
//         ->with('services')
//         ->get();

//     $combinedServices = $servicesAtLocation->concat($servicesNearLocation);

//     return [
//         'combinedServices' => $combinedServices,
//     ];
// }

// base
// public function searchServicesByLocation($location, $radius = 10)
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


// , 'like', '%' . $longitude . '%'
// old base
// public function searchServicesByLocation($location)
// {
//     [$latitude, $longitude] = explode(',', $location);

//     $latitudeRange = [$latitude - 0.01, $latitude + 0.01];
//     $longitudeRange = [$longitude - 0.01, $longitude + 0.01];

//     return $this->query->whereHas('providers.serviceLocations', function ($query) use ($latitudeRange, $longitudeRange) {
//         $query->whereBetween('latitude', $latitudeRange)
//             ->whereBetween('longitude', $longitudeRange);
//     })->with('services');
// }

// base
// public function searchServicesByLocation($location, $k = 3)
// {
//     [$latitude, $longitude] = explode(',', $location);

//     // Convert latitude and longitude to float
//     $latitude = (float) $latitude;
//     $longitude = (float) $longitude;

//     // Initialize an array to store the distances and an array to store the corresponding service locations
//     $distances = [];
//     $serviceLocations = [];

//     // Calculate the distance between the given location and each service location
//     foreach ($this->query->get() as $serviceLocation) {
//         $distance = $this->calculateDistance($latitude, $longitude, $serviceLocation->latitude, $serviceLocation->longitude);

//         // Add the distance and service location to their respective arrays
//         $distances[] = $distance;
//         $serviceLocations[] = $serviceLocation;
//     }

//     // Find the k-nearest service locations
//     array_multisort($distances, SORT_ASC, $serviceLocations);
//     $nearestServiceLocations = array_slice($serviceLocations, 0, $k);

//     // Filter the query to include only the providers that offer the k-nearest service locations
//     $query = $this->query->where(function ($query) use ($nearestServiceLocations) {
//         foreach ($nearestServiceLocations as $location) {
//             $query->orWhereRelation('providers.serviceLocations', 'id', $location->id);
//         }
//     })->with('services');

//     return $query;
// }

// base
// private function calculateDistance($lat1, $lon1, $lat2, $lon2)
// {
//     $earthRadius = 6371; // Earth radius in km

//     // Convert latitude and longitude to radians
//     $lat1 = deg2rad($lat1);
//     $lon1 = deg2rad($lon1);
//     $lat2 = deg2rad($lat2);
//     $lon2 = deg2rad($lon2);

//     // Apply the haversine formula
//     $latDiff = $lat2 - $lat1;
//     $lonDiff = $lon2 - $lon1;
//     $distance = 2 * $earthRadius * asin(sqrt(pow(sin($latDiff / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($lonDiff / 2), 2)));

//     return $distance;
// }

// base
// public function searchByNearestDistance($location, $distanceLimit = 10)
// {
//     [$latitude, $longitude] = explode(',', $location);

//     // Convert latitude and longitude to float
//     $latitude = (float) $latitude;
//     $longitude = (float) $longitude;

//     // Initialize an array to store the distances and an array to store the corresponding service locations
//     $distances = [];
//     $serviceLocations = [];

//     // Calculate the distance between the given location and each service location
//     foreach ($this->query->get() as $serviceLocation) {
//         $distance = $this->calculateDistance($latitude, $longitude, $serviceLocation->latitude, $serviceLocation->longitude);

//         // Add the distance and service location to their respective arrays if within the distance limit
//         if ($distance <= $distanceLimit) {
//             $distances[] = $distance;
//             $serviceLocations[] = $serviceLocation;
//         }
//     }

//     // Find the nearest service locations
//     array_multisort($distances, SORT_ASC, $serviceLocations);

//     // Filter the query to include only the providers that offer the nearest service locations
//     $query = $this->query->where(function ($query) use ($serviceLocations) {
//         foreach ($serviceLocations as $location) {
//             $query->orWhereRelation('providers.serviceLocations', 'id', $location->id);
//         }
//     })->with('services');

//     return $query;
// }

// old base 
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