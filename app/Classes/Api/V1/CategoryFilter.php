<?php

namespace App\Classes\Api\V1;

use App\Traits\Api\V1\Images;
use App\Models\Api\V1\Product;
use App\Traits\Api\V1\Filters;
use App\Models\Api\V1\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\V1\ProductController;

class CategoryFilter extends QueryFilter
{
    use Filters, Images;

    // Search categories by name.
    public function searchByCategoryName($name)
    {
        $query = $this->query->where('name', 'like', '%' . $name . '%');

        if ($query->count() > 0) {
            $query->select('id', 'admin_id', 'name', 'description', 'status');
        } else {
            $query = $this->query->orWhere('name_ar', 'like', '%' . $name . '%');
            $query->select('id', 'admin_id', 'name_ar', 'description_ar', 'status');
        }

        return $query->active();
    }

    // Search products within a category by name.
    public function searchProductsByCategoryName($name)
    {
        $query = $this->query->where('name', 'like', '%' . $name . '%');

        if ($query->count() > 0) {
            $query->select('id', 'admin_id', 'name', 'description', 'status');
            return $query->with([
                'products' => fn($query) => $query->select('id', 'provider_id', 'category_id', 'name', 'description', 'images', 'price', 'discount', 'quantity', 'status', 'created_at', 'updated_at')
                    ->active(),
            ]);
        } else {
            $query = $this->query->orWhere('name_ar', 'like', '%' . $name . '%');
            $query->select('id', 'admin_id', 'name_ar', 'description_ar', 'status');
            return $query->with([
                'products' => fn($query) => $query->select('id', 'provider_id', 'category_id', 'name_ar', 'description_ar', 'images', 'price', 'discount', 'quantity', 'status', 'created_at', 'updated_at')
                    ->active(),
            ]);
        }

    }

    // Search services within a category by name.
    public function searchServicesByCategoryName($name)
    {
        $query = $this->query->where('name', 'like', '%' . $name . '%');

        if ($query->count() > 0) {
            $query->select('id', 'admin_id', 'name', 'description', 'status');
            return $query->with([
                'services' => fn($query) => $query->select('id', 'category_id', 'service_location_id', 'name', 'description', 'price', 'discount', 'status', 'created_at', 'updated_at')
                    ->active(),
            ]);
        } else {
            $query = $this->query->orWhere('name_ar', 'like', '%' . $name . '%');
            $query->select('id', 'admin_id', 'name_ar', 'description_ar', 'status');
            return $query->with([
                'services' => fn($query) => $query->select('id', 'category_id', 'service_location_id', 'name_ar', 'description_ar', 'price', 'discount', 'status', 'created_at', 'updated_at')
                    ->active(),
            ]);
        }
    }

    // Search services and products within a category by name.
    public function searchServicesProductsByCategoryName($name)
    {
        $query = $this->query->where('name', 'like', '%' . $name . '%');

        if ($query->count() > 0) {
            $query->select('id', 'admin_id', 'name', 'description', 'status');
            return $query->with([
                'services' => fn($query) => $query->select('id', 'category_id', 'service_location_id', 'name', 'description', 'price', 'discount', 'status', 'created_at', 'updated_at')
                    ->active(),
                'products' => fn($query) => $query->select('id', 'provider_id', 'category_id', 'name', 'description', 'images', 'price', 'discount', 'quantity', 'status', 'created_at', 'updated_at')
                    ->active(),
            ]);
        } else {
            $query = $this->query->orWhere('name_ar', 'like', '%' . $name . '%');
            $query->select('id', 'admin_id', 'name_ar', 'description_ar', 'status');
            return $query->with([
                'services' => fn($query) => $query->select('id', 'category_id', 'service_location_id', 'name_ar', 'description_ar', 'price', 'discount', 'status', 'created_at', 'updated_at')
                    ->active(),
                'products' => fn($query) => $query->select('id', 'provider_id', 'category_id', 'name_ar', 'description_ar', 'images', 'price', 'discount', 'quantity', 'status', 'created_at', 'updated_at')
                    ->active(),
            ]);
        }
    }

    // Search doctors by service name within a category.
    public function searchDoctorsByServiceName($name)
    {
        $query = $this->query
            ->whereHas('services', fn($query) => $query
                ->where('name', 'like', '%' . $name . '%'));

        if ($query->count() > 0) {
            $query->select('id', 'admin_id', 'name', 'description', 'status');
        } else {
            $query = $this->query
                ->orWhereHas('services', fn($query) => $query
                    ->where('name_ar', 'like', '%' . $name . '%'));

            $query->select('id', 'admin_id', 'name_ar', 'description_ar', 'status');
        }

        return $query->with([
            'providers' => fn($query) => $query->active()
        ])->active();
    }

    // Search services within a certain distance from a location.
    public function searchServicesByLocation($location, $distance = 1)
    {
        [$latitude, $longitude] = explode(',', $location);

        return $this->buildQueryForLocationSearch($latitude, $longitude, $distance);
    }

    // Search services within the nearest distance from a location.
    public function searchServicesByNearestDistance($location, $distance = 1, $sortByDistance = true)
    {
        [$latitude, $longitude] = explode(',', $location);

        return $this->buildQueryForNearestServices($latitude, $longitude, $distance, $sortByDistance);
    }

}