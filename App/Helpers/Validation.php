<?php namespace App\Helpers;

/**
 * Class Validation
 * @package App\Services
 */
class Validation
{
    /**
     * This is the field we are validating for
     *
     * @var string
     */
    private $key;

    /**
     * This is the $key with the words separated and capitalized
     *
     * @var string
     */
    private $SpacedKey;

    /**
     * This is the value give by the user which needs to be validated
     *
     * @var string
     */
    private $value;

    /**
     * This is the type of value we are expecting
     *
     * @var string
     */
    private $type = 'string';

    /**
     * These are the rules for the field which the value must follow
     *
     * @var array
     */
    private $rules = [];

    /**
     * These are the custom error messages for the rules given by the developer
     *
     * @var array
     */
    private static $Messages = [];

    /**
     * This is for duplicate checking
     *
     * @var int
     */
    private static $ExceptID = 0;

    /**
     * This is the error if this field is invalid
     *
     * @var bool|string
     */
    public $error = false;

    /**
     * Validation constructor.
     *
     * @param string $key This is the field we are validating for
     * @param string $value This is the value given that we need to validate
     * @param string $rules This is a list of rules separated by a pipe
     */
    public function __construct ($key, $value, $rules)
    {
        $this->key = $key;
        $this->SpacedKey = str_replace('_', ' ', $key);
        $this->value = $value;
        $this->rules = explode ('|', $rules);

        foreach ($this->rules as $k => $v)
        {
            if (!$k && $v != 'required' && empty ($this->value)) { break; }
            list($rule, $param) = array_pad(explode(':', $v), 2, false);
            if (!$this->$rule ($param)) { break; }
        }
    }

    /**
     * @param array $input Values to be validated
     * @param array $rules Rules that values must follow
     * @param array $messages Custom error messages
     * @param int $ExceptID This is used for duplicate checking
     *
     * @return array Errors
     */
    public static function getErrors ($input, $rules, $messages = [], $ExceptID = 0)
    {
        self::$Messages = $messages;
        self::$ExceptID = $ExceptID;
        $errors = [];
        foreach ($rules as $k => $v)
        {
            $validation = new self ($k, $input[$k], $v);
            if ($validation->error) { $errors[$k] = $validation->error; }
        }
        return $errors;
    }

    /**
     * @param string $rule The rule being violated
     * @param string $default The default error message if no custom error message
     */
    private function SetError ($rule, $default)
    {
        $error = $default;
        if (isset(self::$Messages[$this->key][$rule])) { $error = self::$Messages[$this->key][$rule]; }
        $this->error = $error;
    }

    /**
     * Cast the value to a string
     *
     * @param string $param Rule parameters
     *
     * @return bool
     */
    private function string ($param)
    {
        $this->type = 'string';
        $this->value = (string)$this->value;
        return true;
    }

    /**
     * Cast the value to an integer
     *
     * @param string $param Rule parameters
     *
     * @return bool
     */
    private function int ($param)
    {
        $this->type = 'int';
        $this->value = (int)$this->value;
        return true;
    }

    /**
     * Cast the value to a float
     *
     * @param string $param Rule parameters
     *
     * @return bool
     */
    private function float ($param)
    {
        $this->type = 'float';
        $this->value = (float)$this->value;
        return true;
    }

    /**
     * Make sure this field has a value
     *
     * @param string $param Rule parameters
     *
     * @return bool
     */
    private function required ($param)
    {
        $v = trim($this->value);
        if ($v === '')
        {
            $this->SetError('required', 'The '.$this->SpacedKey.' field is required');
            return false;
        }
        return true;
    }

    /**
     * Make sure this field has the syntax of an email address
     *
     * @param string $param Rule parameters
     *
     * @return bool
     */
    private function email ($param)
    {
        if(!filter_var($this->value, FILTER_VALIDATE_EMAIL))
        {
            $this->SetError('email', 'The '.$this->SpacedKey.' field must be a valid email address');
            return false;
        }
        return true;
    }

    /**
     * Make sure this field has the syntax of a url
     *
     * @param string $param Rule parameters
     *
     * @return bool
     */
    private function url ($param)
    {
        if(!filter_var($this->value, FILTER_VALIDATE_URL))
        {
            $this->SetError('url', 'The '.$this->SpacedKey.' field must be a valid url');
            return false;
        }
        return true;
    }

    /**
     * Minimum number value or date or string length
     *
     * @param string $param Rule parameters
     *
     * @return bool
     */
    private function min ($param)
    {
        $v = $this->value;
        if (in_array($this->type, ['int', 'float']))
        {
            if ($v < $param)
            {
                $this->SetError('min', 'The '.$this->SpacedKey.' field must be at least '.$param);
                return false;
            }
        }
        elseif ($this->type == 'date')
        {
            $v = strtotime($v);
            $MinTime = strtotime($param);
            if ($v < $MinTime)
            {
                $this->SetError('min', 'The '.$this->SpacedKey.' field must be after '.$param);
                return false;
            }
        }
        else
        {
            if (strlen($v) < $param)
            {
                $this->SetError('min', 'The '.$this->SpacedKey.' field must be at least '.$param.' charcters in length');
                return false;
            }
        }
        return true;
    }

