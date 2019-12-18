<?php


namespace Firebase\Auth\UserRecord;


class CreateRequest extends UpdateRequest
{
    private $uid;

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param mixed $uid
     * @return CreateRequest
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }
}
