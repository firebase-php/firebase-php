<?php


namespace Firebase\Auth;


use Firebase\Auth\Internal\UploadAccountResponse;
use Respect\Validation\Validator as v;

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

    /**
     * UserImportResult constructor.
     * @param int $users
     * @param UploadAccountResponse $response
     */
    public function __construct(int $users, UploadAccountResponse $response)
    {
        /** @var ErrorInfo[] $errorBuilder */
        $errorBuilder = [];
        $errors = $response->getErrors();
        if(!is_null($errors)) {
            v::yes()->assert($users >= count($errors));

            foreach($errors as $error) {
                $errorBuilder[] = new ErrorInfo($error->getIndex(), $error->getMessage());
            }
        }
        $this->users = $users;
        $this->errors = $errorBuilder;
    }

    public function getSuccessCount() {
        return $this->users - count($this->errors);
    }

    public function getFailureAccount() {
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
