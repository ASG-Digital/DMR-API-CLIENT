<?php

namespace ASG\DMRAPI;

interface TokenStorageInterface
{
    /**
     * @param KeyPair $keyPair
     * @return void
     */
    public function write(KeyPair $keyPair);

    /**
     * @return KeyPair|null
     */
    public function read();
}