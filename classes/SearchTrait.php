<?php
namespace Ffte\Search\Classes;

use DB;
use App;

use Ffte\Search\Models\SearchIndex;
use RainLab\Translate\Models\Locale;

trait SearchTrait
{

    public static function scopeSearch($query, $args)
    {
        $locale = App::getLocale();

        $ids = SearchIndex
            ::select('model_id')
            ->where('locale', $locale)
            ->where('model_type', self::class)
            ->where('value', 'like', "%$args%")
            ->distinct()
            ->lists('model_id');

        return $query->whereIn('id', $ids);
    }

    public static function reindex()
    {
        SearchIndex::where('model_type', self::class)->delete();

        Locale::all()->each(function($locale) {
            self::all()->each(function($entity) use($locale) {
                self::reindexOne($entity, $locale);
            });
        });
    }

    public function updateSearchIndex() {
        Locale::all()->each(function($locale) {
            self::reindexOne($this, $locale);
        });
    }

    private static function reindexOne($entity, $locale)
    {
        $searchIndex = $entity->getSearchIndex($locale->code);

        foreach($searchIndex as $item => $value) {
            SearchIndex::updateOrCreate([
                'locale' => $locale->code,
                'model_type' => self::class,
                'model_id' => $entity->id,
                'item' => $item
            ], ['value' => $value]);
        }
    }

    public function removeSearchIndex()
    {
        SearchIndex
            ::where('model_id', $this->id)
            ->delete();
    }
}