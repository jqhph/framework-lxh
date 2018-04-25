<?php

namespace Lxh\Admin\Controllers;

use Lxh\Admin\Http\Controllers\Controller;
use Lxh\Install\Install as Installer;
use Lxh\Install\Step1;
use Lxh\Install\Step2;
use Lxh\Install\Step3;

class Install extends Controller
{
    protected function initialize()
    {
    }

    public function actionInstall(array $params)
    {
        $step = get_value($params, 'step', 1);

        switch ($step) {
            case 1:
                return $this->step1();
            case 2:
                return $this->step2();
            case 3:
                return $this->step3();
            default:
                return $this->step1();

        }
    }

    protected function step1()
    {
        $step = new Step1($this->admin()->content());

        return $step->build();
    }

    protected function step2()
    {
        $step = new Step2($this->admin()->content());

        if ($this->request->isPOST()) {
            return $step->setupDatabaseConfig();
        }

        return $step->build();
    }

    protected function step3()
    {
        $step = new Step3($this->admin()->content());

        if ($this->request->isPOST()) {
            return $step->install();
        }

        return $step->build();
    }
}