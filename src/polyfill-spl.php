<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

if (!function_exists('spl_object_id')) {
    /**
     * Get the ID for the given object.
     *
     * @param object $subject
     *
     * @return int
     */
    // @codingStandardsIgnoreLine
    function spl_object_id($subject): int
    {
        return (int)spl_object_hash($subject);
    }
}
