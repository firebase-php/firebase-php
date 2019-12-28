<?php


namespace Firebase\Auth;

use Carbon\CarbonInterval;
use Firebase\Util\Validator\Validator;

class SessionCookieOptions
{
    /**
     * @var int
     */
    private $expiresIn;

    public function __construct(SessionCookieOptionsBuilder $builder)
    {
        Validator::checkArgument($builder->getExpiresIn() > CarbonInterval::minutes(5)->totalMilliseconds, 'expiresIn duration must be at least 5 minutes');
        Validator::checkArgument($builder->getExpiresIn() < CarbonInterval::days(14)->totalMilliseconds, 'expiresIn duration must be at most 14 days');
        $this->expiresIn = $builder->getExpiresIn();
    }

    public function getExpiresInSeconds()
    {
        return CarbonInterval::millisecond($this->expiresIn)->totalSeconds;
    }
}
