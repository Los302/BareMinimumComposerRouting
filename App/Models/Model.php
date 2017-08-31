<?php namespace App\Models;

use PDO;
use PDOException;
use App\Services\Validation;

class Model
{
    public static $searchCount;

    public static $DB = 0;
    
    protected static $ID = 'id';

    public $errors = [];

    public static function find_all ($start = 0, $limit = 0)
    {
        $class = get_called_class();
        $Vs = [':start' => $start, ':limit' => $limit];
        $q = 'SELECT * FROM '.$class::$TableName.(!$start&&!$limit?'':' LIMIT '.$start.', '.$limit);
        return self::find_by_sql($q, $Vs);
    }

    public static function find_by_id ($FindID)
    {
        $class = get_called_class();
        $ID = $class::$ID;
        $r = self::find_by_sql('SELECT * FROM '.$class::$TableName.' WHERE '.$ID.' = :id', [':id' => $FindID]);
        return !empty ($r) ? array_shift ($r) : false;
    }

    public static function find_by_sql ($q, $Vs = [], $Ordered = false)
    {
        $class = get_called_class();
        $db = self::GetThePDO($class);
        $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
        $stmt = $db->prepare($q);
        if (isset($Vs[':start']))
        {
            if ($Vs[':start'] || $Vs[':limit'])
            {
                $stmt->bindValue(':start', (int) $Vs[':start'], PDO::PARAM_INT);
                unset($Vs[':start']);
            }
            if ($Vs[':limit'])
            {
                $stmt->bindValue(':limit', (int) $Vs[':limit'], PDO::PARAM_INT);
                unset($Vs[':limit']);
            }
        }
        try {
            $stmt->execute($Vs);
        } catch (PDOException $e) {
            die($e.'<br />'.$q.'<pre>'.print_r($Vs, true).'</pre>');
        }
        $stmt->setFetchMode(PDO::FETCH_CLASS, $class);
        $a = [];
        while ($o = $stmt->fetch(PDO::FETCH_CLASS))
        {
            if ($Ordered) { $a[] = $o; }
            else
            {
                $ID = $class::$ID;
                $a[$o->$ID] = $o;
            }
        }//echo '<pre>'.print_r($a, true).'</pre>';
        return $a;
    }

    public static function count_all ()
    {
        $class = get_called_class();
        $db = self::GetThePDO($class);
        $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
        $q = 'SELECT COUNT(*) FROM '.$class::$TableName;
        $r = $db->query ($q);
        $a = $db->fetch_array($r);
        $class::$searchCount = array_shift ($a);
        return $class::$searchCount;
    }

    public static function count ($where, $Vs = [])
    {
        $class = get_called_class();
        $db = self::GetThePDO($class);
        $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
        $q = 'SELECT COUNT(*) AS TheCount FROM '.$class::$TableName.' WHERE '.$where;
        $stmt = $db->prepare ($q);
        $stmt->execute ($Vs);
        $a = $stmt->fetch();
        return array_shift ($a);
    }

//    public static function search ($term, $start = 0, $limit = 0)
//    {
//        global $db;
//        $class = get_called_class();
//        $term = $db[$class::$DB]->escape_value($term);
//        $newObject = new $class;
//        $where = '';
//        foreach ($newObject->Fields as $k => $v) { $where .= ($k ? ' OR ' : '').$v.' LIKE "%'.$term.'%"'; }
//        $count = self::count($where);
//        if (!$count) { return []; }
//        $q = 'SELECT * FROM '.$class::$TableName.' WHERE '.$where.' LIMIT '.$start.', '.$limit;//die($q);
//        $r = self::find_by_sql ($q);
//        return $r;
//    }

    protected function sanitized_attributes ()
    {
        $clean_attributes = array ();
        foreach ($this->Fields as $v)
        {
            if (!property_exists($this, $v)) { continue; }
            $value = $this->$v;
            if (empty($value) && $value !== 0) { continue; }
            $clean_attributes[$v] = $value;
        }
        return $clean_attributes;
    }

