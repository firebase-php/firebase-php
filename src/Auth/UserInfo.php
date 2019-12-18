<?php


namespace Firebase\Auth;


interface UserInfo
{
    public function getUid(): ?string;

    public function getDisplayName(): ?string;

    public function getEmail(): ?string;

    public function getPhoneNumber(): ?string;

    public function getPhotoUrl(): ?string;

    public function getProviderId(): ?string;
}
