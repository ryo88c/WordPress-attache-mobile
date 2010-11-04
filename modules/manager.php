<?php
/**
 * 管理画面系マネージャークラス
 *
 * @package wp_attache
 * @author HAYASHI Ryo<ryo@spais.co.jp>
 * @version 0.0.1
 */
class wp_attache_mobile_manager extends wp_attache_mobile {
    /**
     * セッティングフォーム
     */
    function display_main_menu()
    {
        $action = 'wp_attache_mobile_setting';
        $file = basename(__FILE__);
        add_settings_section('general', __('General', 'wp_attache_mobile'), array(&$this, 'get_section'), $file);
        add_settings_field('modules', __('Modules', 'wp_attache_mobile'), array(&$this, 'get_element_modules'), $file, 'general');

        ?><div class="wrap">
			<div id="icon-options-general" class="icon32"><br /></div>
			<h2><?php _e('wp_attache_mobile settings', 'wp_attache_mobile')?></h2>
			<form method="post" action="<?php echo getenv('REQUEST_URI')?>">
				<?php settings_fields($action)?>
				<input type="hidden" name="wp_attache_mobile_action" value="<?php echo esc_attr($action)?>" />
				<?php do_settings_sections($file)?>
				<p class="submit"><input type="submit" name="submit" class="button-primary" value="<?php _e('Save')?>" /></p>
			</form>
		</div><?php
    }

    /**
     * セッティングアクション
     */
    function action_wp_attache_mobile_setting()
    {
        $options = $this -> get_option();
        $options['modules'] = $_POST['modules'];
        $this -> update_option($options);
    }

    /**
     * Section 用ダミー
     */
    function get_section()
    {
    }

    /**
     * モジュールリストを生成
     */
    function get_element_modules()
    {
        $options = $this -> get_option();

        ?><p><?php _e('Choose a module to use', 'wp_attache_mobile')?></p><ul>
            <li><label><input type="checkbox" name="modules[]" value="ua_sniffer"<?php if (in_array('ua_sniffer', $options['modules'])) echo 'checked="checked"'?> /><?php _e('UA Sniffer Module', 'wp_attache_mobile')?></label></li>
            <li><label><input type="checkbox" name="modules[]" value="emoji"<?php if (in_array('emoji', $options['modules'])) echo 'checked="checked"'?> /><?php _e('Emoji Module', 'wp_attache_mobile')?></label></li>
            <li><label><input type="checkbox" name="modules[]" value="session"<?php if (in_array('session', $options['modules'])) echo 'checked="checked"'?> /><?php _e('Session Module', 'wp_attache_mobile')?></label></li>
        </ul><?php
    }

    /**
     * メニュー項目を追加
     */
    function add_menus()
    {
        add_options_page(__('wp_attache_mobile', 'wp_attache_mobile'), __('wp_attache_mobile', 'wp_attache_mobile'), 10, basename(__FILE__), array(&$this, 'display_main_menu'));
    }

    /**
     * コンストラクタ
     * init アクションで呼び出される
     */
    function wp_attache_mobile_manager()
    {
        add_action('admin_menu', array(&$this, 'add_menus'));
        add_action('admin_notices', array('wp_attache_mobile', 'notice'));
        if (!isset($_POST) || !isset($_POST['wp_attache_mobile_action']) || !isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], $_POST['wp_attache_mobile_action'] . '-options')) return;
        if (!in_array($_POST['wp_attache_mobile_action'], array('wp_attache_mobile_setting'))) return;
        call_user_func(array(&$this, "action_{$_POST['wp_attache_mobile_action']}"));
    }
}
