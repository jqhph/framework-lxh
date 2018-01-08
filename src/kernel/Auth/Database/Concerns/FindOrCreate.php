<?php

namespace Lxh\Auth\Database\Concerns;

use Lxh\Auth\Clipboard;
use Lxh\Auth\Helpers;
use Lxh\Container\Container;
use Lxh\Support\Arr;
use Lxh\Support\Collection;

trait FindOrCreate
{
    /**
     * Find the given names, creating the names that don't exist yet.
     *
     * @param  iterable  $names
     * @return Collection
     */
    public function findOrCreate($names, array $attributes = [])
    {
        $items = Helpers::groupModelsAndIdentifiersByType($names);

        if ($items['integers']) {
            $items['integers'] = $this->where('id', 'IN', $items['integers'])->find();
        }

        if ($items['strings']) {
            $items['strings'] = $this->findOrCreateByName($items['strings'], $attributes);
        }

        return new Collection(Arr::collapse($items));
    }

    /**
     * Find by names, creating the ones that don't exist.
     *
     * @param  iterable  $names
     * @return Collection
     */
    protected function findOrCreateByName($names, array $attributes = [])
    {
        if (empty($names)) {
            return new Collection([]);
        }

        $rows = $this->where('name', 'IN', $names)->find() ?: [];

        $existing = (new Collection($rows))->keyBy('name');

        return (new Collection($names))
            ->diff($existing->pluck('name'))
            ->map(function ($name) use ($attributes) {
                return $this->createAndReturn(compact('name'), $attributes);
            })
            ->merge($existing);
    }

}
