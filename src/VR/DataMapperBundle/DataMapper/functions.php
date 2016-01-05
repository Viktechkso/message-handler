<?php

/**
 * @author Jimmie Louis Borch
 */

function regex($input, $pattern, $matchNumber)
{
    preg_match($pattern, $input, $matches);

    return isset($matches[$matchNumber]) ? $matches[$matchNumber] : null;
}
