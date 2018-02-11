<?php

namespace Lxh\Admin\Fields;

use Lxh\Admin\Admin;
use Lxh\Exceptions\InvalidArgumentException;
use Lxh\MVC\Controller;

class Switcher extends Field
{
    /**
     * @var int
     */
    protected $uncheckedValue = 0;

    public function primary()
    {
        return $this->attribute('data-color', '#00b19d');
    }

    public function info()
    {
        return $this->attribute('data-color', '#3bafda');
    }

    public function warning()
    {
        return $this->attribute('data-color', '#ffaa00');
    }

    public function inverse()
    {
        return $this->attribute('data-color', '#4c5667');
    }

    public function danger()
    {
        return $this->attribute('data-color', '#ef5350');
    }

    public function purple()
    {
        return $this->attribute('data-color', '#5b69bc');
    }

    /**
     *
     * @param $color
     * @return $this
     */
    public function secondary($color)
    {
        return $this->attribute('data-secondary-color', $color);
    }

    /**
     * @return $this
     */
    public function small()
    {
        return $this->attribute('data-size', 'small');
    }

    /**
     * @return $this
     */
    public function large()
    {
        return $this->attribute('data-size', 'large');
    }

    /**
     * @return $this
     */
    public function disabled()
    {
        return $this->attribute('disabled', 'disabled');
    }

    /**
     * @param $color
     * @return $this
     */
    public function color($color)
    {
        return $this->attribute('data-color', $color);
    }

    /**
     * 设置选中后表单值
     *
     * @param $value
     * @return $this
     */
    public function inputValue($value)
    {
        return $this->attribute('value', $value);
    }

    /**
     * 设置未选中表单值
     *
     * @param $value
     * @return $this
     */
    public function uncheckedValue($value)
    {
        $this->uncheckedValue = $value;
        return $this;
    }

    public function render()
    {
        $this->css('Switching', '@lxh/plugins/switchery/switchery.min');
        $this->js('Switching', '@lxh/plugins/switchery/switchery.min');

        if (!$id = $this->getModelId()) {
            throw new InvalidArgumentException("Id not found!");
        }
        $this->attribute('data-pk', $id);

        $url = Admin::url()->updateField('{id}');

        $this->script('Switching', <<<EOF
(function(){
var s=$('[data-switchery="1"]'),r=0,list={},checked,_new,__;
function b(){
    s.each(function(k){
        __ = \$(this);
        if (__.attr('icd'))
            __.prop('checked',true);
        else
            __.prop('checked',false);
        list[k]=new Switchery(__[0],$(this).data())
    })
} 
b();
s.change(function(e) {
    if (r) return;
    r=1; NProgress.start();
    var t=$(this);val = t.val(),ntf=\$lxh.ui().notify(),id=t.data('pk'),u='{$url}',all=$('.switchery'),checked=t.is(':checked');
    all.addClass('disabled');
    if (!checked)
        val = '{$this->uncheckedValue}';
    $.post(u.replace('{id}',id), {name:'{$this->name}',value:val}, function (d) {
        r=0; NProgress.done(); all.removeClass('disabled');
        if (checked) 
            t.attr('icd',1);
        else 
            t.removeAttr('icd');
        all.remove(); b();
            
        if (d.status)
            ntf.success(d.msg);
        else
            ntf.error(d.msg)
    }, 'JSON');
});
})();
EOF
        );

        $this->small();
        if (empty($this->attributes['data-color'])) {
            $this->primary();
        }
        if (empty($this->attributes['value'])) {
            $this->attributes['value'] = 1;
        }

        $checked = $this->value ? 'checked' : '';
        $icd = $checked ? 'icd="1"' : '';

        return <<<EOF
<input li='{$this->items->offset()}' name="{$this->name}" $icd $checked type="checkbox" data-switchery="1" {$this->formatAttributes()}/>
EOF;
    }

}
