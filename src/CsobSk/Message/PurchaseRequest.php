<?php

namespace Omnipay\CsobSk\Message;

use Omnipay\Common\Currency;
use Omnipay\CsobSk\File\TmpFile;
use Omnipay\CsobSk\Sign\HmacSign;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\CsobSk\Sign\OpenSSLSign;

class PurchaseRequest extends AbstractRequest
{
    public function initialize(array $parameters = array())
    {
        parent::initialize($parameters);
        return $this;
    }

    /**
	 * Vygeneruje XML obsahujuce udaje pre platbu (pre pole formulara ZPRAVA)
	 * @param TPlatobneTlacidlo_Platba $platba
	 * @return string
	 * @throws InvalidArgumentException
	 */
	private function vytvorXML($data)
	{
//		$values = array(
//			'VS' => $this->getVs(),
//			'SS' => $this->getSs(),
//			'AMT' => $this->getAmt(),
//		);
		//$urlObchodnika = $this->substituteValues($this->urlObchodnika, $values);
		$xml  = "<zprava ofce='3111'><obchodnik>";
		$xml .= "<id>" . $data['idVOD'] . "</id>";
		$xml .= "<urlObchodnika>" . $data['rurl'] . "</urlObchodnika>";
		$xml .= "</obchodnik>";
		$xml .= "<data>";
		$xml .= "<nProtiucet>" . $data['protiucet'] . "</nProtiucet>";
		$xml .= "<chKodBankaProti>" . $data['kodBankaProti'] . "</chKodBankaProti>";
		$xml .= "<nCastka>" . $data['amt'] . "</nCastka>";
		$xml .= "<nKS>" . $data['cs'] . "</nKS>";
		$xml .= "<chVS>" . $data['vs'] . "</chVS>";
		$xml .= "<nSS>" . $data['ss'] . "</nSS>";
		$xml .= "<vchPoleAV1>" . "#" . $data['idVOD'] . " " . $data['av1'] . "</vchPoleAV1>";
		$xml .= "<vchPoleAV2>" . $data['av2'] . "</vchPoleAV2>";
		$xml .= "<vchPoleAV3>" . $data['av3'] . "</vchPoleAV3>";
		$xml .= "<vchPoleAV4>" . $data['av4'] . "</vchPoleAV4>";
		$xml .= "</data>";
		$xml .= "</zprava>";

		return $xml;
	}

    public function getData(){
        $this->validate('vs', 'rurl');
        $data = [];

        $data['amt'] = $this->getAmount();
        $data['vs'] = $this->getVs();
        $data['cs'] = $this->getCs();
        $data['ss'] = $this->getSs();
        $data['rurl'] = $this->getRurl();

        $data['idVOD'] = $this->getIdVOD();
        $data['protiucet'] = $this->getProtiucet();
        $data['kodBankaProti'] = $this->getKodBankaProti();
        $data['av1'] = $this->getAV1();
        $data['av2'] = $this->getAV2();
        $data['av3'] = $this->getAV3();
        $data['av4'] = $this->getAV4();

//        'amount' => number_format($this->getPaymentProperty('castka_platby'), 2, '.', ''),
//        'vs' => $this->_paymentObject->createProviderTransactionV3(),
//        'cs' => '0000',
//        'ss' => '',
//        'av1' => sprintf('#%1$s', $this->_profileSettings['idVod']),
//        'av2' => '',
//        'av3' => '',
//        'av4' => '',
//        'rurl' => $backurl,

        return $data;
    }

    public function sendData($data)
    {
        $signer = new OpenSSLSign();

        $xml = $this->convertUTF8($this->vytvorXML($data));
        $signData = $signer->signXML($xml, $this->getSignCert(), $this->getPrivKey(), $this->getTmpFile());
        $signature = sprintf('<signed><id>%1$s</id><message>%2$s</message></signed>',
             $this->getIdVOD(), $signData);
        $signature = $this->convertUTF8($signature);
        $encryptedsignature = $signer->signXMLcrypt($signature, $this->getCsobPublicKey());

        //$this->logger->debug('MSignXMLcrypt result: ' . $encryptedsignature);

        $zprava = '<zprava ofce="3111X">' . $encryptedsignature . '</zprava>';
//        $result = '<input type=hidden name="ZPRAVA" value="' . htmlspecialchars($zprava) . '">';

//        $sharedSecret = $this->getParameter('sharedSecret');

//        $input = "{$this->getMid()}{$this->getAmount()}{$this->getVs()}{$this->getCs()}{$this->getRurl()}";
//        $data['SIGN'] = $this->generateSignature($input);

        $requestData = array(
            'ZPRAVA' => $zprava,
        );

        return $this->response = new PurchaseResponse($this, $requestData);
    }

