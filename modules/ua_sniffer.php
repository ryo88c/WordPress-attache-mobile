<?php
/**
 * UA スニッファクラス
 * 
 * @package wp_attache
 * @author HAYASHI Ryo<ryo@spais.co.jp> 
 * @version 0.0.1
 */
class wp_attache_mobile_ua_sniffer extends wp_attache_mobile {
    /**
     * スニッフィング
     * 
     * @see wp-includes/template-loader.php
     */
    function sniffing($template)
    {
        $ua = &wp_attache_mobile_controller :: boot('ua');
        if ($ua -> _isMobile === true) {
            $theme = get_template();
            $themeRoots = get_theme_roots();
            $uaTemplate = str_replace('.php', ".{$ua->_ua}.php", $template);
            $mobileTemplate = str_replace('.php', ".{$ua->_agents['MOBILE']}.php", $template);
            if (file_exists($uaTemplate)) {
                $template = $uaTemplate;
            } elseif (file_exists($mobileTemplate)) {
                $template = $mobileTemplate;
            } 
        } 
        return $template;
    } 

    /**
     * コンストラクタ
     */
    function wp_attache_mobile_ua_sniffer()
    {
        add_filter('template_include', array(&$this, 'sniffing'));
    } 
} 
