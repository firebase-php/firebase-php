<?php

namespace Firebase;

interface FirebaseServiceInterface
{
    public function getApp(): FirebaseApp;

    public function getInternal(): FirebaseServiceInternalsInterface;
}
