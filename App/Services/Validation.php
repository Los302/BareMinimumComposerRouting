<?php namespace App\Services;

class Validation
{
    private $key;
    private $value;
    private $type = 'string';
    private $rules = [];

    private static $Messages = [];
    private static $ExceptID = 0;

    public $error = false;

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

    private function SetError ($rule, $default)
    {
        $error = $default;
        if (isset(self::$Messages[$this->key][$rule])) { $error = self::$Messages[$this->key][$rule]; }
        $this->error = $error;
    }

    private function string ($param)
    {
        $this->type = 'string';
        $this->value = (string)$this->value;
        return true;
    }

    private function int ($param)
    {
        $this->type = 'int';
        $this->value = (int)$this->value;
        return true;
    }

    private function float ($param)
    {
        $this->type = 'float';
        $this->value = (float)$this->value;
        return true;
    }

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

    private function email ($param)
    {
        if(!filter_var($this->value, FILTER_VALIDATE_EMAIL))
        {
            $this->SetError('email', 'The '.$this->SpacedKey.' field must be a valid email address');
            return false;
        }
        return true;
    }

    private function url ($param)
    {
        if(!filter_var($this->value, FILTER_VALIDATE_URL))
        {
            $this->SetError('url', 'The '.$this->SpacedKey.' field must be a valid url');
            return false;
        }
        return true;
    }

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

    private function unique ($param)
    {
        list($Class, $Function) = explode(',', $param);
        $Count = $Class::$Function($this->value, self::$ExceptID);
        if ($Count)
        {
            $this->SetError('unique', 'The '.$this->SpacedKey.' field value is already in use and must be unique');
            return false;
        }
        return true;
    }

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

    private function custom ($param)
    {
        list($Class, $Function) = explode(',', $param);
        $Valid = $Class::$Function($this->value, self::$ExceptID);
        if (!$Valid)
        {
            $this->SetError($Function, 'The '.$this->SpacedKey.' field value is invalid');
            return false;
        }
        return true;
    }
}
?>