    public function generateSignature($data)
    {
//        $sign = new HmacSign();
//        return $sign->sign($data, $this->getParameter('sharedSecret'));
    }

    public function getEndpoint()
    {
//        if ($this->getTestmode()) {
//            return 'https://nib.vub.sk/nepay/merchant'; // vub test server
//             return 'https://platby.tomaj.sk/payment/eplatby-hmac';
//        } else {
//            return 'https://ib.vub.sk/e-platbyeuro.aspx';
//        }

        return 'https://bb24.csob.sk/Channels.aspx';
    }

    /**
     * Pomocna funkcia pre zakodovanie dat do UTF-8
     * @param string $data
     * @return string
     */
    private function convertUTF8($data)
    {
        $result = utf8_encode($data);
        return $result;
    }

    /**
     * Pomocna funkcia pre zakodovanie dat do BASE64
     * @param string $data
     * @return string
     */
    private function convertBase64($data)
    {
        $result = base64_encode($data);
        return $result;
    }

    /**
     * Pomocna funkcia pre dekodovanie dat z BASE64
     * @param string $data
     * @return string
     */
    private function decodeBase64($data)
    {
        return base64_decode($data);
    }

    public function getTmpFile()
    {
        return $this->getParameter('tmpFile');
    }

    public function setTmpFile($value)
    {
        return $this->setParameter('tmpFile', $value);
    }

    public function getSharedSecret()
    {
        return $this->getParameter('sharedSecret');
    }

    public function setSharedSecret($value)
    {
        return $this->setParameter('sharedSecret', $value);
    }

    public function getVs()
    {
        return $this->getParameter('vs');
    }

    public function setVs($value)
    {
        return $this->setParameter('vs', $value);
    }

    public function getCs()
    {
        return $this->getParameter('cs');
    }

    public function setCs($value)
    {
        return $this->setParameter('cs', $value);
    }

    public function getSs()
    {
        return $this->getParameter('ss');
    }

    public function setSs($value)
    {
        return $this->setParameter('ss', $value);
    }

    public function getRsms()
    {
        return $this->getParameter('rsms');
    }

    public function setRsms($value)
    {
        return $this->setParameter('rsms', $value);
    }

    public function getRem()
    {
        return $this->getParameter('rem');
    }

    public function setRem($value)
    {
        return $this->setParameter('rem', $value);
    }

    public function getRurl()
    {
        return $this->getParameter('rurl');
    }
    
    public function setRurl($value)
    {
        return $this->setParameter('rurl', $value);
    }

    private function getIdVOD()
    {
        return $this->getParameter('idVOD');
    }

    public function setIdVOD($value)
    {
        return $this->setParameter('idVOD', $value);
    }

    private function getProtiucet()
    {
        return $this->getParameter('protiucet');
    }

    public function setProtiucet($value)
    {
        return $this->setParameter('protiucet', $value);
    }

    private function getKodBankaProti()
    {
        return $this->getParameter('kodBankaProti');
    }

    public function setKodBankaProti($value)
    {
        return $this->setParameter('kodBankaProti', $value);
    }

    private function getAV1()
    {
        return $this->getParameter('av1');
    }

    public function setAV1($value)
    {
        return $this->setParameter('av1', $value);
    }

    private function getAV2()
    {
        return $this->getParameter('av2');
    }

    public function setAV2($value)
    {
        return $this->setParameter('av2', $value);
    }

    private function getAV3()
    {
        return $this->getParameter('av3');
    }

    public function setAV3($value)
    {
        return $this->setParameter('av3', $value);
    }

    private function getAV4()
    {
        return $this->getParameter('av4');
    }

    public function setAV4($value)
    {
        return $this->setParameter('av4', $value);
    }

    private function getSignCert()
    {
        return $this->getParameter('signCert');
    }

    public function setSignCert($value)
    {
        return $this->setParameter('signCert', $value);
    }

    private function getPrivKey()
    {
        return $this->getParameter('privKey');
    }

    public function setPrivKey($value)
    {
        return $this->setParameter('privKey', $value);
    }

    private function getCsobPublicKey()
    {
        return $this->getParameter('csobPublicKey');
    }

    public function setCsobPublicKey($value)
    {
        return $this->setParameter('csobPublicKey', $value);
    }

    private function getTrustedCAs()
    {
        return $this->getParameter('trustedCAs');
    }

    public function setTrustedCAs($value)
    {
        return $this->setParameter('trustedCAs', $value);
    }
}
