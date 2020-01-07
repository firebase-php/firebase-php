<?php


namespace Firebase\Auth\ListUsersPage;

interface Page
{
    /**
     * @return bool
     */
    public function hasNextPage();

    /**
     * @return string|null
     */
    public function getNextPageToken();

    /**
     * @return Page
     */
    public function getNextPage();

    /**
     * @return \Iterator
     */
    public function getValues();
}
