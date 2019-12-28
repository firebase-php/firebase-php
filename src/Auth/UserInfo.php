<?php


namespace Firebase\Auth;

interface UserInfo
{
    /**
     * Returns the user's unique ID assigned by the identity provider.
     *
     * @return string|null a user ID string.
     */
    public function getUid(): ?string;

    /**
     * Returns the user's display name, if available.
     *
     * @return string|null a display name string or null.
     */
    public function getDisplayName(): ?string;

    /**
     * Returns the user's email address, if available.
     *
     * @return string|null an email address string or null.
     */
    public function getEmail(): ?string;

    /**
     * Returns the user's phone number, if available.
     *
     * @return string|null a phone number string or null.
     */
    public function getPhoneNumber(): ?string;

    /**
     * Returns the user's photo URL, if available.
     *
     * @return string|null a URL string or null.
     */
    public function getPhotoUrl(): ?string;

    /**
     * Returns the ID of the identity provider. This can be a short domain name (e.g. google.com) or
     * the identifier of an OpenID identity provider.
     *
     * @return string|null an ID string that uniquely identifies the identity provider.
     */
    public function getProviderId(): ?string;
}
