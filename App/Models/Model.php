<?php namespace App\Models;

use PDO;
use PDOException;
use App\Helpers\Validation;

/**
 * Class Model
 * @package App\Models
 */
class Model
{
    /**
     * @var int
     */
    public static $searchCount;

    /**
     * This refers to $Los_DB in /includes/env.php. It is the key of the db you wish to use. It can be overridden in the
     * child classes.
     *
     * @var int|string
     */
    public static $DB = 0;

    /**
     * This is the name of the auto increment column in the db table.
     *
     * @var string
     */
    protected static $ID = 'id';

    /**
     * These are the validation rules. This should be overridden in the child classes. Refer to /App/Services/Validation.
     *
     * @var array
     */
    public $rules = [];

    /**
     * These are the error messages for the validation rules. This should be overridden in the child classes.
     *
     * @var array
     */
    public $messages = [];

    /**
     * This will be used if there are any errors while validating the user inputs.
     *
     * @var array
     */
    public $errors = [];

    /**
     * Get all records in the table. This uses PDO.
     *
     * @param int $start
     * @param int $limit
     * @return array
     */
    public static function find_all ($start = 0, $limit = 0)
    {
        $class = get_called_class();
        $Vs = [':start' => $start, ':limit' => $limit];
        $q = 'SELECT * FROM '.$class::$TableName.(!$start&&!$limit?'':' LIMIT '.$start.', '.$limit);
        return self::find_by_sql($q, $Vs);
    }

    /**
     * Find the record by the auto increment id. This uses PDO.
     *
     * @param int $FindID
     * @return bool|object
     */
    public static function find_by_id ($FindID)
    {
        $class = get_called_class();
        $ID = $class::$ID;
        $r = self::find_by_sql('SELECT * FROM '.$class::$TableName.' WHERE '.$ID.' = :id', [':id' => $FindID]);
        return !empty ($r) ? array_shift ($r) : false;
    }

    /**
     * Find all records with the given query. This uses PDO.
     *
     * @param string $q PDO query
     * @param array $Vs PDO values
     * @param bool $Ordered Does the query request a specific order
     * @return array Returns an array of objects
     */
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
            if ($Ordered === true) { $a[] = $o; }
            elseif ($Ordered)  { $a[$o->$Ordered] = $o; }
            else
            {
                $ID = $class::$ID;
                $a[$o->$ID] = $o;
            }
        }//echo '<pre>'.print_r($a, true).'</pre>';
        return $a;
    }

    /**
     * Counts all records in the table.
     *
     * @return int
     */
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

    /**
     * Counts all records based on the given where clause. This uses PDO.
     *
     * @param $where WHERE clause without the WHERE keyword
     * @param array $Vs PDO values
     * @return int
     */
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

    /**
     * If the property doesn't exist or if the property value is empty, don't include it.
     *
     * @return array
     */
    protected function sanitized_attributes ()
    {
        $clean_attributes = [];
        foreach ($this->Fields as $v)
        {
            if (!property_exists($this, $v)) { continue; }
            $value = $this->$v;
            if (empty($value) && $value !== 0) { continue; }
            $clean_attributes[$v] = $value;
        }
        return $clean_attributes;
    }

    /**
     * Checks the auto increment value and runs create if empty and update otherwise.
     *
     * @return bool
     */
    public function save ()
    {
        $class = get_called_class();
		$ID = $class::$ID;
        return empty ($this->$ID) ? $this->create () : $this->update ();
    }

    /**
     * Inserts the record into the db table.
     *
     * @return bool
     */
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

    /**
     * Updates the record in the db table.
     *
     * @return bool
     */
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

    /**
     * Deletes the record from the db table.
     *
     * @return mixed
     */
    public function delete ()
    {
        $class = get_called_class();
        $db = self::GetThePDO($class);
        $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
		$ID = $class::$ID;
        $q = 'DELETE FROM '.$class::$TableName.' WHERE '.$ID.' = '.$this->$ID;
        return $db->exec($q);
    }

    /**
     * Validates the user inputs in the child class.
     *
     * @param int $ExceptKey
     * @return array
     */
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

    /**
     * Truncates the db table of the child class.
     */
    public static function TruncateTable ()
    {
        $class = get_called_class();
        $db = self::GetThePDO($class);
        $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
        $q = 'TRUNCATE TABLE '.$class::$TableName;
        $db->query($q);
    }

    /**
     * Gets the PDO of the desired db.
     *
     * @param $Class
     * @return object PDO
     */
    public static function GetThePDO ($Class)
    {
        return $GLOBALS['DB'][$Class::$DB];
    }

    // Encrypt/Decrypt functions

    /**
     * Encrypts the given text string with the given salt. The salt us usually found in /includes/env.php.
     *
     * @param $text
     * @param $salt
     * @return string
     */
    public static function EncryptThis ($text, $salt)
    {
        return USE_CRACKER ? trim (
                base64_encode (
                        mcrypt_encrypt (
                                MCRYPT_RIJNDAEL_256,	$salt,	$text,	MCRYPT_MODE_ECB,
                                mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256,	MCRYPT_MODE_ECB ),	MCRYPT_RAND )
                        )
                )
        ) : $text;
    }

    /**
     * Decrypts the given encrypted text string using the given salt. The salt us usually found in /includes/env.php.
     *
     * @param $text
     * @param $salt
     * @return string
     */
    public static function DecryptThis ($text, $salt)
    {
        return USE_CRACKER ? trim (
                mcrypt_decrypt (
                        MCRYPT_RIJNDAEL_256,	$salt,	base64_decode ( $text ),	MCRYPT_MODE_ECB,
                        mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256,	MCRYPT_MODE_ECB ),	MCRYPT_RAND )
                )
        ) : $text;

    }
}
?>
