<?php


namespace Firebase\Auth;

interface FirebaseTokenVerifier
{
    /**
     * Verifies that the given token string is a valid Firebase JWT.
     *
     * @param string The token string to be verified.
     * @return FirebaseToken A decoded representation of the input token string.
     * @throws FirebaseAuthException If the input token string fails to verify due to any reason.
     */
    public function verifyToken(string $token);
}
