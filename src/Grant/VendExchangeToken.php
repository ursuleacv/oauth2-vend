<?php

namespace League\OAuth2\Client\Grant;

class VendExchangeToken extends AbstractGrant
{
    public function __toString()
    {
        return 'vend_exchange_token';
    }

    protected function getRequiredRequestParameters()
    {
        return [
            'vend_exchange_token',
        ];
    }

    protected function getName()
    {
        return 'vend_exchange_token';
    }
}
