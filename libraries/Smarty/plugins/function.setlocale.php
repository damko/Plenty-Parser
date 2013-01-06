<?php
function smarty_function_setlocale($params, &$smarty)
{
    // http://www.hackvalue.nl/en/article/65/multilingual%20websites%20and%20webapplications%20using%20php%20and%20smarty,%20part%203:%20locales
    //
    if (!array_key_exists('locale', $params))
    {
        $smarty->trigger_error("setlocale: Missing 'locale' parameter", E_USER_WARNING);
        return;
    }

    if (array_key_exists('type', $params))
        $type = strtolower($params['type']);
    else
        $type = 'all';

    switch ($type)
    {
        case 'collate':
            setlocale(LC_COLLATE,$params['locale']);
            break;

        case 'ctype':
            setlocale(LC_CTYPE, $params['locale']);
            break;

        case 'monetary':
            setlocale(LC_MONETARY,$params['locale']);
            break;

        case 'numeric':
            setlocale(LC_NUMERIC,$params['locale']);
            break;

        case 'time':
            setlocale(LC_TIME,$params['locale']);
            break;

        case 'all':
            setlocale(LC_ALL,$params['locale']);
            break;

        default:
            $smarty->trigger_error('setlocale: Invalid type: '.$params['type'], E_USER_WARNING);
    }
}
?>