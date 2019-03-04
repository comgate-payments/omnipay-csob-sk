<?php

namespace Omnipay\CsobSk\Sign;

class OpenSSLSign
{
    /**
     * Pomocna funkcia pre podpis dat privatnym klucom obchodnika.
     * @param string $data
     * @return boolean|string vracia FALSE pri chybe, inak vysledny popdis
     */
    public function signXML($data, $signCert, $privKey, $tmpFile)
    {
        $msgFileName = $tmpFile->getTempFileName('msg.txt');
        $outFileName = $tmpFile->getTempFileName('out.txt');

        $fp = fopen($msgFileName, "w");
        fwrite($fp, $data);
        fclose($fp);

        $result = openssl_pkcs7_sign($msgFileName, $outFileName, $signCert, $privKey, array(), PKCS7_BINARY);
        $error = '';
        while ($msg = openssl_error_string())
        {
            $error .= $msg . PHP_EOL;
        }

        if (!empty($error))
        {
            throw new Exception('3a. Errors during OPENSSL_PCK7_SIGN: ' . $error);
        }

        if ($result == false)
        {
            throw new Exception('OPENSSL_PCK7_SIGN failed.');
        }

        $data = file_get_contents($outFileName);
        //$this->logger->debug("3b.DATA po PODPISE vystup OPENSSL_PCKS7_SIGN s hlavickou Podpis je uz BASE64: " . $data);

        //zmazanie hlavicky podpisu. Je potrebne pre spravne unsign na strane banky. Z povodneho podpisu musi zostat iba retazes s BASE64
        $pos1 = stripos($data, "base64");
        $data = substr($data, $pos1 + 8);
        //$this->logger->debug("3c.DATA po PODPISE vystup OPENSSL_PCKS7_SIGN bez hlavicky: " . $data);

        unset($msgFileName);
        unset($outFileName);
        return $data;
    }


    /**
     * Pomocna funkcia pre zasifrovanie dat privatnym klucom obchodnika.
     * @param string $source
     * @return string
     */
    public function signXMLcrypt($source, $publicKey)
    {
        $public = openssl_get_publickey($publicKey);
        $a_key = openssl_pkey_get_details($public);
        $chunkSize = ceil($a_key['bits'] / 8) - 11;
        //echo "ChunkSIZE:".$chunkSize;
        $crypttext = null;
        $output = null;
        while ($source)
        {
            $chunk = substr($source, 0, $chunkSize);
            $source = substr($source, $chunkSize);
            openssl_public_encrypt($chunk, $crypttext, $public);
            $output .= $crypttext;
            $crypttext = null;
        }

        openssl_free_key($public);
        $result = base64_encode($output);

        return $result;
    }

}
