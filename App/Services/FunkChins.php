<?php
function redirect_to ($location = NULL)
{
    if ($location != NULL)
    {
        header ('Location: ' . $location);
        exit;
    }
}

function output_message ($message = '', $type = 'alert-danger')
{
    if (!empty ($message))
    {
        $message = is_array($message) ? implode('<br />', $message) : $message;
        return '<div class="alert '.$type.'">'.$message.'</div>';
    }
}

function Logit ($action, $message = '')
{
    $logfile = 'log';
    $new = file_exists ($logfile) ? false : true;
    if ($handle = fopen ($logfile, 'a'))
    {
        $timestamp = strftime ("%Y-%m-%d %H:%M:%S", time ());
        $content = $timestamp.' | '.$action.': '.$message."\r\n";
        fwrite ($handle, $content);
        fclose ($handle);
        if ($new) { chmod ($logfile, 0755); }
    }
    else
    {
        //"Could not open log file for writing.";
    }
}

function datetime2text ($datetime)
{
    $unixdatetime = strtotime ($datetime);
    return strftime ("%B %d, %Y at %I:%M %p", $unixdatetime);
}

function text2mysqldate ($text)
{
    $date = explode ('/', $text);
    $mysqldate = $date[2].'-'.$date[0].'-'.$date[1];
    return $mysqldate;
}

function mysqldate2text ($date)
{
    $time = strtotime($date);
    return date ('n/j/Y', $time);
}

function mysqldatetime2text ($date)
{
    $time = strtotime($date);
    return date ('n/j/Y h:i a', $time);
}

function paragraphs ($info)
{
    return str_replace("\r\n", '<br />', $info);
}

function NoScript ($string)
{
    $string = str_replace('<script', '', trim($string));
    if (strchr($string, '<script')) { NoScript ($string); }
    return $string;
}

function NoScript_NoQuotes ($string)
{
    $string = str_replace("'", '', str_replace('"', '', trim($string)));
    $string = NoScript($string);
    return $string;
}

function MoneyFormat ($float)
{
    return "$".number_format($float, 2);
}

function CashToDouble ($cash)
{
    $cash = str_replace('$', '', str_replace(',', '', $cash));
    return (double)$cash;
}

function Page($page)
{
    return strchr($_SERVER["SCRIPT_NAME"], '/'.$page.'.php');
}

function CurrentLink($link)
{
    if (Page($link))
    {
        return ' class="first"';
    }
    elseif ($link == 'Home' && $_SERVER["SCRIPT_NAME"] == '/index.php')
    {
        return ' class="first"';
    }
}

//set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
//    // error was suppressed with the @-operator
//    if (0 === error_reporting()) {
//        return false;
//    }
//echo $errno.'<br>';
//echo $errstr.'<br>';
//echo $errfile.'<br>';
//echo $errline.'<br>';
//echo '<pre>'.print_r($errcontext, true).'</pre>';
//    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
//});

?>
