<?php

if (! function_exists('mollie')) {
    function mollie()
    {
        return app('mollie.api');
    }
}
