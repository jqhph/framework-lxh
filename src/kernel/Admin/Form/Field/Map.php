<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Admin;
use Lxh\Admin\Form\Field;

class Map extends Field
{
    /**
     * @var string
     */
    protected $view = 'admin::form.map';

    /**
     * Column name.
     *
     * @var string
     */
    protected $column = [];

    /**
     * 是否使用goole地图
     *
     * @var bool
     */
    protected static $google = false;

    /**
     * 地图容器高度
     *
     * @var string
     */
    protected $height = '400px';

    /**
     * @var int
     */
    protected $zoom = 10;

    /**
     * @var array
     */
    protected $defaultValue = [];

    /**
     *
     * @return $this
     */
    public function google()
    {
        static::$google = true;

        return $this;
    }

    public function __construct($column, $arguments)
    {
        $this->column = $column;

        $this->defaultValue['lat'] = get_value($arguments, 0);
        $this->defaultValue['lng'] = get_value($arguments, 1);

        array_shift($arguments);

        $this->label = $this->formatLabel($this->column);
        $this->id = $this->formatId($this->defaultValue);

        $this->name = [
            'lat' => $column . '-lat',
            'lng' => $column . '-lng',
        ];

    }

    /**
     * 设置地图容器高度
     *
     * @param string $height
     * @return $this
     */
    public function height($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     *
     * @return array
     */
    protected function variables()
    {
        $vars = parent::variables(); // TODO: Change the autogenerated stub

        $vars['height'] = $this->height;
        $vars['defaultValue'] = &$this->defaultValue;

        return $vars;
    }

    public function render()
    {
        /*
        * Google map is blocked in mainland China
        * people in China can use Tencent map instead(;
        */
        if (!static::$google) {
            $this->useTencentMap();
        } else {
            $this->useGoogleMap();
        }

        if (!static::$google) {
            $js = 'https://map.qq.com/api/js?v=2.exp'; // &Key='.config('admin.qq-map-key')
        } else {
            $js = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&key='.config('admin.google-map-key', 'AIzaSyBzE9xAESye6Kde-3hT-6B90nfwUkcS8Yw');
        }
        // 以同步的形式引入地图js
        Admin::loadScript($js);

        return parent::render(); // TODO: Change the autogenerated stub
    }

    public function useGoogleMap()
    {
        $this->script = <<<EOT
function initGoogleMap(name) {
    var lat = $('#{$this->id['lat']}');
    var lng = $('#{$this->id['lng']}');
    var search = $('.search-map'), 
        container = document.getElementById("map_"+name),
        map,
        marker;

    var LatLng = new google.maps.LatLng(lat.val(), lng.val());
    
    build();
    
    google.maps.event.addListener(marker, 'dragend', function (event) {
        lat.val(event.latLng.lat());
        lng.val(event.latLng.lng());
    });
    
    search.off('click');
    search.click(function () {
        LatLng = new google.maps.LatLng(lat.val(), lng.val());
        build();
    });
    {$this->publicScript()};
    
    function build() {
        var options = {
            zoom: {$this->zoom},
            center: LatLng,
            panControl: false,
            zoomControl: true,
            scaleControl: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        map = new google.maps.Map(container, options);
    
        marker = new google.maps.Marker({
            position: LatLng,
            map: map,
            title: 'Drag Me!',
            draggable: true
        });
    }
}

initGoogleMap('{$this->id['lat']}{$this->id['lng']}');
EOT;
    }

    /**
     * 只允许输入数值
     *
     * @return string
     */
    protected function publicScript()
    {
        return <<<EOF
lat.on('keyup', isfloat);
lng.on('keyup', isfloat);
function isfloat() {
    var v = $(this).val();
    v && $(this).val(v.replace(/[^0-9.-]/gi, ''));
}
EOF;

    }

    public function useTencentMap()
    {
        $this->script = <<<EOT
function initTencentMap(name) {
    var lat = $('#{$this->id['lat']}'),
        lng = $('#{$this->id['lng']}'),
        search = $('.search-map');
        center = new qq.maps.LatLng(lat.val(), lng.val()),
        n = NProgress;

    var container = document.getElementById("map_"+name);
    var map = new qq.maps.Map(container, {
        center: center,
        zoom: {$this->zoom},
    });

    var marker = new qq.maps.Marker({
        position: center,
        draggable: true,
        map: map
    });
    
    var cs = new qq.maps.CityService({
        map: map,
        complete: function(result){
            map.setCenter(result.detail.latLng);
            marker.setPosition(result.detail.latLng);
            n.done();
        }
    });

    if( ! lat.val() || ! lng.val()) {
        cs.searchLocalCity();
    }
    qq.maps.event.addListener(map, 'click', function(event) {
        marker.setPosition(event.latLng);
    });

    qq.maps.event.addListener(marker, 'position_changed', function(event) {
        var position = marker.getPosition();
        lat.val(position.getLat());
        lng.val(position.getLng());
    });
    
    search.off('click');
    search.click(function () {
        n.done(); n.start();
        cs.searchCityByLatLng(center = new qq.maps.LatLng(lat.val(), lng.val()));
    });
    
    {$this->publicScript()};
}

initTencentMap('{$this->id['lat']}{$this->id['lng']}');
EOT;
    }

}
