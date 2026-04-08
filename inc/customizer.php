<?php
if ( ! defined( 'ABSPATH' ) ) { die; }

/* ── Admin Menu ── */
add_action( 'admin_menu', function () {
    add_theme_page(
        'ZHUOER 主题设置',
        'ZHUOER 主题设置',
        'manage_options',
        'zhuoer-settings',
        'zhuoer_settings_page_html'
    );
} );

/* ── Register all settings ── */
add_action( 'admin_init', function () {
    $opts = array(
        'zhuoer_home_title', 'zhuoer_home_description', 'zhuoer_og_image',
        'zhuoer_baidu_verify', 'zhuoer_baidu_tongji_id', 'zhuoer_ga_id', 'zhuoer_icp_number', 'zhuoer_icp_url',
        'zhuoer_primary_color', 'zhuoer_hero_title', 'zhuoer_hero_subtitle',
        'zhuoer_cta_text', 'zhuoer_cta_url',
        'zhuoer_show_featured_image', 'zhuoer_show_author_bio',
        'zhuoer_show_sidebar_archive', 'zhuoer_show_sidebar_home', 'zhuoer_show_sidebar_single',
    );
    foreach ( $opts as $opt ) {
        register_setting( 'zhuoer_group', $opt, 'sanitize_text_field' );
    }
    $social = array( 'weibo', 'zhihu', 'bilibili', 'xiaohongshu', 'github', 'twitter', 'facebook', 'instagram', 'linkedin' );
    foreach ( $social as $s ) {
        register_setting( 'zhuoer_group', "zhuoer_social_{$s}", 'esc_url_raw' );
    }
} );

/* ── AJAX save handler ── */
add_action( 'wp_ajax_zhuoer_save_settings', function () {
    check_ajax_referer( 'zhuoer_save_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( '无权限' );
    }

    $opts = array(
        'zhuoer_home_title', 'zhuoer_home_description', 'zhuoer_og_image',
        'zhuoer_baidu_verify', 'zhuoer_baidu_tongji_id', 'zhuoer_ga_id', 'zhuoer_icp_number', 'zhuoer_icp_url',
        'zhuoer_primary_color', 'zhuoer_hero_title', 'zhuoer_hero_subtitle',
        'zhuoer_cta_text', 'zhuoer_cta_url',
        'zhuoer_show_featured_image', 'zhuoer_show_author_bio',
        'zhuoer_show_sidebar_archive', 'zhuoer_show_sidebar_home', 'zhuoer_show_sidebar_single',
    );

    foreach ( $opts as $key ) {
        if ( isset( $_POST[ $key ] ) ) {
            update_option( $key, sanitize_text_field( $_POST[ $key ] ) );
        } else {
            update_option( $key, '0' );
        }
    }

    $social = array( 'weibo', 'zhihu', 'bilibili', 'xiaohongshu', 'github', 'twitter', 'facebook', 'instagram', 'linkedin' );
    foreach ( $social as $s ) {
        $k = "zhuoer_social_{$s}";
        if ( isset( $_POST[ $k ] ) ) {
            update_option( $k, esc_url_raw( $_POST[ $k ] ) );
        } else {
            update_option( $k, '' );
        }
    }

    wp_send_json_success( '设置已保存！' );
} );

