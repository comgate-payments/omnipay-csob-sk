<?php

namespace Omnipay\CsobSk\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\CsobSk\Sign\HmacSign;

class CompletePurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $sharedSecret = $this->getParameter('sharedSecret');

        $data = "{$_GET['VS']}{$_GET['RES']}";
        $sign = new HmacSign();

        if ($sign->sign($data, $sharedSecret) != $_GET['SIGN']) {
            throw new InvalidRequestException('incorect signature');
        }

        return [
            'RES' => $_GET['RES'],
            'VS' => $_GET['VS'],
        ];
    }

    public function sendData($data)
    {
        return $this->response = new CompletePurchaseResponse($this, $data);
    }

    public function getSharedSecret()
    {
        return $this->getParameter('sharedSecret');
    }

    public function setSharedSecret($value)
    {
        return $this->setParameter('sharedSecret', $value);
    }
}
