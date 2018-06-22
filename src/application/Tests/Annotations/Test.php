<?php

namespace Lxh\Tests\Annotations;

/**
 *
 * @Annotation
 * @Target({"METHOD","CLASS"})
 */
class Test
{
    protected $pro1;

    public function __construct($v)
    {
        dd($v);
        $this->handle('fuck');
    }

    public function handle($v)
    {
        echo 2311111111;
    }
}
