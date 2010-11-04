<?php
/**
 * セッション機能補完クラス
 * 
 * @author HAYASHI Ryo<ryo@spais.co.jp> 
 * @version 0.0.1
 */
class wp_attache_mobile_session {
    /**
     * クッキーが使えない端末用にURLからセッションクエリー付きURLを返す
     * 
     * @param string $url URL
     * @return string 
     */
    function get_session_query($url = false)
    {
        $sessionName = session_name();
        if (!isset($_COOKIE[$sessionName])) {
            $url = preg_replace("/&*{$sessionName}=[^&]+/is", '', $url);
            $con = ($url && strpos($url, "?")) ? '&' : '?';
            $url .= "{$con}{$sessionName}=" . session_id();
        } 
        return $url;
    } 

    function wp_attache_mobile_session()
    {
    } 
} 
