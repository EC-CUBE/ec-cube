<?php

function env($key, $default = null)
{
    $value = getenv($key);

    if ($value === false) {
        return $default;
    }

    switch (strtolower($value)) {
        case 'true':
            return true;
        case 'false':
            return false;
        case 'null':
            return null;
    }

    if ($value === '') {
        return $value;
    }

    $decoded = json_decode($value, true);
    if ($decoded !== null) {
        return $decoded;
    }

    return $value;
}
