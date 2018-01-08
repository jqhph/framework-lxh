<?php

namespace Lxh\Auth;

use Lxh\Auth\Database\Models;

use Lxh\Auth\Cache\Store;
use Lxh\MVC\Model;
use Lxh\Support\Collection;

class CachedClipboard extends Clipboard
{
    /**
     * The tag used for caching.
     *
     * @var string
     */
    protected $tag = 'Lxh-auth';

    /**
     * The cache store.
     *
     * @var \Lxh\Auth\Cache\Store
     */
    protected $cache;

    /**
     * Constructor.
     *
     * @param \Lxh\Auth\Cache\Store  $cache
     */
    public function __construct(Model $user, Store $cache)
    {
        parent::__construct($user);

        $this->setCache($cache);
    }

    /**
     * Set the cache instance.
     *
     * @param  \Lxh\Auth\Cache\Store  $cache
     * @return $this
     */
    public function setCache(Store $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Get the cache instance.
     *
     * @return \Lxh\Auth\Cache\Store
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Get the given authority's abilities.
     *
     * @return Collection
     */
    public function getAbilities()
    {
        $key = $this->getCacheKey($this->user, 'abilities');

        if (is_array($abilities = $this->cache->get($key)) && $abilities) {
            return $abilities;
        }

        $abilities = $this->getFreshAbilities();

        $this->cache->forever($key, $this->serializeAbilities($abilities));

        return $abilities;
    }

    /**
     * Get a fresh copy of the given authority's abilities.
     *
     * @param  Model  $authority
     * @param  bool  $allowed
     * @return Collection
     */
    public function getFreshAbilities(Model $authority)
    {
        return parent::getAbilities($authority);
    }

    /**
     * Get the given authority's roles.
     *
     * @param  Model  $authority
     * @return \Lxh\Support\Collection
     */
    public function getRoles()
    {
        $key = $this->getCacheKey($this->user, 'roles');

        return $this->sear($key, function () {
            return parent::getRoles();
        });
    }

    /**
     * Get an item from the cache, or store the default value forever.
     *
     * @param  string  $key
     * @param  callable  $callback
     * @return mixed
     */
    protected function sear($key, callable $callback)
    {
        if (is_null($value = $this->cache->get($key))) {
            $this->cache->forever($key, $value = $callback());
        }

        return $value;
    }

    /**
     * Clear the cache.
     *
     * @param  Model  $authority
     * @return $this
     */
    public function refresh()
    {
        return $this->refreshFor();
    }

    /**
     * @return $this
     */
    public function refreshAll()
    {
        if ($this->cache) {
            $this->cache->flush();
        } else {
            $this->refreshAllIteratively();
        }

        return $this;
    }

    /**
     * Clear the cache for the given authority.
     *
     * @param  Model  $authority
     * @return $this
     */
    public function refreshFor(Model $authority = null)
    {
        $authority = $authority ?: $this->user;
        $this->cache->forget($this->getCacheKey($authority, 'abilities'));
        $this->cache->forget($this->getCacheKey($authority, 'roles'));

        return $this;
    }

    /**
     * Refresh the cache for all roles and users, iteratively.
     *
     * @return void
     */
    protected function refreshAllIteratively()
    {
        foreach (Models::user()->all() as $user) {
            $this->refreshFor($user);
        }

        foreach (Models::role()->all() as $role) {
            $this->refreshFor($role);
        }
    }

    /**
     * Get the cache key for the given model's cache type.
     *
     * @param  Model  $model
     * @param  string  $type
     * @param  bool  $allowed
     * @return string
     */
    protected function getCacheKey(Model $model, $type)
    {
        return implode('-', [
            $this->tag(),
            $type,
            '_auth_',
            $model->getId()
        ]);
    }

    /**
     * Get the cache tag.
     *
     * @return string
     */
    protected function tag()
    {
        return $this->user->getId();
    }

    /**
     * Serialize a collection of ability models into a plain array.
     *
     * @param  Collection  $abilities
     * @return array
     */
    protected function serializeAbilities(Collection $abilities)
    {
        return $abilities->map(function ($ability) {
            return $ability->all();
        })->all();
    }
}
