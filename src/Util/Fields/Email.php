<?php


namespace Firebase\Util\Fields;

use Firebase\Util\Interfaces\Comparable;

class Email implements \JsonSerializable, Comparable
{
    /**
     * @param $target
     * @return bool
     */
    public function equals($target): bool
    {
        // TODO: Implement equals() method.
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
    }
}
