<?php

namespace Lxh\Helper\Valitron;

use InvalidArgumentException;

/**
 * Validation Class https://github.com/vlucas/valitron
 *
 * Validates input against certain criteria
 * $v = new Validator();
    $v->fill(['name' => '张三', 'email' => 'jqh@163.com'])
    ->rule('name', 'required')
    ->rule('myemail', 'email');

    if ($v->validate()) {
        echo "Yay! We're all good!<br>";
    } else {
        // Errors
        debug($v->errors());
    }
 *
 * @package Valitron
 * @author Vance Lucas <vance@vancelucas.com>
 * @link http://www.vancelucas.com/
 */
class Validator
{
    /**
     * @var string
     */
    const ERROR_DEFAULT = 'Invalid';

    /**
     * @var array
     */
    protected $_fields = array();

    /**
     * @var array
     */
    protected $_errors = null;

    /**
     * @var array
     */
    protected $_validations = array();

    /**
     * @var array
     */
    protected $_labels = array();

    /**
     * @var string
     */
    protected static $_lang = 'zh-cn';

    /**
     * @var string
     */
    protected static $_langDir;

    /**
     * @var array
     */
    protected static $_rules = array();

    /**
     * @var array
     */
    protected static $_ruleMessages = array();

    /**
     * @var array
     */
    protected $validUrlPrefixes = array('http://', 'https://', 'ftp://');

    
    /**
     * Setup validation
     *
     * @param  array                     $data
     * @param  array                     $fields
     * @param  string                    $lang
     * @param  string                    $langDir
     * @throws \InvalidArgumentException
     */
    public function fill(array & $data)
    {
    	$this->reset();
    	// Allows filtering of used input fields against optional second array of field names allowed
    	// This is useful for limiting raw $_POST or $_GET data to only known fields
    	$this->_fields = & $data;
    	
    	// set lang in the follow order: constructor param, static::$_lang, default to en
    	$lang = self::$_lang;
    	
    	// set langDir in the follow order: constructor param, static::$_langDir, default to package lang dir
    	$langDir = self::$_langDir ?: 'lang';
    	
    	// Load language file in directory
    	$langFile = "$langDir/$lang.php";
//     	if (is_file($langFile)) {
    	static::$_ruleMessages = include $langFile;
//     	} else {
//     		throw new \InvalidArgumentException("fail to load language file '$langFile'");
//     	}
    	return $this;
    }
    
    /**
     * 设置语言包
     * */
    public function lang($lang)
    {
    	self::$_lang = $lang;
    	return $this;
    }

    /**
     * 设置语言包路径
     * */
    public function langDir($file)
    {
    	self::$_langDir = $file;
    	return $this;
    }

    /**
     * Required field validator
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateRequired($field, $value)
    {
//    	return $value !== null;
         if (is_null($value)) {
             return false;
         } elseif (trim($value) === '') {
             return false;
         }

         return true;
    }

    /**
     * Validate that two values match
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateEquals($field, $value, array $params)
    {
        $field2 = $params[0];

        return isset($this->_fields[$field2]) && $value == $this->_fields[$field2];
    }

    /**
     * Validate that a field is different from another field
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateDifferent($field, $value, array $params)
    {
        $field2 = $params[0];

        return isset($this->_fields[$field2]) && $value != $this->_fields[$field2];
    }

    /**
     * Validate that a field was "accepted" (based on PHP's string evaluation rules)
     *
     * This validation rule implies the field is "required"
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateAccepted($field, $value)
    {
        $acceptable = array('yes', 'on', 1, '1', true);

        return $this->validateRequired($field, $value) && in_array($value, $acceptable, true);
    }

    /////////////////////////////////////////

    /**
     * 验证输入字段中是否含有该字段
     *
     * @return bool
     */
    protected function validatePresent($field, $value)
    {
        return isset($this->_fields[$field]);
    }

