<?php

namespace Lxh\Admin;

use Closure;
use Lxh\Admin\Exception\Handle;
use Lxh\Admin\Form\Builder;
use Lxh\Admin\Form\Field;
use Lxh\Admin\Form\Field\File;
use Lxh\Admin\Form\Tab;
use Lxh\Database\Eloquent\Model;
use Lxh\Database\Eloquent\Relations\Relation;
use Lxh\Http\Request;
use Lxh\Support\Arr;
use Lxh\Support\Facades\DB;
use Lxh\Support\Facades\Input;
use Lxh\Support\MessageBag;
use Lxh\Support\Str;
use Lxh\Validation\Validator;
use Spatie\EloquentSortable\Sortable;
use Symfony\Component\HttpFoundation\Response;

class Form
{
    /**
     * Available fields.
     *
     * @var array
     */
    public static $availableFields = [
        'button'         => \Lxh\Admin\Form\Field\Button::class,
        'checkbox'       => \Lxh\Admin\Form\Field\Checkbox::class,
        'color'          => \Lxh\Admin\Form\Field\Color::class,
        'currency'       => \Lxh\Admin\Form\Field\Currency::class,
        'date'           => \Lxh\Admin\Form\Field\Date::class,
        'dateRange'      => \Lxh\Admin\Form\Field\DateRange::class,
        'datetime'       => \Lxh\Admin\Form\Field\Datetime::class,
        'dateTimeRange'  => \Lxh\Admin\Form\Field\DatetimeRange::class,
        'datetimeRange'  => \Lxh\Admin\Form\Field\DatetimeRange::class,
        'decimal'        => \Lxh\Admin\Form\Field\Decimal::class,
        'display'        => \Lxh\Admin\Form\Field\Display::class,
        'divider'        => \Lxh\Admin\Form\Field\Divide::class,
        'divide'         => \Lxh\Admin\Form\Field\Divide::class,
        'embeds'         => \Lxh\Admin\Form\Field\Embeds::class,
        'editor'         => \Lxh\Admin\Form\Field\Editor::class,
        'email'          => \Lxh\Admin\Form\Field\Email::class,
        'file'           => \Lxh\Admin\Form\Field\File::class,
        'hasMany'        => \Lxh\Admin\Form\Field\HasMany::class,
        'hidden'         => \Lxh\Admin\Form\Field\Hidden::class,
        'id'             => \Lxh\Admin\Form\Field\Id::class,
        'image'          => \Lxh\Admin\Form\Field\Image::class,
        'ip'             => \Lxh\Admin\Form\Field\Ip::class,
        'map'            => \Lxh\Admin\Form\Field\Map::class,
        'mobile'         => \Lxh\Admin\Form\Field\Mobile::class,
        'month'          => \Lxh\Admin\Form\Field\Month::class,
        'multipleSelect' => \Lxh\Admin\Form\Field\MultipleSelect::class,
        'number'         => \Lxh\Admin\Form\Field\Number::class,
        'password'       => \Lxh\Admin\Form\Field\Password::class,
        'radio'          => \Lxh\Admin\Form\Field\Radio::class,
        'rate'           => \Lxh\Admin\Form\Field\Rate::class,
        'select'         => \Lxh\Admin\Form\Field\Select::class,
        'selectTree'     => \Lxh\Admin\Form\Field\SelectTree::class,
        'slider'         => \Lxh\Admin\Form\Field\Slider::class,
        'switch'         => \Lxh\Admin\Form\Field\SwitchField::class,
        'text'           => \Lxh\Admin\Form\Field\Text::class,
        'textarea'       => \Lxh\Admin\Form\Field\Textarea::class,
        'time'           => \Lxh\Admin\Form\Field\Time::class,
        'timeRange'      => \Lxh\Admin\Form\Field\TimeRange::class,
        'url'            => \Lxh\Admin\Form\Field\Url::class,
        'year'           => \Lxh\Admin\Form\Field\Year::class,
        'html'           => \Lxh\Admin\Form\Field\Html::class,
        'tags'           => \Lxh\Admin\Form\Field\Tags::class,
        'icon'           => \Lxh\Admin\Form\Field\Icon::class,
        'multipleFile'   => \Lxh\Admin\Form\Field\MultipleFile::class,
        'multipleImage'  => \Lxh\Admin\Form\Field\MultipleImage::class,
        'captcha'        => \Lxh\Admin\Form\Field\Captcha::class,
        'tableCheckbox'          => \Lxh\Admin\Form\Field\TableCheckbox::class,
        'switching'          => \Lxh\Admin\Form\Field\Switching::class,
    ];
}
