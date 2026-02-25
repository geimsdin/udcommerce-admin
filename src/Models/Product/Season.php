<?php

namespace Unusualdope\LaravelEcommerce\Models\Product;

use Carbon\Carbon;
use Filament\Forms\Components\TextInput;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Unusualdope\LaravelModelTranslatable\Traits\HasTranslation;

class Season extends Model
{
    use Cachable, HasTranslation;

    // public function setTranslatableFilamentFields(): array
    // {
    //     return [
    //         TextInput::make('name')
    //             ->required()
    //             ->label('Name'),
    //     ];
    // }
    public function setTranslatableFields(): array
    {
        $this->translatable_fields = [
            'name' => 'string',
        ];

        return $this->translatable_fields;
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function (Season $season) {
            if ($season->products()->count() > 0) {
                throw new \Exception('Cannot delete season with assigned products.');
            }
            // Detach all related brands instead of deleting them
            $season->brands()->detach();
        });
    }

    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'brand_season')
            ->using(BrandSeason::class)
            ->withPivot(['date_start', 'date_end'])
            ->withTimestamps();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'season_product');
    }

    public function languages(): HasMany
    {
        return $this->hasMany(SeasonLanguage::class);
    }

    public function getNameAttribute()
    {
        return $this->currentLanguage->name;
    }

    public static function getActiveSeasons()
    {
        return self::where('date_start', '<=', Carbon::today())
            ->where('date_end', '>=', Carbon::today())
            ->with('brands')
            ->with('currentLanguage')
            ->get()
            ->keyBy('id');
    }

    public static function isReadyStock($season_id)
    {
        return self::where('id', $season_id)->where('ready_stock', 1)->exists();
    }

    public static function getReadyStockSeasons()
    {
        return self::where('ready_stock', 1)
            ->with('currentLanguage')
            ->get()
            ->keyBy('id');
    }

    public static function getFutureSeasons()
    {
        return self::where('date_start', '>', Carbon::today())
            ->where('date_start', '<', Carbon::today()->addDays(30))
            ->with('currentLanguage')
            ->get();
    }

    public static function export()
    {
        $seasons = [];
        foreach (self::all() as $season) {
            $seasons[] = [
                'NAME' => $season->getNameAttribute(),
                'START DATE' => $season->date_start ?? '',
                'END DATE' => $season->date_end ?? '',
                'READY STOCK' => $season->read_stock,
            ];
        }

        return $seasons;
    }
}