    protected function validateSize($field, $value, $param)
    {
        return $this->getSize($field, $value) == $param[0];
    }

    // 验证的字段必须是有效的时区标识符，会根据 PHP 函数 timezone_identifiers_list 来判断。
    protected function validateTimezone($field, $value, $param)
    {
        try {
            new \DateTimeZone($value);
        } catch (\Exception $e) {
            return false;
        } catch (\Throwable $e) {
            return false;
        }
        return true;
    }

    /////////////////////////////////////////////

    /**
     * Get the size of an attribute.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return mixed
     */
    protected function getSize($attribute, $value)
    {
        // This method will determine if the attribute is a number, string, or file and
        // return the proper size accordingly. If it is a number, then number itself
        // is the size. If it is a file, we take kilobytes, and for a string the
        // entire length of the string will be considered the attribute size.
        if (is_numeric($value)) {
            return $value;
        } elseif (is_array($value)) {
            return count($value);
        }

        return mb_strlen($value);
    }

    /**
     * Validate that a field is an array
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateArray($field, $value)
    {
        return is_array($value);
    }

    /**
     * Validate that a field is numeric
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateNumeric($field, $value)
    {
        return is_numeric($value);
    }

    /**
     * Validate that a field is an integer
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateInteger($field, $value)
    {
        return filter_var($value, \FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Validate the length of a string
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateLength($field, $value, array $params)
    {
        $length = $this->stringLength($value);
        // Length between
        if (isset($params[1])) {
            return $length >= $params[0] && $length <= $params[1];
        }
        // Length same
        return ($length !== false) && $length == $params[0];
    }

    /**
     * Validate the length of a string (between)
     *
     * @param  string  $field
     * @param  mixed   $value
     * @param  array   $params
     * @return boolean
     */
    protected function validateLengthBetween($field, $value, array $params)
    {
        $length = $this->stringLength($value);

        return ($length !== false) && $length >= $params[0] && $length <= $params[1];
    }

    protected function validateBetween($field, $value, array $params)
    {
        return $value >= $params[0] && $value <= $params[1];
    }

    /**
     * Validate the length of a string (min)
     *
     * @param string $field
     * @param mixed  $value
     * @param array  $params
     *
     * @return boolean
     */
    protected function validateLengthMin($field, $value, $params)
    {
        $length = $this->stringLength($value);

        return ($length !== false) && $length >= $params[0];
    }

    /**
     * Validate the length of a string (max)
     *
     * @param string $field
     * @param mixed  $value
     * @param array  $params
     *
     * @return boolean
     */
    protected function validateLengthMax($field, $value, $params)
    {
        $length = $this->stringLength($value);

        return ($length !== false) && $length <= $params[0];
    }

    /**
     * Get the length of a string
     *
     * @param  string $value
     * @return int|false
     */
    protected function stringLength($value)
    {
        if (!is_string($value)) {
            return false;
        } elseif (function_exists('mb_strlen')) {
            return mb_strlen($value);
        }

        return strlen($value);
    }

    /**
     * Validate the size of a field is greater than a minimum value.
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateMin($field, $value, $params)
    {
        if (!is_numeric($value)) {
            return false;
        } elseif (function_exists('bccomp')) {
            return !(bccomp($params[0], $value, 14) == 1);
        } else {
            return $params[0] <= $value;
        }
    }

    /**
     * Validate the size of a field is less than a maximum value
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateMax($field, $value, $params)
    {
        if (!is_numeric($value)) {
            return false;
        } elseif (function_exists('bccomp')) {
            return !(bccomp($value, $params[0], 14) == 1);
        } else {
            return $params[0] >= $value;
        }
    }

    /**
     * Validate a field is contained within a list of values
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateIn($field, $value, $params)
    {
        $isAssoc = array_values($params[0]) !== $params[0];
        if ($isAssoc) {
            $params[0] = array_keys($params[0]);
        }

        $strict = false;
        if (isset($params[1])) {
            $strict = $params[1];
        }

        return in_array($value, $params[0], $strict);
    }

    /**
     * Validate a field is not contained within a list of values
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateNotIn($field, $value, $params)
    {
        return !$this->validateIn($field, $value, $params);
    }

    /**
     * Validate a field contains a given string
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @return bool
     */
    protected function validateContains($field, $value, $params)
    {
        if (!isset($params[0])) {
            return false;
        }
        if (!is_string($params[0]) || !is_string($value)) {
            return false;
        }

        return (strpos($value, $params[0]) !== false);
    }

