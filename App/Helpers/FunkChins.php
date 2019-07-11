<?php
use App\Helpers\View;

/**
 * Redirects the user to the specified $Location
 *
 * @param string $Location url or even partial url
 */
function Redirect ($Location = '')
{
    if ($Location)
    {
        header ('Location: ' . $Location);
        exit;
    }
}

/**
 * Shows the given message in a nice looking div
 *
 * @param string $message
 * @param string $type
 *
 * @return string
 *
 * @throws Exception
 */
function ShowMessage ($message = '', $type = 'alert-danger')
{
    if (!empty ($message))
    {
        $message = is_array($message) ? implode('<br />', $message) : $message;
        View::$ModuleViews = false;
        return View::GetHTML('partials.Message', compact('message', 'type'));
    }

    return '';
}

/**
 * Formats any given date into the given $Format
 *
 * @param string $Format
 * @param string $strDate
 *
 * @return string
 */
function FormatDate ($Format, $strDate = 'now')
{
    $Date = new DateTime($strDate);
    return $Date->format($Format);
}

/**
 * Replaces \r\n with <br>
 *
 * @param string $info
 *
 * @return string
 */
function Paragraphs ($info)
{
    return str_replace("\r\n", '<br>', $info);
}

/**
 * Removes all script tags from the string
 *
 * @param string $string
 *
 * @return string
 */
function NoScript ($string)
{
    $string = str_replace('<script', '', trim($string));
    if (strchr($string, '<script')) { NoScript ($string); }
    return $string;
}

/**
 * Removes all quotes and script tags from the string
 *
 * @param string $string
 *
 * @return string
 */
function NoScript_NoQuotes ($string)
{
    $string = str_replace("'", '', str_replace('"', '', trim($string)));
    $string = NoScript($string);
    return $string;
}

/**
 * Rounds the number to the hundreths and places a cash symbol in front of it
 *
 * @param float $float
 *
 * @return string
 */
function MoneyFormat ($float)
{
    return "$".number_format($float, 2);
}

/**
 * Removes the cash symbol and commas and casts it to a double
 *
 * @param string $cash
 *
 * @return double
 */
function CashToDouble ($cash)
{
    $cash = str_replace('$', '', str_replace(',', '', $cash));
    return (double)$cash;
}

/*set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }
echo $errno.'<br>';
echo $errstr.'<br>';
echo $errfile.'<br>';
echo $errline.'<br>';
echo '<pre>'.print_r($errcontext, true).'</pre>';
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});*/
