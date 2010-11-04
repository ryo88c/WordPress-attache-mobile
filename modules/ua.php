<?php
/**
 * UA モデル
 * 
 * @package wp_attache
 * @author HAYASHI Ryo<ryo@spais.co.jp> 
 * @version 0.0.1
 */
class wp_attache_mobile_ua extends wp_attache_mobile {
    var $_ua;
    var $_isMobile = false;
    var $_agentMobile;
    var $_agents = array('DEFAULT' => 'default', 'MOBILE' => 'mobile', 'DOCOMO' => 'docomo',
        'EZWEB' => 'ezweb', 'SOFTBANK' => 'softbank', 'BOT' => 'bot', 'IPHONE' => 'iphone', 'ANDROID' => 'android');

    /**
     * UA (とモバイルフラッグ)の判定
     */
    function set_ua()
    {
        $this -> _agentMobile = &wp_attache_mobile_controller :: boot('Net_UserAgent_Mobile', 'pear');
        if ($this -> _agentMobile -> isNonMobile()) {
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false) {
                $this -> _ua = $this -> _agents['IPHONE'];
            } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false) {
                $this -> _ua = $this -> _agents['ANDROID'];
            } else {
                $this -> _ua = $this -> _agents['DEFAULT'];
            } 
        } else {
            $this -> _ua = strtolower($this -> _agentMobile -> getCarrierLongName());
            $this -> _isMobile = true;
        } 
    } 

    /**
     * UA フラッグを返す
     * 
     * @see wp_attache_mobile_ua::$_agents
     */
    function get_ua()
    {
        return $this -> _ua;
    } 

    /**
     * モバイルでのアクセスの場合に true
     */
    function is_mobile()
    {
        return $this -> _isMobile;
    } 

    /**
     * 携帯のユニークIDを取得
     * 
     * @return string 
     */
    function get_serialNumber()
    {
        $serial = '';
        switch ($this -> _ua) {
        case $this -> _agents['DOCOMO']:
            $serial = $this -> _agentMobile -> getCardID();
            if ($serial === '') {
                $serial = $this -> _agentMobile -> getSerialNumber();
            } 
            break;
        case $this -> _agents['EZWEB']:
            $serial = $this -> _agentMobile -> getHeader('X-UP-SUBNO');
            break;
        case $this -> _agents['SOFTBANK']:
            $serial = $this -> _agentMobile -> getSerialNumber();
            break;
        default :
            break;
        } 
        return $serial;
    } 

    /**
     * 画面サイズの縦、横のサイズを取得
     * 
     * @return array array(width, height)
     */
    function get_display_size()
    {
        static $size;

        if (!isset($size)) {
            $display = $this -> _agentMobile -> getDisplay();
            $size = $display -> getSize();
        } 
        return $size;
    } 

    /**
     * 携帯の表示可能文字数を取得
     * 
     * @return array array(width, height)
     */
    function get_display_byte_size()
    {
        static $byteSize;
        if (!isset($byteSize)) {
            $display = $this -> _agentMobile -> getDisplay();
            $byteSize = array($display -> getWidthBytes(), $display -> getHeightBytes());
        } 
        return $byteSize;
    } 

    /**
     * キャッシュサイズの取得
     * 
     * @return integer 
     */
    function get_cache_size()
    {
        switch ($this -> _ua) {
        case $this -> _agents['DOCOMO']:
            $size = $this -> _agentMobile -> getCacheSize() * 1024;
            break;
        case $this -> _agents['EZWEB']:
            $headers = getallheaders();
            $size = $headers['x-up-devcap-max-pdu'];
            break;
        case $this -> _agents['SOFTBANK']:
            $phone = $this -> _agentMobile -> getName(); // 'J-PHONE'
            $version = (int) $this -> _agentMobile -> getVersion(); // 2.0
            if ($phone == 'J-PHONE') {
                if ($version <= 3.0) {
                    $size = 6000;
                } elseif ($version <= 4.2) {
                    $size = 12000;
                } elseif ($version <= 4.3) {
                    $size = 30000;
                } elseif ($version <= 5.0) {
                    $size = 200000;
                } else {
                    $size = 200000;
                } 
            } else {
                $size = 300000;
            } 
            break;
        default :
            $size = false;
        } 
        return $size;
    } 

    /**
     * コンストラクタ
     */
    function wp_attache_mobile_ua()
    {
        $this -> set_ua();
    } 
} 