    /**
     * Validate that a field is a valid IP address
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateIp($field, $value)
    {
        return filter_var($value, \FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Validate that a field is a valid e-mail address
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateEmail($field, $value)
    {
        return filter_var($value, \FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate that a field is a valid URL by syntax
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateUrl($field, $value)
    {
        foreach ($this->validUrlPrefixes as & $prefix) {
            if (strpos($value, $prefix) !== false) {
                return filter_var($value, \FILTER_VALIDATE_URL) !== false;
            }
        }

        return false;
    }

    /**
     * Validate that a field is an active URL by verifying DNS record
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateUrlActive($field, $value)
    {
        foreach ($this->validUrlPrefixes as $prefix) {
            if (strpos($value, $prefix) !== false) {
                $host = parse_url(strtolower($value), PHP_URL_HOST);
                
                return checkdnsrr($host, 'A') || checkdnsrr($host, 'AAAA') || checkdnsrr($host, 'CNAME');
            }
        }

        return false;
    }

    /**
     * Validate that a field contains only alphabetic characters
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateAlpha($field, $value)
    {
        return preg_match('/^([a-z])+$/i', $value);
    }

    /**
     * Validate that a field contains only alpha-numeric characters
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateAlphaNum($field, $value)
    {
        return preg_match('/^([a-z0-9])+$/i', $value);
    }

    /**
     * Validate that a field contains only alpha-numeric characters, dashes, and underscores
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateSlug($field, $value)
    {
        return preg_match('/^([-a-z0-9_-])+$/i', $value);
    }

    /**
     * Validate that a field passes a regular expression check
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @return bool
     */
    protected function validateRegex($field, $value, $params)
    {
        return preg_match($params[0], $value);
    }

