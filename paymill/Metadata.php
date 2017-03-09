<?php

/**
 * @copyright  Copyright (c) 2017 PAYMILL GmbH (http://www.paymill.com/)
 */
class Metadata
{
    /**
     * Paymill's Api url.
     */
    const PAYMILL_API = 'https://api.paymill.com/v2/';

    private $_version = "2.3.0";

    public function getVersion()
    {
        return $this->_version;
    }
}
