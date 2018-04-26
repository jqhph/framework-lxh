<?php

namespace Lxh\Filters;

use Lxh\Container\Container;
use Lxh\Support\Str;

class Filter
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * The registered filters.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * The wildcard filters.
     *
     * @var array
     */
    protected $wildcards = [];

    /**
     * The sorted event filters.
     *
     * @var array
     */
    protected $sorted = [];

    /**
     * @var array
     */
    protected $latests = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $tag
     * @param string|callable $filter ($value, $latestValue ...)
     * @param int $priority
     * @return $this
     */
    public function add($tag, $filter, $priority = 0)
    {
        if (strpos($tag, '*') !== false) {
            $this->wildcards[$tag][] = $filter;
        } else {
            $this->filters[$tag][$priority][] = $filter;

            unset($this->sorted[$tag]);
        }
        return $this;
    }

    /**
     * @param string $tag
     * @return array|mixed|null
     */
    public function apply($tag)
    {
        if (! $tag) return null;
        
        $args = func_get_args();
        // 第二个参数为需要过滤的值
        $args[0] = $this->latests[$tag] = getvalue($args, 1);

        foreach ($this->getFilters($tag) as &$filter) {
            $this->latests[$tag] = call_user_func_array($this->resolveFilter($filter), $args);
            $args[1] = $this->latests[$tag];

            if ($this->latests[$tag] === false) {
                return $this->latests[$tag];
            }
        }

        return $this->latests[$tag];
    }

    /**
     * 获取最后一次过滤器过滤后的内容
     *
     * @param $tag
     * @return bool|mixed
     */
    public function getLatestValue($tag)
    {
        return isset($this->latests[$tag]) ? $this->latests[$tag] : false;
    }

    /**
     * @param $tag
     * @return bool
     */
    public function has($tag)
    {
        return isset($this->filters[$tag]);
    }

    /**
     * Register an event filter with the dispatcher.
     *
     * @param  mixed  $filter
     * @return mixed
     */
    public function resolveFilter($filter)
    {
        if (is_string($filter)) {
            $segments = explode('@', $filter);

            return [
                $this->container->make($segments[0]),
                !empty($segments[1]) ? $segments[1] : 'handle'
            ];
        }

        return $filter;
    }

    /**
     * Get all of the filters for a given event name.
     *
     * @param  string  $tag
     * @return array
     */
    public function getFilters($tag)
    {
        if (! isset($this->sorted[$tag])) {
            $this->sortFilters($tag);
        }

        return $this->sorted[$tag];
    }

    /**
     * Sort the filters for a given event by priority.
     *
     * @param  string $tag
     * @return array
     */
    protected function sortFilters($tag)
    {
        $this->sorted[$tag] = [];

        // If filters exist for the given event, we will sort them by the priority
        // so that we can call them in the correct order. We will cache off these
        // sorted event filters so we do not have to re-sort on every events.
        if (isset($this->filters[$tag])) {
            krsort($this->filters[$tag]);

            $this->sorted[$tag] = call_user_func_array(
                'array_merge', $this->filters[$tag]
            );
        }
    }

    /**
     * Get the wildcard filters for the tag.
     *
     * @param  string $tag
     * @return array
     */
    protected function getWildcardFilters($tag)
    {
        $wildcards = [];

        foreach ($this->wildcards as $key => & $filters) {
            if (Str::is($key, $tag)) {
                $wildcards = array_merge($wildcards, $filters);
            }
        }

        return $wildcards;
    }
}
