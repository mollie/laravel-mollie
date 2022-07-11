<?php

if (! function_exists('mollie')) {
    /**
     * @return \Mollie\Laravel\Wrappers\MollieApiWrapper
     */
    function mollie()
    {
        return app('mollie.api');
    }
}
