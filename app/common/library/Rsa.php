<?php

namespace app\common\library;

class Rsa
{
    private $rsaPublicKey = ''; //公钥
    private $rsaPrivateKey = '';//私钥

    public function __construct($rsaPublicKey = null, $rsaPrivateKey = null)
    {
        $this->setKey($rsaPublicKey, $rsaPrivateKey);
    }

    /**
     * 禁止克隆
     */
    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * 设置公钥私钥
     * @param null $rsaPublicKey
     * @param null $rsaPrivateKey
     */
    public function setKey($rsaPublicKey = null, $rsaPrivateKey = null)
    {
        if (!is_null($rsaPublicKey)) {
            $this->rsaPublicKey = $this->formatRsaPublic($rsaPublicKey);
        }
        if (!is_null($rsaPrivateKey)) {
            $this->rsaPrivateKey = $this->formatRsaPrivate($rsaPrivateKey);
        }
    }

    /**
     * RSA 公钥加密
     * @param string $data 明文字符串类型
     * @param string $code 密文编码(base64/hex/bin)
     * @param int $padding 填充方式（貌似php有bug，所以目前仅支持OPENSSL_PKCS1_PADDING）
     * @return bool|string 密文
     */
    public function encrypt($data = '', $code = 'base64', $padding = OPENSSL_PKCS1_PADDING)
    {
        $ret = false;
        $type = 'en';
        $data = $this->buildParams($data, $type);
        if (!$this->_checkPadding($padding, $type)) return 'check padding error';
        //公钥加密
        if (openssl_public_encrypt($data, $result, $this->rsaPublicKey, $padding)) {
            //对加密数据进行编码
            $ret = $this->_encode($result, $code);
        }
        return $ret;
    }

    /**
     * RSA 私钥解密
     * @param string $data 密文
     * @param string $code 密文编码（base64/hex/bin）
     * @param int $padding 填充方式（OPENSSL_PKCS1_PADDING / OPENSSL_NO_PADDING）
     * @param bool $rev 是否反转明文（When passing Microsoft CryptoAPI-generated RSA cyphertext, revert the bytes in the block）
     * @return bool|mixed|string
     */
    public function decrypt($data = '', $code = 'base64', $padding = OPENSSL_PKCS1_PADDING, $rev = false)
    {
        $ret = false;
        $type = 'de';
        $data = $this->_decode($data, $code);
        if (!$this->_checkPadding($padding, $type)) return 'check padding error';
        if ($data !== false) {
            //私钥解密
            if (openssl_private_decrypt($data, $result, $this->rsaPrivateKey, $padding)) {
                $ret = $rev ? $this->buildParams($result, $type) : '' . $result;
            }
        }
        return $ret;
    }

