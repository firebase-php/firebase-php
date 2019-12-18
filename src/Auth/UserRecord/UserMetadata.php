<?php


namespace Firebase\Auth\UserRecord;


class UserMetadata
{
    private $creationTime;

    private $lastSignInTime;

    public function __construct(array $response = [])
    {
        $this->creationTime = UserRecordHelper::parseDate($response['createdAt']);
        $this->lastSignInTime = UserRecordHelper::parseDate($response['lastSignInTime']);
    }

    public function toArray() {
        return [
            'lastSignInTime' => $this->lastSignInTime,
            'creationTime' => $this->creationTime
        ];
    }
}