    /**
     * Validate that a field is a valid date
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateDate($field, $value)
    {
        $isDate = false;
        if ($value instanceof \DateTime) {
            $isDate = true;
        } else {
            $isDate = strtotime($value) !== false;
        }

        return $isDate;
    }

    /**
     * Validate that a field matches a date format
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateDateFormat($field, $value, $params)
    {
        $parsed = date_parse_from_format($params[0], $value);

        return $parsed['error_count'] === 0 && $parsed['warning_count'] === 0;
    }

    /**
     * Validate the date is before a given date
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateDateBefore($field, $value, $params)
    {
        $vtime = ($value instanceof \DateTime) ? $value->getTimestamp() : strtotime($value);
        $ptime = ($params[0] instanceof \DateTime) ? $params[0]->getTimestamp() : strtotime($params[0]);

        return $vtime < $ptime;
    }

    /**
     * Validate the date is after a given date
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateDateAfter($field, $value, $params)
    {
        $vtime = ($value instanceof \DateTime) ? $value->getTimestamp() : strtotime($value);
        $ptime = ($params[0] instanceof \DateTime) ? $params[0]->getTimestamp() : strtotime($params[0]);

        return $vtime > $ptime;
    }

    /**
     * Validate that a field contains a boolean.
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateBoolean($field, $value)
    {
        return (is_bool($value)) ? true : false;
    }

    /**
     * Validate that a field contains a valid credit card
     * optionally filtered by an array
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @return bool
     */
    protected function validateCreditCard($field, $value, $params)
    {
        /**
         * I there has been an array of valid cards supplied, or the name of the users card
         * or the name and an array of valid cards
         */
        if (!empty($params)) {
            /**
             * array of valid cards
             */
            if (is_array($params[0])) {
                $cards = $params[0];
            } elseif (is_string($params[0])) {
                $cardType  = $params[0];
                if (isset($params[1]) && is_array($params[1])) {
                    $cards = $params[1];
                    if (!in_array($cardType, $cards)) {
                        return false;
                    }
                }
            }
        }
        /**
         * Luhn algorithm
         *
         * @return bool
         */
        $numberIsValid = function () use ($value) {
            $number = preg_replace('/[^0-9]+/', '', $value);
            $sum = 0;

            $strlen = strlen($number);
            if ($strlen < 13) {
                return false;
            }
            for ($i = 0; $i < $strlen; $i++) {
                $digit = (int) substr($number, $strlen - $i - 1, 1);
                if ($i % 2 == 1) {
                    $sub_total = $digit * 2;
                    if ($sub_total > 9) {
                        $sub_total = ($sub_total - 10) + 1;
                    }
                } else {
                    $sub_total = $digit;
                }
                $sum += $sub_total;
            }
            if ($sum > 0 && $sum % 10 == 0) {
                    return true;
            }

            return false;
        };

        if ($numberIsValid()) {
            if (!isset($cards)) {
                return true;
            } else {
                $cardRegex = array(
                    'visa'          => '#^4[0-9]{12}(?:[0-9]{3})?$#',
                    'mastercard'    => '#^5[1-5][0-9]{14}$#',
                    'amex'          => '#^3[47][0-9]{13}$#',
                    'dinersclub'    => '#^3(?:0[0-5]|[68][0-9])[0-9]{11}$#',
                    'discover'      => '#^6(?:011|5[0-9]{2})[0-9]{12}$#',
                );

                if (isset($cardType)) {
                    // if we don't have any valid cards specified and the card we've been given isn't in our regex array
                    if (!isset($cards) && !in_array($cardType, array_keys($cardRegex))) {
                        return false;
                    }

                    // we only need to test against one card type
                    return (preg_match($cardRegex[$cardType], $value) === 1);

                } elseif (isset($cards)) {
                    // if we have cards, check our users card against only the ones we have
                    foreach ($cards as $card) {
                        if (in_array($card, array_keys($cardRegex))) {
                            // if the card is valid, we want to stop looping
                            if (preg_match($cardRegex[$card], $value) === 1) {
                                return true;
                            }
                        }
                    }
                } else {
                    // loop through every card
                    foreach ($cardRegex as $regex) {
                        // until we find a valid one
                        if (preg_match($regex, $value) === 1) {
                            return true;
                        }
                    }
                }
            }
        }

        // if we've got this far, the card has passed no validation so it's invalid!
        return false;
    }

    protected function validateInstanceOf($field, $value, $params)
    {
        $isInstanceOf = false;
        if (is_object($value)) {
            if (is_object($params[0]) && $value instanceof $params[0]) {
                $isInstanceOf = true;
            }
            if (get_class($value) === $params[0]) {
                $isInstanceOf = true;
            }
        }
        if (is_string($value)) {
            if (is_string($params[0]) && get_class($value) === $params[0]) {
                $isInstanceOf = true;
            }
        }

        return $isInstanceOf;
    }

    //Validate optional field
    protected function validateOptional($field, $value, $params) {
        //Always return true
        return true;
    }

    /**
     *  Get array of fields and data
     *
     * @return array
     */
    public function data()
    {
        return $this->_fields;
    }

    /**
     * Get array of error messages
     *
     * @param  null|string $field
     * @return array|bool
     */
    public function errors($field = null)
    {
//         if ($field !== null) {
//             return isset($this->_errors[$field]) ? $this->_errors[$field] : false;
//         }

        return $this->_errors;
    }