    /**
     * RSA 签名
     * @param array $data 参与签名的关联数组
     * @param string $code 编码格式
     * @param string $signType RSA类型(默认RSA2)
     * @return string 签名值
     */
    public function sign($data, $code = 'base64', $signType = "RSA2")
    {
        $type = 'en';
        $data = $this->buildParams($data, $type);
        if (!$this->rsaPrivateKey) {
            return '私钥数据错误，请检查RSA私钥配置项';
        }
        $sign = false;
        if (strtolower($signType) == 'rsa2') {
            openssl_sign($data, $sign, $this->rsaPrivateKey, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($data, $sign, $this->rsaPrivateKey);
        }
        if ($sign !== false) {
            return $this->_encode($sign, $code);
        }
        exit('签名失败');

    }

    /**
     * 签名校验
     * @param $data
     * @param $sign
     * @param string $code
     * @param string $signType
     * @return bool
     */
    public function verify($data, $sign, $code = 'base64', $signType = "RSA2")
    {

        $data = $this->buildParams($data, 'en');
        $ret = false;
        if ($sign !== false) {
            if (strtolower($signType) == 'rsa2') {
                $result = openssl_verify($data, $this->_decode($sign, $code), $this->rsaPublicKey, OPENSSL_ALGO_SHA256);
            } else {
                $result = openssl_verify($data, $this->_decode($sign, $code), $this->rsaPublicKey);
            }
            switch ($result) {
                case 1:
                    $ret = true;
                    break;
                case 0:
                case -1:
                default:
                    $ret = false;
            }
        }
        return $ret;

    }

    /**
     * 构建所需数据
     * @param $data
     * @param string $type
     * @return mixed|string
     */
    public function buildParams($data, $type = 'en')
    {
        return $type == 'en' ? serialize($data) : unserialize($data);
    }

    /**
     * 根据code进行数据转码
     * @param $data
     * @param string $code
     * @return string
     */
    private function _encode($data, $code = 'base64')
    {
        switch (strtolower($code)) {
            case 'base64':
                $data = base64_encode($data);
                break;
            case 'hex':
                $data = bin2hex($data);
                break;
            case 'bin':
            default:
        }
        return $data;
    }

    /**
     * 根据code进行数据解码
     * @param $data
     * @param string $code
     * @return bool|false|string
     */
    private function _decode($data, $code = 'base64')
    {
        switch (strtolower($code)) {
            case 'base64':
                $data = base64_decode($data);
                break;
            case 'hex':
                $data = self::_hex2bin($data);
                break;
            case 'bin':
            default:
        }
        return $data;
    }

    /**
     * 十六进制转回原本数据
     * @param bool $hex
     * @return bool|string
     */
    private function _hex2bin($hex = false)
    {
        $ret = $hex !== false && preg_match('/^[0-9a-fA-F]+$/i', $hex) ? pack("H*", $hex) : false;
        return $ret;
    }

    /**
     * 检测填充类型
     * 加密只支持PKCS1_PADDING
     * 解密支持PKCS1_PADDING和NO_PADDING
     * @param int $padding 填充模式
     * @param string $type 加密en/解密de
     * @return bool
     */
    private function _checkPadding($padding, $type)
    {
        if ($type == 'en') {
            switch ($padding) {
                case OPENSSL_PKCS1_PADDING:
                    $ret = true;
                    break;
                default:
                    $ret = false;
            }
        } else {
            switch ($padding) {
                case OPENSSL_PKCS1_PADDING:
                case OPENSSL_NO_PADDING:
                    $ret = true;
                    break;
                default:
                    $ret = false;
            }
        }
        return $ret;
    }

    /**
     * 格式化公钥
     * @param $publicKey
     * @return bool|string
     */
    public function formatRsaPublic($publicKey)
    {
        $publicKeyBegin = "-----BEGIN PUBLIC KEY-----\n";
        $publicKeyEnd = "\n-----END PUBLIC KEY-----";
        $publicKey = preg_replace("/\s/", "", $publicKey);
        $publicKey = $publicKeyBegin . wordwrap($publicKey, '64', "\n", true) . $publicKeyEnd;
        return $publicKey ? openssl_pkey_get_public($publicKey) : false;

    }

    /**
     * 格式化私钥
     * @param $privateKey
     * @return bool|string
     */
    public function formatRsaPrivate($privateKey)
    {
        $privateKeyBegin = "-----BEGIN RSA PRIVATE KEY-----\n";
        $privateKeyEnd = "\n-----END RSA PRIVATE KEY-----";
        $privateKey = preg_replace("/\s/", "", $privateKey);
        $privateKey = $privateKeyBegin . wordwrap($privateKey, '64', "\n", true) . $privateKeyEnd;
        return $privateKey ? openssl_pkey_get_private($privateKey) : false;
    }

    /**
     * arrayFilterRecursive 清除多维数组里面的空值
     * @param array $array
     * @return array
     * @author   liuml
     * @DateTime 2018/12/3  11:27
     */
    public function arrayFilterRecursive(array &$arr)
    {
        if (count($arr) < 1) {
            return [];
        }
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $arr[$k] = $this->arrayFilterRecursive($v);
            }
            if ($arr[$k] == '') {
                unset($arr[$k]);
            }
        }
        return $arr;
    }

}