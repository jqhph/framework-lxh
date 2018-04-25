<?php

namespace Lxh\Install;

class Validator
{
    /**
     * @var string
     */
    protected $error;

    /**
     * @var string
     */
    protected $helperUrl;

    /**
     * @var array
     */
    protected $options = [
        'version' => __VERSION__,
        'requiredphpversion' => '5.5',
    ];

    public function __construct($helper)
    {
        $this->helperUrl = $helper;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        if (!$this->checkPhpVersion()) {
            return false;
        }

        return true;
    }

    public function getError()
    {
        return $this->error;
    }

    /**
     * 检查php版本
     *
     * @return bool
     */
    protected function checkPhpVersion()
    {
        if (version_compare(PHP_VERSION, $this->options['requiredphpversion'], '>=' )) {
            return true;
        }
        $this->error = sprintf(
            trans('You cannot install because <a target="_blank" href="%1$s">Lxh Framework %2$s</a> requires PHP version %3$s or higher. You are running PHP version %4$s.'),
            $this->helperUrl,
            $this->options['version'],
            $this->options['requiredphpversion'],
            PHP_VERSION
        );

        return false;
    }
}
