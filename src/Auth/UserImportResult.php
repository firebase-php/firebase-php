<?php


namespace Firebase\Auth;

use Firebase\Auth\Internal\UploadAccountResponse;
use Firebase\Util\Validator\Validator;

final class UserImportResult
{
    /**
     * @var int
     */
    private $users;

    /**
     * @var ErrorInfo[]
     */
    private $errors;

    public function __construct(int $users, UploadAccountResponse $response)
    {
        /** @var ErrorInfo[] $errorBuilder */
        $errorBuilder = [];
        $errors = $response->getErrors();
        if (!is_null($errors)) {
            Validator::checkArgument($users > count($errors));

            foreach ($errors as $error) {
                $errorBuilder[] = new ErrorInfo($error->getIndex(), $error->getMessage());
            }
        }
        $this->users = $users;
        $this->errors = $errorBuilder;
    }

    public function getSuccessCount()
    {
        return $this->users - count($this->errors);
    }

    public function getFailureCount()
    {
        return count($this->errors);
    }

    /**
     * @return ErrorInfo[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