    public function save ()
    {
        $class = get_called_class();
		$ID = $class::$ID;
        return empty ($this->$ID) ? $this->create () : $this->update ();
    }

    public function create ()
    {
        $class = get_called_class();
        $db = self::GetThePDO($class);
        $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
        $attributes = $this->sanitized_attributes ();
        $attribute_pairs = [];
        $Vs = [];
        foreach ($attributes as $k => $v)
        {
            $attribute_pairs[] = $k . ' = ?';
            $Vs[] = $v;
        }
        $q = 'INSERT INTO '.$class::$TableName.' SET
							'.join (', ', $attribute_pairs);
        $stmt = $db->prepare($q);
        try {
            $AffectedRows = $stmt->execute($Vs);
            $ID = $class::$ID;
            $this->$ID = $db->lastInsertId();
        } catch (PDOException $e) {
            die($e.'<br />'.$q.'<pre>'.print_r($Vs, true).'</pre>');
        }
        return $AffectedRows == 1 ? true : false;
    }

    public function update ()
    {
        $class = get_called_class();
        $db = self::GetThePDO($class);
        $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
        $attributes = $this->sanitized_attributes ();
        $attribute_pairs = [];
        $Vs = [];
        foreach ($attributes as $k => $v)
        {
            $attribute_pairs[] = $k . ' = ?';
            $Vs[] = $v;
        }
		$ID = $class::$ID;
        $q = 'UPDATE '.$class::$TableName.' SET
							'.join (', ', $attribute_pairs).'
							WHERE '.$ID.' = '.$this->$ID;
        $stmt = $db->prepare($q);
        try {
            $AffectedRows = $stmt->execute($Vs);
        } catch (PDOException $e) {
            die($e.'<br />'.$q.'<pre>'.print_r($Vs, true).'</pre>');
        }
        return $AffectedRows == 1 ? true : false;
    }

    public function delete ()
    {
        $class = get_called_class();
        $db = self::GetThePDO($class);
        $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
		$ID = $class::$ID;
        $q = 'DELETE FROM '.$class::$TableName.' WHERE '.$ID.' = '.$this->$ID;
        return $db->exec($q);
    }

    public function ToScreen ($var)
    {
        $var = htmlspecialchars($this->$var);
        $var = stripslashes($var);
        return $var;
    }

    public function validate ($ExceptKey = 0)
    {
        $class = get_called_class();
        if (!$ExceptKey)
        {
            $ID = $class::$ID;
            $ExceptKey = $this->$ID;
        }
        $input = [];
        foreach ($this->rules as $k => $v) { $input[$k] = $this->$k; }
        $Messages = isset ($this->messages) ? $this->messages : [];
        return array_merge($this->errors, Validation::getErrors ($input, $this->rules, $Messages, $ExceptKey));
    }

    public static function TruncateTable ()
    {
        $class = get_called_class();
        $db = self::GetThePDO($class);
        $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
        $q = 'TRUNCATE TABLE '.$class::$TableName;
        $db->query($q);
    }

    public static function GetThePDO ($Class)
    {
        return $GLOBALS['DB'][$Class::$DB];
    }

    // Encrypt/Decrypt functions
    public static function EncryptThis ($text, $salt)
    {
        return $text;
        return trim (
                base64_encode (
                    mcrypt_encrypt (
                        MCRYPT_RIJNDAEL_256,	$salt,	$text,	MCRYPT_MODE_ECB,
                        mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256,	MCRYPT_MODE_ECB ),	MCRYPT_RAND )
                    )
                )
        );
    }

    public static function DecryptThis ($text, $salt)
    {
        return $text;
        return trim (
            mcrypt_decrypt (
                MCRYPT_RIJNDAEL_256,	$salt,	base64_decode ( $text ),	MCRYPT_MODE_ECB,
                mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256,	MCRYPT_MODE_ECB ),	MCRYPT_RAND )
            )
        );

    }
}
?>
