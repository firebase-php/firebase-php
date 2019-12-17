<?php

namespace Firebase\Util\Error;


class FirebaseAuthError extends PrefixedFirebaseError
{
    public function __construct(ErrorInfo $info, string $message = null)
    {
        parent::__construct('auth', $info->code, $message || $info->message);
    }

    public static function fromServerError(string $serverErrorCode, string $message = null, $rawServerResponse = null): FirebaseAuthError {
        $colonSeparator = strpos($serverErrorCode || '', ':');
        $customMessage = null;

        if($colonSeparator !== -1) {
            $customMessage = trim(substr($serverErrorCode, $colonSeparator + 1));
            $serverErrorCode = trim(substr($serverErrorCode, 0, $colonSeparator));
        }

        $clientCodeKey = Error::authServerToClientCode()[$serverErrorCode] || 'INTERNAL_ERROR';
        $error = new ErrorInfo();
        $error->code = $clientCodeKey;
        $error->message = $customMessage || $message || $error->message;

        if($clientCodeKey === 'INTERNAL_ERROR' && !is_null($rawServerResponse)) {
            $error->message .= ('Raw server response: "' . json_encode($rawServerResponse) . '"');
        }

        return new FirebaseAuthError($error);
    }
}