    /**
     * Add an error to error messages array
     *
     * @param string $field
     * @param string $msg
     * @param array  $params
     */
    public function error($field, $msg, array &$params = array())
    {
        $msg = $this->checkAndSetLabel($field, $msg, $params);
        $values = array();
        // Printed values need to be in string format
        foreach ($params as $param) {
            if (is_array($param)) {
                $param = "['" . implode("', '", $param) . "']";
            }
            if ($param instanceof \DateTime) {
                $param = $param->format('Y-m-d');
            } else {
                if (is_object($param)) {
                    $param = get_class($param);
                }
            }
            // Use custom label instead of field name if set
            if (is_string($params[0])) {
                if (isset($this->_labels[$param])) {
                    $param = $this->_labels[$param];
                }
            }
            $values[] = $param;
        }

        $this->_errors = vsprintf($msg, $values);
    }

    /**
     * Specify validation message to use for error for the last validation rule
     *
     * @param  string $msg
     * @return $this
     */
    public function message($rule, $msg = null)
    {
//        $this->_validations[count($this->_validations) - 1]['message'] = $msg;
        if (is_array($rule)) {
            static::$_ruleMessages = array_merge(static::$_ruleMessages, $rule);
        } else {
            static::$_ruleMessages[$rule] = &$msg;
        }

        return $this;
    }

    /**
     * Reset object properties
     */
    public function reset()
    {
        $this->_fields 		= array();
        $this->_errors 		= null;
        $this->_validations = array();
        $this->_labels 		= array();
        return $this;
    }

    protected function getPart(&$data, $identifiers)
    {
        // Catches the case where the field is an array of discrete values
        if (count($identifiers) === 0) {
            return array($data, false);
        }

        $identifier = array_shift($identifiers);

        // Glob match
        if ($identifier === '*') {
            $values = array();
            foreach ($data as &$row) {
                list($value, $multiple) = $this->getPart($row, $identifiers);
                if ($multiple) {
                    $values = array_merge($values, $value);
                } else {
                    $values[] = $value;
                }
            }

            return array($values, true);
        }

        // Dead end, abort
        elseif ($identifier === NULL || ! isset($data[$identifier])) {
            return array(null, false);
        }

        // Match array element
        elseif (count($identifiers) === 0) {
            return array($data[$identifier], false);
        }

        // We need to go deeper
        else {
            return $this->getPart($data[$identifier], $identifiers);
        }
    }

    /**
     * Run validations and return boolean result
     *
     * @return boolean
     */
    public function validate()
    {
        foreach ($this->_validations as &$v) {
            $field = &$v['fields'];

             list($values, $multiple) = $this->getPart($this->_fields, explode('.', $field));

            // Don't validate if the field is not required and the value is empty
//                 if ($this->hasRule('optional', $field) && isset($values)) {
//                     //Continue with execution below if statement
//                 } else
            if ($v['rule'] !== 'required' && !$this->hasRule('required', $field)
                    && (empty($values) || ($multiple && count($values) == 0))) {
                continue;
            }

            // Callback is user-specified or assumed method on class
            if (isset(static::$_rules[$v['rule']])) {
                $callback = static::$_rules[$v['rule']];
            } else {
                $callback = array($this, 'validate' . $v['rule']);
            }

            if (!$multiple) {
                $values = array($values);
            }

            foreach ($values as &$value) {
                $result = call_user_func($callback, $field, $value, $v['params']);

                if (! $result) {
                    $this->error(
                        $field,
                        // Ensure rule has an accompanying message
                        $message = '{field} ' .  (isset(static::$_ruleMessages[$v['rule']]) ? static::$_ruleMessages[$v['rule']] : self::ERROR_DEFAULT),
                        $v['params']
                    );
                    return false;
                }
            }
        }

        return true;//count($this->errors()) === 0;
    }