    /**
     * Maximum number value or date or string length
     *
     * @param string $param Rule parameters
     *
     * @return bool
     */
    private function max ($param)
    {
        $v = $this->value;
        if (is_numeric($v) && $v < $param)
        {
            $this->SetError('max', 'The '.$this->SpacedKey.' field must be at least '.$param);
            return false;
        }
        elseif ($this->type == 'date')
        {
            $v = strtotime($v);
            $MaxTime = strtotime($param);
            if ($v > $MaxTime)
            {
                $this->SetError('max', 'The '.$this->SpacedKey.' field must be before '.$param);
                return false;
            }
        }
        elseif (strlen($v) > $param)
        {
            $this->SetError('max', 'The '.$this->SpacedKey.' field must be '.$param.' charcters or less in length');
            return false;
        }
        return true;
    }

    /**
     * Make sure the value is listed in the parameters
     *
     * @param string $param Rule parameters
     *
     * @return bool
     */
    private function in ($param)
    {
        $list = ','.trim($param, ',').',';
        if (strpos($list, $this->value) < 0)
        {
            $this->SetError('in', 'The '.$this->SpacedKey.' field must be one of the following: '.str_replace(',', ' / ', trim($param, ',')));
            return false;
        }
        return true;
    }

    /**
     * Make sure the value is a datetime
     *
     * @param string $param Rule parameters
     *
     * @return bool
     */
    private function datetime ($param)
    {
        $time = strtotime($this->value);
        if (FALSE === $time)
        {
            $this->SetError('datetime', 'The '.$this->SpacedKey.' field must be a valid date time');
            return false;
        }
        return true;
    }

    /**
     * Make sure the value is a date
     *
     * @param string $param Rule parameters
     *
     * @return bool
     */
    private function date ($param)
    {
        $this->type = 'date';
        $time = strtotime($this->value);
        if (FALSE === $time)
        {
            $this->SetError('date', 'The '.$this->SpacedKey.' field must be a valid date');
            return false;
        }
        return true;
    }

    /**
     * Make sure the value is unique. The parameter must be in the form of class,method
     *
     * @param string $param Rule parameters
     *
     * @return bool
     */
    private function unique ($param)
    {
        list($Class, $Method) = explode(',', $param);
        $Count = $Class::$Method($this->value, self::$ExceptID);
        if ($Count)
        {
            $this->SetError('unique', 'The '.$this->SpacedKey.' field value is already in use and must be unique');
            return false;
        }
        return true;
    }

    /**
     * Match to fields so that user didn't mistype
     *
     * @param string $param Rule parameters
     *
     * @return bool
     */
    private function confirmed ($param)
    {
        $key = $this->key.'_confirmed';
        if (!isset($_POST[$key]))
        {
            $this->SetError('confirmed', 'The '.$this->SpacedKey.' field does not match its confirm counterpart');
            return false;
        }
        if ($this->value !== $_POST[$key])
        {
            $this->SetError('confirmed', 'The '.$this->SpacedKey.' field does not match its confirm counterpart');
            return false;
        }
        return true;
    }

    /**
     * Make this field required if another field meet certain criteria
     *
     * @param string $param Rule parameters
     *
     * @return bool
     */
    private function requiredIf ($param)
    {
        list($OtherFieldName, $rule) = explode(',', $param);
        switch ($rule)
        {
            case 'notEmpty':
                if (empty($this->value) && (isset($_POST[$OtherFieldName]) && !empty($_POST[$OtherFieldName])))
                {
                    $this->SetError('requiredIf', 'The '.$this->SpacedKey.' field is required');
                    return false;
                }
            break;
            default:
                if (empty($this->value) && (isset($_POST[$OtherFieldName]) && $_POST[$OtherFieldName] == $rule))
                {
                    $this->SetError('requiredIf', 'The '.$this->SpacedKey.' field is required');
                    return false;
                }
            break;
        }
        return true;
    }

    /**
     * Custom validator function. The parameter must be in the form of class,method
     *
     * @param string $param Rule parameters
     *
     * @return bool
     */
    private function custom ($param)
    {
        list($Class, $Method) = explode(',', $param);
        $Valid = $Class::$Method($this->value, self::$ExceptID);
        if (!$Valid)
        {
            $this->SetError($Method, 'The '.$this->SpacedKey.' field value is invalid');
            return false;
        }
        return true;
    }
}
?>