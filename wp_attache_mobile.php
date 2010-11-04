<?php
/**
 * Plugin Name: wp_attache_mobile
 * Plugin URI: http://spais.co.jp/%E3%83%97%E3%83%AD%E3%83%80%E3%82%AF%E3%83%88-product/wordpress/wp_attache_mobile/
 * Description: Attached "Japan's" mobile functions for WordPress.
 * Version: 0.0.1
 * Author: SPaiS Inc.
 * Author URI: http://spais.co.jp/
 */
/**
 * TODO:
 *      - 絵文字をどう取り扱うか決める→たぶん _s() みたいなテンプレートタグを用意する
 *      - セッション関係のヘルパーを揃える
 *      - 管理画面はモバイル対応しない
 *      - PEAR 同梱にするかどうか決める→同梱版と同梱しない版両方つくるかどうか
 */
/**
 * メインコントローラー
 *
 * @package wp_attache_mobile
 * @author HAYASHI Ryo<ryo@spais.co.jp>
 * @version 0.0.1
 */
class wp_attache_mobile_controller extends wp_attache_mobile {

    /**
     * 初期処理
     */
    function init()
    {
        load_plugin_textdomain('wp_attache_mobile', false, 'wp_attache_mobile/languages');
        wp_attache_mobile_controller :: boot();
    }

    /**
     * 登録されたモジュールの起動
     * manager は管理画面の場合自動的に起動する
     */
    function boot_modules()
    {
        if (is_admin()) $this -> boot('manager');
        $options = $this -> get_option();
        $this -> boot('ua');
        foreach($options['modules'] as $module) $this -> boot($module);
    }

    /**
     * モジュールの起動(シングルトン)
     *
     * @param  $name string 読み込むモジュール
     * @param  $type string OPTIONAL モジュールのタイプ(wp_attache_mobile|pear)
     * @param  $args mixed モジュールに渡す引数(PEAR のみ使用)
     */
    function &boot($name = null, $type = 'wp_attache_mobile', $args = null)
    {
        static $i = array();
        if (empty($name)) {
            $class = __CLASS__;
            $name = 'controller';
            $i[$name] = new $class;
        } elseif (!isset($i[$name])) {
            if ($type === 'wp_attache_mobile') {
                if (($path = realpath(dirname(__FILE__) . "/modules/{$name}.php")) === false) {
                    wp_attache_mobile :: notice(sprintf(__('wp_attache_mobile: %s module not found.', 'wp_attache_mobile'), ucfirst($name)), 'error');
                } else {
                    include($path);
                    $class = "wp_attache_mobile_{$name}";
                    $i[$name] = new $class;
                }
            } elseif ($type === 'pear') {
                if (($path = realpath(sprintf("%s/%s.php", wp_attache_mobile_controller :: pear_path(), str_replace('_', '/', $name)))) === false) {
                    wp_die(sprintf(__('wp_attache_mobile: %s PEAR library not found.', 'wp_attache_mobile'), $name));
                    wp_attache_mobile :: notice(sprintf(__('wp_attache_mobile: %s PEAR library not found.', 'wp_attache_mobile'), $name), 'error');
                } else {
                    include($path);
                    if (method_exists($name, 'singleton')) $i[$name] = &call_user_func(array($name, 'singleton'), $args);
                    else $i[$name] = new $name;
                }
            } else {
                wp_die(sprintf(__('wp_attache_mobile: %s is invalid module type.', 'wp_attache_mobile'), $type));
                wp_attache_mobile :: notice(sprintf(__('wp_attache_mobile: %s is invalid module type.', 'wp_attache_mobile'), $type), 'error');
            }
        }
        return $i[$name];
    }

    function pear_path()
    {
        static $path;
        if (empty($path)) {
            if (($path = realpath(dirname(__FILE__) . '/pear')) === false)
                wp_die(__('wp_attache_mobile: PEAR not found.', 'wp_attache_mobile'), 'error');
        }
        return $path;
    }

    /**
     * コンストラクタ
     */
    function wp_attache_mobile_controller()
    {
        ini_set('include_path',/* ini_get('include_path').':'. */ '.:' . $this -> pear_path());
        add_action('init', array(&$this, 'boot_modules'), -1);
    }
}
wp_attache_mobile_controller :: init();

/**
 * 基底クラス
 *
 * @package wp_attache
 * @author HAYASHI Ryo<ryo@spais.co.jp>
 * @version 0.0.1
 */
class wp_attache_mobile {
    var $_defaultOptions = array('modules' => array());
    var $_notices = array();

    /**
     * オプションの取得
     *
     * @see get_option
     */
    function get_option()
    {
        return get_option(__CLASS__, $this -> _defaultOptions);
    }

    /**
     * オプションの更新
     *
     * @see update_option
     * @param  $newOptions mixed 更新内容
     */
    function update_option($newOptions)
    {
        return update_option(__CLASS__, array_merge($this -> _defaultOptions, $newOptions));
    }

    /**
     * 管理画面の Notice メッセージ用
     * 引数渡せばセッター、渡さなければセッター
     *
     * @param  $message string OPTIONAL 表示するメッセージ
     * @param  $class string OPTIONAL notice の種類
     */
    function notice($message = null, $class = 'notice')
    {
        static $notices = array('notice' => array(), 'error' => array(), 'updated' => array());
        if (!empty($message)) {
            foreach($notices as $class => $notice)
            printf('<div id="message" class="%s fade"><p>%s</p></div>', $class, implode("<br />\n", $notice));
        } else $notices[$class][] = $message;
    }
}

function _s($id, $echo = true)
{
}
