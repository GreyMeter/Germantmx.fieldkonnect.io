<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class OrderDispactchDetails extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'order_dispatch_po_no',
        'driver_name',
        'driver_contact_number',
        'vehicle_number'
    ];

    public function dispactOrder()
    {
        return $this->hasMany(OrderDispactch::class, 'order_dispatch_po_no', 'dispatch_po_no');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('tc')
            ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
            ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
            ->singleFile();
        $this->addMediaCollection('invoice')
            ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
            ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
            ->singleFile();
        $this->addMediaCollection('e_way_bill')
            ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
            ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
            ->singleFile();
        $this->addMediaCollection('wevrage_slip')
            ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
            ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
            ->singleFile();
    }
}
