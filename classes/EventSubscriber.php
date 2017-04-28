<?php
/**
 * Created by PhpStorm.
 * User: saschaaeppli
 * Date: 28.04.17
 * Time: 16:04
 */

namespace Ffte\Search\Classes;

use Illuminate\Database\Eloquent\Model;

class EventSubscriber
{
    public function subscribe($events)
    {
        $events->listen('eloquent.saved*', '\Ffte\Search\Classes\EventSubscriber@saved');
        $events->listen('eloquent.deleted*', '\Ffte\Search\Classes\EventSubscriber@deleted');
    }

    public function saved($eventName, $payload = null)
    {
        $model = $this->getModelFromParams($eventName, $payload);
        if(!$this->hasSearchTrait($model)) {
            return true;
        }

        $model->updateSearchIndex();

        return true;
    }

    public function deleted($eventName, $payload = null)
    {
        $model = $this->getModelFromParams($eventName, $payload);
        if(!$this->hasSearchTrait($model)) {
            return true;
        }

        $model->removeSearchIndex();

        return true;
    }

    /**
     * @param string|Model $eventName
     * @param array|null   $payload
     *
     * @return Model
     */
    private function getModelFromParams($eventName, $payload = null)
    {
        if($eventName instanceof Model) {
            // Laravel < 5.4
            return $eventName;
        }

        // Laravel >= 5.4
        return $payload[0];
    }

    private function hasSearchTrait(Model $class, $autoload = false)
    {
        $traits = [];

        // Get traits of all parent classes
        do {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        } while ($class = get_parent_class($class));

        // Get traits of all parent traits
        $traitsToSearch = $traits;
        while (!empty($traitsToSearch)) {
            $newTraits = class_uses(array_pop($traitsToSearch), $autoload);
            $traits = array_merge($newTraits, $traits);
            $traitsToSearch = array_merge($newTraits, $traitsToSearch);
        };

        foreach ($traits as $trait => $same) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }

        $traits = array_unique($traits);

        return (isset($traits[SearchTrait::class]));
    }
}