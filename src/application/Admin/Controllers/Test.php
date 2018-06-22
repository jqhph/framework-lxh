<?php

namespace Lxh\Admin\Controllers;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Lxh\Mvc\Controller;



class Test extends Controller
{

    public function actionTest()
    {
        $mx = 800000;

        $arr = [];
        $a   = null;

        $start = microtime(true);
        for ($i = 0; $i < $mx; $i++) {
            $a = @$arr['test'];
        }

        dd("[@] useage time: " . (microtime(true) - $start));

        $start = microtime(true);
        for ($i = 0; $i < $mx; $i++) {
            $a = getvalue($arr, 'test');
        }

        dd("[getvalue] useage time: " . (microtime(true) - $start));

//        AnnotationRegistry::registerFile(alias('@root/application/Tests/Annotations/Test.php'));
//
//        $reader = new AnnotationReader();
//
//        $reflClass = new \ReflectionClass(\Lxh\Tests\Exp\User::class);
//        $classAnnotations = $reader->getClassAnnotations($reflClass);
//
//        ddd($classAnnotations, $reader->getMethodAnnotations($reflClass->getMethod('exec')));

    }
}
