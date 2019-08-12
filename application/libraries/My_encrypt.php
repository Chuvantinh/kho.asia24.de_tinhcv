<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Rsa_encrypt
 *
 * @author Pham Trong
 */
$include = realpath(dirname(__FILE__));
set_include_path($include . DIRECTORY_SEPARATOR . 'CryptLib');

class My_encrypt {

    //put your code here
    var $rsaPrivateKey = "MIICXAIBAAKBgQCqGKukO1De7zhZj6+H0qtjTkVxwTCpvKe4eCZ0FPqri0cb2JZfXJ/DgYSF6vUpwmJG8wVQZKjeGcjDOL5UlsuusFncCzWBQ7RKNUSesmQRMSGkVb1/3j+skZ6UtW+5u09lHNsj6tQ51s1SPrCBkedbNf0Tp0GbMJDyR4e9T04ZZwIDAQABAoGAFijko56+qGyN8M0RVyaRAXz++xTqHBLh3tx4VgMtrQ+WEgCjhoTwo23KMBAuJGSYnRmoBZM3lMfTKevIkAidPExvYCdm5dYq3XToLkkLv5L2pIIVOFMDG+KESnAFV7l2c+cnzRMW0+b6f8mR1CJzZuxVLL6Q02fvLi55/mbSYxECQQDeAw6fiIQXGukBI4eMZZt4nscy2o12KyYner3VpoeE+Np2q+Z3pvAMd/aNzQ/W9WaI+NRfcxUJrmfPwIGm63ilAkEAxCL5HQb2bQr4ByorcMWm/hEP2MZzROV73yF41hPsRC9m66KrheO9HPTJuo3/9s5p+sqGxOlFL0NDt4SkosjgGwJAFklyR1uZ/wPJjj611cdBcztlPdqoxssQGnh85BzCj/u3WqBpE2vjvyyvyI5kX6zk7S0ljKtt2jny2+00VsBerQJBAJGC1Mg5Oydo5NwD6BiROrPxGo2bpTbu/fhrT8ebHkTz2eplU9VQQSQzY1oZMVX8i1m5WUTLPz2yLJIBQVdXqhMCQBGoiuSoSjafUhV7i1cEGpb88h5NBYZzWXGZ37sJ5QsW+sJyoNde3xH8vdXhzU7eT82D6X/scw9RZz+/6rCJ4p0=";

    /*
     * $this->load->library("My_encrypt");
     * $plain_text = $this->my_encrypt->rsa_decrypt($EncryptText);
     */

    public function rsa_decrypt($ciphertext) {
        $oldIncludePath = get_include_path();
        $include = realpath(dirname(__FILE__));
        set_include_path($include . DIRECTORY_SEPARATOR . 'CryptLib');
        include_once('Crypt/RSA.php');
        $rsa = new Crypt_RSA();
        $rsa->loadKey($this->rsaPrivateKey);
        $plain_text = $rsa->decrypt(base64_decode($ciphertext));
        set_include_path($oldIncludePath);
        return $plain_text;
    }

    public function rsa_encrypt($plain_text, $publicKey) {
        $oldIncludePath = get_include_path();
        $include = realpath(dirname(__FILE__));
        set_include_path($include . DIRECTORY_SEPARATOR . 'CryptLib');
        include_once('Crypt/RSA.php');
        $rsa = new Crypt_RSA();
        $rsa->loadKey($publicKey);
        $ciphertext = $rsa->encrypt($plain_text);
        set_include_path($oldIncludePath);
        return base64_encode($ciphertext);
    }

    public function creat_public_key() {
        $oldIncludePath = get_include_path();
        $include = realpath(dirname(__FILE__));
        set_include_path($include . DIRECTORY_SEPARATOR . 'CryptLib');
        include_once('Crypt/RSA.php');
        $rsa = new Crypt_RSA();

        $rsa->setPrivateKeyFormat(CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
        $rsa->setPublicKeyFormat(CRYPT_RSA_PUBLIC_FORMAT_PKCS1);
        //define('CRYPT_RSA_EXPONENT', 65537);
        //define('CRYPT_RSA_SMALLEST_PRIME', 64); // makes it so multi-prime RSA is used
        $a = $rsa->createKey(); // == $rsa->createKey(1024) where 1024 is the key size
        return $a;
    }

}
