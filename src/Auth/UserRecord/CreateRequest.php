<?php


namespace Firebase\Auth\UserRecord;

use Firebase\Util\Validator\Validator;

final class CreateRequest
{
    use UserRecordRequest;

    public function setUid(string $uid)
    {
        Validator::isUid($uid);
        $this->properties['localId'] = $uid;
        return $this;
    }
}
