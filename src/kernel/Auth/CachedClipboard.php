<?php

namespace Lxh\Auth;

use Lxh\Auth\Database\Models;

use Lxh\Auth\Cache\Storage;
use Lxh\Cache\CacheInterface;
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
     * The cache Storage.
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Constructor.
     *
     * @param CacheInterface  $cache
     */
    public function __construct(Model $user, CacheInterface $cache)
    {
        parent::__construct($user);

        $this->setCache($cache);
    }

    /**
     * Set the cache instance.
     *
     * @param  CacheInterface  $cache
     * @return $this
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Get the cache instance.
     *
     * @return CacheInterface
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
        if ($this->abilities) {
            return $this->abilities;
        }

        $key = $this->getCacheKey($this->user, 'abilities');

        if (is_array($abilities = $this->cache->get($key)) && $abilities) {
            return $this->abilities = new Collection($abilities);
        }

        $abilities = parent::getAbilities();

        $this->cache->set($key, $abilities->all());

        return $this->abilities = $abilities;
    }

    /**
     * Get an item from the cache, or Storage the default value forever.
     *
     * @param  string  $key
     * @param  callable  $callback
     * @return mixed
     */
    protected function sear($key, callable $callback)
    {
        if (is_null($value = $this->cache->get($key))) {
            $this->cache->set($key, $value = $callback());
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
        $this->refreshAllIteratively();

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
        $this->cache->delete($this->getCacheKey($authority, 'abilities'));
        $this->cache->delete($this->getCacheKey($authority, 'roles'));

        return $this;
    }

    /**
     * Refresh the cache for all roles and users, iteratively.
     *
     * @return void
     */
    protected function refreshAllIteratively()
    {
        foreach (Models::user()->find() as &$user) {
            $this->refreshFor(Models::user($user));
        }

        foreach (Models::role()->find() as &$role) {
            $this->refreshFor(Models::role($role));
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
        return '_auth_';
    }

}
