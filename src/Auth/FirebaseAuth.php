<?php


namespace Firebase\Auth;


use Firebase\Auth\UserRecord\CreateRequest;
use Firebase\Auth\UserRecord\UpdateRequest;

class FirebaseAuth
{
    public function createCustomToken(string $uid, array $developClaims = null) {}

    public function verifyIdToken(string $idToken, bool $checkRevoked = false) {}

    public function getUser(string $uid): UserRecord {}

    public function getUserByEmail(string $email): UserRecord {}

    public function getUserByPhoneNumber(string $phoneNumber): UserRecord {}

    public function listUsers(int $maxResults = null, string $pageToken = null) {}

    public function createUser(CreateRequest $request): UserRecord {}

    public function deleteUser(string $uid): void {}

    public function updateUser(string $uid, UpdateRequest $request): UserRecord {}
}