<?php

namespace ASG\DMRAPI;

interface HttpResponseInterface
{
    /**
     * @return string
     */
    public function getContent();

    /**
     * @return bool
     */
    public function hasContent();

    /**
     * @return int
     */
    public function getCode();

    /**
     * @return array
     */
    public function getHeaders();
}