    /**
     * Determine whether a field is being validated by the given rule.
     *
     * @param  string  $name  The name of the rule
     * @param  string  $field The name of the field
     * @return boolean
     */
    protected function hasRule($name, $field)
    {
        foreach ($this->_validations as &$validation) {
            if ($validation['rule'] == $name) {
                if ($field == $validation['fields']) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Register new validation rule callback
     *
     * @param  string                    $name
     * @param  mixed                     $callback
     * @param  string                    $message
     * @throws \InvalidArgumentException
     */
    public static function addRule($name, $callback, $message = self::ERROR_DEFAULT)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Second argument must be a valid callback. Given argument was not callable.');
        }

        static::$_rules[$name] = $callback;
        static::$_ruleMessages[$name] = $message;
    }

    /**
     * Convenience method to add a single validation rule
     *
     * @param  string                    $rule
     * @param  array                     $fields
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function rule($fields, $rule)
    {
        if (!isset(static::$_rules[$rule])) {
            $ruleMethod = 'validate' . $rule;
            if (!method_exists($this, $ruleMethod)) {
                throw new \InvalidArgumentException("Rule '" . $rule . "' has not been registered with " . __CLASS__ . "::addRule().");
            }
        }

        // Get any other arguments passed to function
        $params = array_slice(func_get_args(), 2);

        $this->_validations[] = array(
            'rule' => &$rule,
            'fields' => &$fields,
            'params' => (array) $params
        );

        return $this;
    }

    /**
     * @param  string $value
     * @internal param array $labels
     * @return $this
     */
    public function label($value)
    {
        $lastRules = $this->_validations[count($this->_validations) - 1]['fields'];
        $this->labels(array($lastRules[0] => $value));

        return $this;
    }

    /**
     * @param  array  $labels
     * @return string
     */
    public function labels($labels = array())
    {
        $this->_labels = array_merge($this->_labels, $labels);

        return $this;
    }

    /**
     * @param  string $field
     * @param  string $msg
     * @param  array  $params
     * @return array
     */
    protected function checkAndSetLabel(&$field, &$msg, &$params)
    {
        if (isset($this->_labels[$field])) {
            $msg = str_replace('{field}', $this->_labels[$field], $msg);

            if (is_array($params)) {
                $i = 1;
                foreach ($params as $k => &$v) {
                    $tag = '{field'. $i .'}';
                    $label = isset($params[$k]) && (is_numeric($params[$k]) || is_string($params[$k])) && isset($this->_labels[$params[$k]]) ? $this->_labels[$params[$k]] : $tag;
                    $msg = str_replace($tag, $label, $msg);
                    $i++;
                }
            }
        } else {
            $msg = str_replace('{field}', ucwords(str_replace('_', ' ', $field)), $msg);
        }

        return $msg;
    }

    /**
     * Convenience method to add multiple validation rules with an array
     *
     * $this->rules(['name' =>'required|max:400|between:1,5']);
     *
     * $this->rules(
            [
              'name' => ['required', 'max' => 400, 'between' => [1, 5]]
           ]
     * );
     *
     * @param array $rules
     * @return static
     */
    public function rules(array $rules)
    {
        foreach ($rules as $field => &$rows) {
            if (is_string($rows)) {
                $rows = explode('|', $rows);
            }

            foreach ($rows as $k => &$item) {
                $params = [];

                if (is_string($k)) {
                    $params[] = $field;
                    $params[] = $k;

                    $params = array_merge($params, $item);
                } else {
                    if (is_string($item)) {
                        $item = explode(':', $item);
                    }
                    $params[] = $field;
                    $params[] = $item[0];

                    if (isset($item[1])) {
                        $item[1] = explode(',', $item[1]);

                        $params = array_merge($params, $item[1]);
                    }
                }

                call_user_func_array([$this, 'rule'], $params);
            }
        }

        return $this;

        //    	foreach ($rules as & $rule) {
        //    		call_user_func_array([$this, 'rule'], $rule);
        //    	}
        //    	return $this;

    }
}
