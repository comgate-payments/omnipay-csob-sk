<?php

namespace Omnipay\CsobSk;

use Omnipay\Common\AbstractGateway;

class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'CSOB SK Gateway';
    }

    public function getDefaultParameters()
    {
        return [
            'idVOD' => '',
            'urlObchodnika' => '',
            'protiucet' => '',
            'kodBankaProti' => '7500',
        ];
    }

    public function getResourcesDir(){
        return __DIR__ . '/../../resources/';
    }

    public function getSharedSecret()
    {
        return $this->getParameter('sharedSecret');
    }

    public function setSharedSecret($value)
    {
        return $this->setParameter('sharedSecret', $value);
    }

    public function getIdVOD()
    {
        return $this->getParameter('idVOD');
    }

    public function setIdVOD($value)
    {
        return $this->setParameter('IdVOD', $value);
    }

    public function getUrlObchodnika()
    {
        return $this->getParameter('urlObchodnika');
    }

    public function setUrlObchodnika($value)
    {
        return $this->setParameter('urlObchodnika', $value);
    }

    public function getProtiucet()
    {
        return $this->getParameter('protiucet');
    }

    public function setProtiucet($value)
    {
        return $this->setParameter('protiucet', $value);
    }

    public function getKodBankaProti()
    {
        return $this->getParameter('kodBankaProti');
    }

    public function setKodBankaProti($value)
    {
        return $this->setParameter('kodBankaProti', $value);
    }

    public function getSignCert()
    {
        return $this->getParameter('signCert');
    }

    public function setSignCert($value)
    {
        return $this->setParameter('signCert', $value);
    }

    public function getPrivKey()
    {
        return $this->getParameter('privKey');
    }

    public function setPrivKey($value)
    {
        return $this->setParameter('privKey', $value);
    }

    public function getCsobPublicKey()
    {
        return $this->getParameter('csobPublicKey');
    }

    public function setCsobPublicKey($value)
    {
        return $this->setParameter('csobPublicKey', $value);
    }

    public function getTrustedCAs()
    {
        return $this->getParameter('trustedCAs');
    }

    public function setTrustedCAs($value)
    {
        return $this->setParameter('trustedCAs', $value);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest(\Omnipay\CsobSk\Message\PurchaseRequest::class, $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest(\Omnipay\CsobSk\Message\CompletePurchaseRequest::class, $parameters);
    }
}
