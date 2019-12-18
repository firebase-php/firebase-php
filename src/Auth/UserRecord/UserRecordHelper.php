<?php


namespace Firebase\Auth\UserRecord;


use Carbon\Carbon;

define('B64_REDACTED', base64_encode('REDACTED'));

class UserRecordHelper
{
    /**
     * Parses a time stamp string or number and returns the corresponding date if valid.
     *
     * @param null $time The unix timestamp string or number in milliseconds.
     * @return string|null The corresponding date as a UTC string, if valid. Otherwise, null.
     */
    public static function parseDate($time = null): ?string {
        $date = Carbon::createFromTimestampMs(intval($time));
        return $date->toRfc7231String();
    }
}
