<?php


namespace Firebase\Auth\Internal;


use MyCLabs\Enum\Enum;

/**
 * Class EmailLinkType
 * @package Firebase\Auth\Internal
 *
 * @method static EmailLinkType EMAIL_SIGNIN()
 * @method static EmailLinkType VERIFY_EMAIL()
 * @method static EmailLinkType PASSWORD_RESET()
 */
class EmailLinkType extends Enum
{
    private const VERIFY_EMAIL = 'VERIFY_EMAIL';
    private const EMAIL_SIGNIN = 'EMAIL_SIGNIN';
    private const PASSWORD_RESET = 'PASSWORD_RESET';
}
