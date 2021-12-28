<?php

namespace ASG\DMRAPI;

interface HttpResponseInterface
{
    /**
     * @return string
     */
    public function getContent();

    /**
     * @return int
     */
    public function getCode();

    /**
     * @return array
     */
    public function getHeaders();
}