/* ── Settings Page HTML ── */
function zhuoer_settings_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    $nonce = wp_create_nonce( 'zhuoer_save_nonce' );

    $social_labels = array(
        'weibo'        => '微博',
        'zhihu'        => '知乎',
        'bilibili'     => 'B站',
        'xiaohongshu'  => '小红书',
        'github'       => 'GitHub',
        'twitter'      => 'Twitter / X',
        'facebook'     => 'Facebook',
        'instagram'    => 'Instagram',
        'linkedin'     => 'LinkedIn',
    );
    ?>
    <div class="wrap">
        <h1>ZHUOER 主题设置</h1>
        <p id="zhuoer-msg" style="display:none;padding:10px 15px;border-radius:4px;margin-bottom:15px;"></p>

        <h2 class="nav-tab-wrapper">
            <a href="#" class="nav-tab nav-tab-active" data-tab="seo">SEO 与统计</a>
            <a href="#" class="nav-tab" data-tab="homepage">首页设置</a>
            <a href="#" class="nav-tab" data-tab="social">社交链接</a>
            <a href="#" class="nav-tab" data-tab="posts">文章显示</a>
            <a href="#" class="nav-tab" data-tab="layout">布局设置</a>
        </h2>

        <form id="zhuoer-settings-form" onsubmit="return false;">

            <!-- SEO -->
            <div id="tab-seo" class="tab-content">
                <h2>SEO 与统计分析</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">首页标题</th>
                        <td>
                            <input type="text" name="zhuoer_home_title" value="<?php echo esc_attr( get_option( 'zhuoer_home_title', '' ) ); ?>" class="regular-text" />
                            <p class="description">留空则使用网站名称</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">首页描述</th>
                        <td>
                            <textarea name="zhuoer_home_description" rows="3" class="regular-text"><?php echo esc_textarea( get_option( 'zhuoer_home_description', '' ) ); ?></textarea>
                            <p class="description">建议 120–150 个字符</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">OG 图片 URL</th>
                        <td>
                            <input type="url" name="zhuoer_og_image" value="<?php echo esc_attr( get_option( 'zhuoer_og_image', '' ) ); ?>" class="regular-text" />
                            <p class="description">微信 / 微博分享图，建议尺寸 1200×630px</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">百度统计 ID</th>
                        <td>
                            <input type="text" name="zhuoer_baidu_tongji_id" value="<?php echo esc_attr( get_option( 'zhuoer_baidu_tongji_id', '' ) ); ?>" class="regular-text" />
                            <p class="description">在百度统计后台获取</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">百度站长验证</th>
                        <td>
                            <input type="text" name="zhuoer_baidu_verify" value="<?php echo esc_attr( get_option( 'zhuoer_baidu_verify', '' ) ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">ICP备案号</th>
                        <td>
                            <input type="text" name="zhuoer_icp_number" value="<?php echo esc_attr( get_option( 'zhuoer_icp_number', '' ) ); ?>" class="regular-text" placeholder="如：京ICP备XXXXXXXX号" />
                            <input type="text" name="zhuoer_icp_url" value="<?php echo esc_attr( get_option( 'zhuoer_icp_url', 'https://beian.miit.gov.cn/' ) ); ?>" class="regular-text" placeholder="ICP备案链接，默认：https://beian.miit.gov.cn/" style="margin-top:4px" />
                            <p class="description">将显示在网站底部正中间</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Google Analytics ID</th>
                        <td>
                            <input type="text" name="zhuoer_ga_id" value="<?php echo esc_attr( get_option( 'zhuoer_ga_id', '' ) ); ?>" class="regular-text" placeholder="G-XXXXXXXXXX" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">主题色调</th>
                        <td>
                            <input type="color" name="zhuoer_primary_color" value="<?php echo esc_attr( get_option( 'zhuoer_primary_color', '#1a73e8' ) ); ?>" />
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Homepage -->
            <div id="tab-homepage" class="tab-content" style="display:none;">
                <h2>首页 Hero 区域</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Hero 标题</th>
                        <td><input type="text" name="zhuoer_hero_title" value="<?php echo esc_attr( get_option( 'zhuoer_hero_title', '' ) ); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th scope="row">Hero 副标题</th>
                        <td><textarea name="zhuoer_hero_subtitle" rows="3" class="regular-text"><?php echo esc_textarea( get_option( 'zhuoer_hero_subtitle', '' ) ); ?></textarea></td>
                    </tr>
                    <tr>
                        <th scope="row">CTA 按钮文字</th>
                        <td><input type="text" name="zhuoer_cta_text" value="<?php echo esc_attr( get_option( 'zhuoer_cta_text', '' ) ); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th scope="row">CTA 按钮链接</th>
                        <td><input type="url" name="zhuoer_cta_url" value="<?php echo esc_attr( get_option( 'zhuoer_cta_url', '' ) ); ?>" class="regular-text" /></td>
                    </tr>
                </table>
            </div>

            <!-- Social -->
            <div id="tab-social" class="tab-content" style="display:none;">
                <h2>社交媒体链接</h2>
                <table class="form-table">
                    <?php foreach ( $social_labels as $key => $label ) : ?>
                    <tr>
                        <th scope="row"><?php echo esc_html( $label ); ?></th>
                        <td>
                            <input type="url" name="zhuoer_social_<?php echo esc_attr( $key ); ?>"
                                   value="<?php echo esc_attr( get_option( "zhuoer_social_{$key}", '' ) ); ?>"
                                   class="regular-text" placeholder="https://" />
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <!-- Posts -->
            <div id="tab-posts" class="tab-content" style="display:none;">
                <h2>文章显示设置</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">显示选项</th>
                        <td>
                            <label>
                                <input type="checkbox" name="zhuoer_show_featured_image" value="1"
                                    <?php checked( get_option( 'zhuoer_show_featured_image', '1' ), '1' ); ?> />
                                文章详情页显示特色图片
                            </label>
                            <br/><br/>
                            <label>
                                <input type="checkbox" name="zhuoer_show_author_bio" value="1"
                                    <?php checked( get_option( 'zhuoer_show_author_bio', '1' ), '1' ); ?> />
                                文章底部显示作者介绍
                            </label>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Layout -->
            <div id="tab-layout" class="tab-content" style="display:none;">
                <h2>侧边栏可见性</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">显示侧边栏</th>
                        <td>
                            <label>
                                <input type="checkbox" name="zhuoer_show_sidebar_archive" value="1"
                                    <?php checked( get_option( 'zhuoer_show_sidebar_archive', '1' ), '1' ); ?> />
                                分类 / 标签 / 归档列表页（默认开启）
                            </label>
                            <br/><br/>
                            <label>
                                <input type="checkbox" name="zhuoer_show_sidebar_home" value="1"
                                    <?php checked( get_option( 'zhuoer_show_sidebar_home', '1' ), '1' ); ?> />
                                首页（默认开启）
                            </label>
                            <br/><br/>
                            <label>
                                <input type="checkbox" name="zhuoer_show_sidebar_single" value="1"
                                    <?php checked( get_option( 'zhuoer_show_sidebar_single', '0' ), '1' ); ?> />
                                文章详情页（默认关闭）
                            </label>
                        </td>
                    </tr>
                </table>
            </div>

            <p class="submit">
                <button type="button" class="button button-primary" id="zhuoer-save-btn">
                    保存设置
                </button>
            </p>
        </form>
    </div>

    <script>
    (function () {
        // Tab switching
        document.querySelectorAll('.nav-tab').forEach(function (tab) {
            tab.addEventListener('click', function (e) {
                e.preventDefault();
                var name = tab.dataset.tab;
                document.querySelectorAll('.tab-content').forEach(function (el) { el.style.display = 'none'; });
                document.querySelectorAll('.nav-tab').forEach(function (el) { el.classList.remove('nav-tab-active'); });
                var target = document.getElementById('tab-' + name);
                if (target) target.style.display = 'block';
                tab.classList.add('nav-tab-active');
            });
        });

        // Save handler
        var btn = document.getElementById('zhuoer-save-btn');
        if (btn) {
            btn.addEventListener('click', function () {
                var form    = document.getElementById('zhuoer-settings-form');
                var fd      = new FormData(form);
                var msg     = document.getElementById('zhuoer-msg');
                fd.append('action', 'zhuoer_save_settings');
                fd.append('nonce',  <?php echo wp_json_encode( $nonce ); ?>);

                msg.style.display = 'none';

                fetch(ajaxurl, { method: 'POST', body: fd })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        msg.style.display = 'block';
                        if (data.success) {
                            msg.style.background = '#d4edda';
                            msg.style.color     = '#155724';
                            msg.textContent      = '\u2713 设置已保存！';
                        } else {
                            msg.style.background = '#f8d7da';
                            msg.style.color     = '#721c24';
                            msg.textContent      = '\u2717 保存失败：' + (data.data || '未知错误');
                        }
                    })
                    .catch(function (e) {
                        msg.style.display = 'block';
                        msg.style.background = '#f8d7da';
                        msg.style.color     = '#721c24';
                        msg.textContent      = '\u2717 请求失败：' + e;
                    });
            });
        }
    })();
    </script>
    <?php
}

/* ── Customizer CSS ── */
add_action( 'wp_head', function () {
    $p = get_option( 'zhuoer_primary_color', '#1a73e8' );
    if ( $p && strtolower( $p ) !== '#1a73e8' ) {
        printf(
            '<style id="zhuoer-customizer-css" type="text/css">:root{--zhuoer-color-link:%1$s;--zhuoer-color-link-hover:#1765d4;--zhuoer-color-accent:%1$s;}</style>',
            esc_attr( $p )
        );
    }
}, 999 );
