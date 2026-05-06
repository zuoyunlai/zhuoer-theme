<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ── Admin Menu ── */
add_action( 'admin_menu', function () {
    add_theme_page(
        __( 'ZHUOER 主题设置', 'zhuoer' ),
        __( 'ZHUOER 主题设置', 'zhuoer' ),
        'manage_options',
        'zhuoer-settings',
        'zhuoer_settings_page_html'
    );
} );

/* ── Register all settings ── */
add_action( 'admin_init', function () {
    $opts = array(
        'zhuoer_home_title', 'zhuoer_home_description', 'zhuoer_og_image',
        'zhuoer_baidu_verify', 'zhuoer_baidu_tongji_id', 'zhuoer_ga_id', 'zhuoer_icp_number',
        'zhuoer_primary_color', 'zhuoer_hero_title', 'zhuoer_hero_subtitle',
        'zhuoer_cta_text', 'zhuoer_cta_url',
        'zhuoer_show_featured_image', 'zhuoer_show_author_bio',
        'zhuoer_show_sidebar_archive', 'zhuoer_show_sidebar_home', 'zhuoer_show_sidebar_single',
        'zhuoer_hero_slides',
    );
    // Text fields — sanitize_text_field
    $text_opts = array(
        'zhuoer_home_title', 'zhuoer_home_description', 'zhuoer_hero_title', 'zhuoer_hero_subtitle',
        'zhuoer_cta_text', 'zhuoer_primary_color',
    );
    foreach ( $text_opts as $opt ) {
        register_setting( 'zhuoer_group', $opt, 'sanitize_text_field' );
    }
    // URL fields — esc_url_raw
    $url_opts = array( 'zhuoer_og_image', 'zhuoer_cta_url' );
    foreach ( $url_opts as $opt ) {
        register_setting( 'zhuoer_group', $opt, 'esc_url_raw' );
    }
    // Code / ID fields — strip to safe chars
    $code_opts = array( 'zhuoer_baidu_verify', 'zhuoer_baidu_tongji_id', 'zhuoer_ga_id', 'zhuoer_icp_number' );
    foreach ( $code_opts as $opt ) {
        register_setting( 'zhuoer_group', $opt, function( $val ){
            return sanitize_text_field( preg_replace( '/[^a-zA-Z0-9_\-\.]/', '', (string) $val ) );
        } );
    }
    // Checkbox fields — 0/1 integer
    $checkbox_opts = array(
        'zhuoer_show_featured_image', 'zhuoer_show_author_bio',
        'zhuoer_show_sidebar_archive', 'zhuoer_show_sidebar_home', 'zhuoer_show_sidebar_single',
    );
    foreach ( $checkbox_opts as $opt ) {
        register_setting( 'zhuoer_group', $opt, 'absint' );
    }
    // Hero slides stored via AJAX, skip default register_setting
    // Social URL fields
    $social = array( 'weibo', 'zhihu', 'bilibili', 'xiaohongshu', 'github', 'twitter', 'facebook', 'instagram', 'linkedin' );
    foreach ( $social as $s ) {
        register_setting( 'zhuoer_group', "zhuoer_social_{$s}", 'esc_url_raw' );
    }
} );

/* ── AJAX save handler ── */
add_action( 'wp_ajax_zhuoer_save_settings', function () {
    check_ajax_referer( 'zhuoer_save_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( __( '无权限', 'zhuoer' ) );
    }

    $opts = array(
        'zhuoer_home_title', 'zhuoer_home_description', 'zhuoer_og_image',
        'zhuoer_baidu_verify', 'zhuoer_baidu_tongji_id', 'zhuoer_ga_id', 'zhuoer_icp_number',
        'zhuoer_primary_color', 'zhuoer_hero_title', 'zhuoer_hero_subtitle',
        'zhuoer_cta_text', 'zhuoer_cta_url',
        'zhuoer_show_featured_image', 'zhuoer_show_author_bio',
        'zhuoer_show_sidebar_archive', 'zhuoer_show_sidebar_home', 'zhuoer_show_sidebar_single',
        'zhuoer_hero_slides',
    );

    // Define field categories for proper sanitization in AJAX handler
    $ajax_url_opts = array( 'zhuoer_og_image', 'zhuoer_cta_url' );
    $ajax_code_opts = array( 'zhuoer_baidu_verify', 'zhuoer_baidu_tongji_id', 'zhuoer_ga_id', 'zhuoer_icp_number' );
    $ajax_checkbox_opts = array(
        'zhuoer_show_featured_image', 'zhuoer_show_author_bio',
        'zhuoer_show_sidebar_archive', 'zhuoer_show_sidebar_home', 'zhuoer_show_sidebar_single',
    );

    foreach ( $opts as $key ) {
        if ( $key === 'zhuoer_hero_slides' ) {
            // Hero slides — only update if sent; otherwise leave existing value
            if ( isset( $_POST[ $key ] ) ) {
                $raw = json_decode( wp_unslash( $_POST[ $key ] ), true );
                $sanitized = array();
                if ( is_array( $raw ) ) {
                    foreach ( $raw as $slide ) {
                        if ( ! empty( $slide['title'] ) || ! empty( $slide['img'] ) ) {
                            $sanitized[] = array(
                                'title'   => sanitize_text_field( $slide['title'] ?? '' ),
                                'sub'     => sanitize_text_field( $slide['sub'] ?? '' ),
                                'cta'     => sanitize_text_field( $slide['cta'] ?? '' ),
                                'url'     => esc_url_raw( $slide['url'] ?? '' ),
                                'img'     => esc_url_raw( $slide['img'] ?? '' ),
                            );
                        }
                    }
                }
                update_option( 'zhuoer_hero_slides', wp_json_encode( $sanitized ) );
            }
        } elseif ( isset( $_POST[ $key ] ) ) {
            // Field is present in POST — sanitize according to type
            if ( in_array( $key, $ajax_url_opts, true ) ) {
                update_option( $key, esc_url_raw( $_POST[ $key ] ) );
            } elseif ( in_array( $key, $ajax_code_opts, true ) ) {
                update_option( $key, sanitize_text_field( preg_replace( '/[^a-zA-Z0-9_\-\.]/', '', (string) $_POST[ $key ] ) ) );
            } elseif ( in_array( $key, $ajax_checkbox_opts, true ) ) {
                update_option( $key, absint( $_POST[ $key ] ) );
            } else {
                update_option( $key, sanitize_text_field( $_POST[ $key ] ) );
            }
        } elseif ( in_array( $key, $ajax_checkbox_opts, true ) ) {
            // Checkbox not in POST → user unchecked it → set to 0
            update_option( $key, '0' );
        }
        // Text/URL/code fields not in POST → skip (leave existing DB value untouched)
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

    wp_send_json_success( __( '设置已保存！', 'zhuoer' ) );
} );

/* ── Settings Page HTML ── */
function zhuoer_settings_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    wp_enqueue_media();  // Load media uploader JS

    $nonce = wp_create_nonce( 'zhuoer_save_nonce' );

    $social_labels = array(
        'weibo'        => __( '微博', 'zhuoer' ),
        'zhihu'        => __( '知乎', 'zhuoer' ),
        'bilibili'     => __( 'B站', 'zhuoer' ),
        'xiaohongshu'  => __( '小红书', 'zhuoer' ),
        'github'       => 'GitHub',
        'twitter'      => 'Twitter / X',
        'facebook'     => 'Facebook',
        'instagram'    => 'Instagram',
        'linkedin'     => 'LinkedIn',
    );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'ZHUOER 主题设置', 'zhuoer' ); ?></h1>
        <p id="zhuoer-msg" style="display:none;padding:10px 15px;border-radius:4px;margin-bottom:15px;"></p>

        <h2 class="nav-tab-wrapper">
            <a href="#" class="nav-tab nav-tab-active" data-tab="seo"><?php esc_html_e( 'SEO 与统计', 'zhuoer' ); ?></a>
            <a href="#" class="nav-tab" data-tab="homepage"><?php esc_html_e( '首页设置', 'zhuoer' ); ?></a>
            <a href="#" class="nav-tab" data-tab="social"><?php esc_html_e( '社交链接', 'zhuoer' ); ?></a>
            <a href="#" class="nav-tab" data-tab="posts"><?php esc_html_e( '文章显示', 'zhuoer' ); ?></a>
            <a href="#" class="nav-tab" data-tab="layout"><?php esc_html_e( '布局设置', 'zhuoer' ); ?></a>
        </h2>

        <form id="zhuoer-settings-form" onsubmit="return false;">

            <!-- SEO -->
            <div id="tab-seo" class="tab-content">
                <h2><?php esc_html_e( 'SEO 与统计分析', 'zhuoer' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( '首页标题', 'zhuoer' ); ?></th>
                        <td>
                            <input type="text" name="zhuoer_home_title" value="<?php echo esc_attr( get_option( 'zhuoer_home_title', '' ) ); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e( '留空则使用网站名称', 'zhuoer' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( '首页描述', 'zhuoer' ); ?></th>
                        <td>
                            <textarea name="zhuoer_home_description" rows="3" class="regular-text"><?php echo esc_textarea( get_option( 'zhuoer_home_description', '' ) ); ?></textarea>
                            <p class="description"><?php esc_html_e( '建议 120–150 个字符', 'zhuoer' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'OG 图片 URL', 'zhuoer' ); ?></th>
                        <td>
                            <input type="url" name="zhuoer_og_image" value="<?php echo esc_attr( get_option( 'zhuoer_og_image', '' ) ); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e( '微信 / 微博分享图，建议尺寸 1200×630px', 'zhuoer' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( '百度统计 ID', 'zhuoer' ); ?></th>
                        <td>
                            <input type="text" name="zhuoer_baidu_tongji_id" value="<?php echo esc_attr( get_option( 'zhuoer_baidu_tongji_id', '' ) ); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e( '在百度统计后台获取', 'zhuoer' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( '百度站长验证', 'zhuoer' ); ?></th>
                        <td>
                            <input type="text" name="zhuoer_baidu_verify" value="<?php echo esc_attr( get_option( 'zhuoer_baidu_verify', '' ) ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'ICP备案号', 'zhuoer' ); ?></th>
                        <td>
                            <input type="text" name="zhuoer_icp_number" value="<?php echo esc_attr( get_option( 'zhuoer_icp_number', '' ) ); ?>" class="regular-text" placeholder="<?php esc_attr_e( '如：京ICP备XXXXXXXX号', 'zhuoer' ); ?>" />
                            <p class="description"><?php esc_html_e( '将显示在网站底部正中间', 'zhuoer' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Google Analytics ID', 'zhuoer' ); ?></th>
                        <td>
                            <input type="text" name="zhuoer_ga_id" value="<?php echo esc_attr( get_option( 'zhuoer_ga_id', '' ) ); ?>" class="regular-text" placeholder="G-XXXXXXXXXX" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( '主题色调', 'zhuoer' ); ?></th>
                        <td>
                            <input type="color" name="zhuoer_primary_color" value="<?php echo esc_attr( get_option( 'zhuoer_primary_color', '#1a73e8' ) ); ?>" />
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Homepage / Hero Slider -->
            <div id="tab-homepage" class="tab-content" style="display:none;">
                <h2><?php esc_html_e( '首页 Hero 幻灯片', 'zhuoer' ); ?></h2>
                <p style="color:#666;font-size:13px;"><?php esc_html_e( '支持多张幻灯片轮播，每张包含：标题、副标题、按钮（可选）、右侧配图。建议图片比例 4:3 或 1:1。', 'zhuoer' ); ?></p>
                <div id="hero-slides-wrapper">
                    <!-- Slides rendered by JS -->
                </div>
                <p style="margin-top:8px;">
                    <button type="button" id="add-hero-slide" class="button button-secondary" style="display:none;"><?php esc_html_e( '+ 添加幻灯片（最多5张）', 'zhuoer' ); ?></button>
                </p>
                <input type="hidden" name="zhuoer_hero_slides" id="zhuoer_hero_slides" value="<?php echo esc_attr( get_option( 'zhuoer_hero_slides', '[]' ) ); ?>" />
                <hr style="margin:24px 0;" />
                <h3 style="font-size:14px;color:#555;"><?php esc_html_e( '单张模式（旧版，幻灯片为空时启用）', 'zhuoer' ); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Hero 标题', 'zhuoer' ); ?></th>
                        <td><input type="text" name="zhuoer_hero_title" value="<?php echo esc_attr( get_option( 'zhuoer_hero_title', '' ) ); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Hero 副标题', 'zhuoer' ); ?></th>
                        <td><textarea name="zhuoer_hero_subtitle" rows="3" class="regular-text"><?php echo esc_textarea( get_option( 'zhuoer_hero_subtitle', '' ) ); ?></textarea></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'CTA 按钮文字', 'zhuoer' ); ?></th>
                        <td><input type="text" name="zhuoer_cta_text" value="<?php echo esc_attr( get_option( 'zhuoer_cta_text', '' ) ); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'CTA 按钮链接', 'zhuoer' ); ?></th>
                        <td><input type="url" name="zhuoer_cta_url" value="<?php echo esc_attr( get_option( 'zhuoer_cta_url', '' ) ); ?>" class="regular-text" /></td>
                    </tr>
                </table>
            </div>

            <!-- Social -->
            <div id="tab-social" class="tab-content" style="display:none;">
                <h2><?php esc_html_e( '社交媒体链接', 'zhuoer' ); ?></h2>
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
                <h2><?php esc_html_e( '文章显示设置', 'zhuoer' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( '显示选项', 'zhuoer' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="zhuoer_show_featured_image" value="1"
                                    <?php checked( get_option( 'zhuoer_show_featured_image', '1' ), '1' ); ?> />
                                <?php esc_html_e( '文章详情页显示特色图片', 'zhuoer' ); ?>
                            </label>
                            <br/><br/>
                            <label>
                                <input type="checkbox" name="zhuoer_show_author_bio" value="1"
                                    <?php checked( get_option( 'zhuoer_show_author_bio', '1' ), '1' ); ?> />
                                <?php esc_html_e( '文章底部显示作者介绍', 'zhuoer' ); ?>
                            </label>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Layout -->
            <div id="tab-layout" class="tab-content" style="display:none;">
                <h2><?php esc_html_e( '侧边栏可见性', 'zhuoer' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( '显示侧边栏', 'zhuoer' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="zhuoer_show_sidebar_archive" value="1"
                                    <?php checked( get_option( 'zhuoer_show_sidebar_archive', '1' ), '1' ); ?> />
                                <?php esc_html_e( '分类 / 标签 / 归档列表页（默认开启）', 'zhuoer' ); ?>
                            </label>
                            <br/><br/>
                            <label>
                                <input type="checkbox" name="zhuoer_show_sidebar_home" value="1"
                                    <?php checked( get_option( 'zhuoer_show_sidebar_home', '1' ), '1' ); ?> />
                                <?php esc_html_e( '首页（默认开启）', 'zhuoer' ); ?>
                            </label>
                            <br/><br/>
                            <label>
                                <input type="checkbox" name="zhuoer_show_sidebar_single" value="1"
                                    <?php checked( get_option( 'zhuoer_show_sidebar_single', '0' ), '1' ); ?> />
                                <?php esc_html_e( '文章详情页（默认关闭）', 'zhuoer' ); ?>
                            </label>
                        </td>
                    </tr>
                </table>
            </div>

            <p class="submit">
                <button type="button" class="button button-primary" id="zhuoer-save-btn">
                    <?php esc_html_e( '保存设置', 'zhuoer' ); ?>
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
                            msg.textContent      = <?php echo wp_json_encode( __( '✓ 设置已保存！', 'zhuoer' ) ); ?>;
                        } else {
                            msg.style.background = '#f8d7da';
                            msg.style.color     = '#721c24';
                            msg.textContent      = <?php echo wp_json_encode( __( '✗ 保存失败：', 'zhuoer' ) ); ?> + (data.data || <?php echo wp_json_encode( __( '未知错误', 'zhuoer' ) ); ?>);
                        }
                    })
                    .catch(function (e) {
                        msg.style.display = 'block';
                        msg.style.background = '#f8d7da';
                        msg.style.color     = '#721c24';
                        msg.textContent      = <?php echo wp_json_encode( __( '✗ 请求失败：', 'zhuoer' ) ); ?> + e;
                    });
            });
        }
    })();
    </script>
    <?php
    // ── Hero Slides Admin JS ──
    $hero_slides_json = get_option( 'zhuoer_hero_slides', '[]' );
    ?>
    <script>
    (function(){
        var slides = <?php echo wp_kses_post( $hero_slides_json ); ?> || [];
        var wrap   = document.getElementById('hero-slides-wrapper');
        var input  = document.getElementById('zhuoer_hero_slides');
        var addBtn = document.getElementById('add-hero-slide');

        function render() {
            wrap.innerHTML = '';
            slides.forEach(function(s, i){
                var div = document.createElement('div');
                div.className = 'hero-slide-item';
                div.innerHTML = '<div class="hero-slide-num">' + <?php echo wp_json_encode( __( '幻灯片 ', 'zhuoer' ) ); ?> + (i+1) + '</div>' +
                    '<table class="form-table" style="margin-bottom:8px;">' +
                    '<tr><th>' + <?php echo wp_json_encode( __( '标题', 'zhuoer' ) ); ?> + '</th><td><input type="text" class="regular-text slide-title" value="' + escAttr(s.title||'') + '" placeholder="' + <?php echo wp_json_encode( __( '主标题', 'zhuoer' ) ); ?> + '" /></td></tr>' +
                    '<tr><th>' + <?php echo wp_json_encode( __( '副标题', 'zhuoer' ) ); ?> + '</th><td><input type="text" class="regular-text slide-sub" value="' + escAttr(s.sub||'') + '" placeholder="' + <?php echo wp_json_encode( __( '副标题（可选）', 'zhuoer' ) ); ?> + '" /></td></tr>' +
                    '<tr><th>' + <?php echo wp_json_encode( __( '按钮文字', 'zhuoer' ) ); ?> + '</th><td><input type="text" class="regular-text slide-cta" value="' + escAttr(s.cta||'') + '" placeholder="' + <?php echo wp_json_encode( __( '如：了解更多（可选）', 'zhuoer' ) ); ?> + '" /></td></tr>' +
                    '<tr><th>' + <?php echo wp_json_encode( __( '按钮链接', 'zhuoer' ) ); ?> + '</th><td><input type="url" class="regular-text slide-url" value="' + escAttr(s.url||'') + '" placeholder="https://" /></td></tr>' +
                    '<tr><th>' + <?php echo wp_json_encode( __( '配图URL', 'zhuoer' ) ); ?> + '</th><td>' + imgField(i, s.img) + '</td></tr>' +
                    '</table>' +
                    '<button type="button" class="button button-secondary slide-del" data-i="' + i + '">' + <?php echo wp_json_encode( __( '删除', 'zhuoer' ) ); ?> + '</button>' +
                    '<hr />';
                wrap.appendChild(div);
            });
            addBtn.style.display = slides.length < 5 ? 'inline-block' : 'none';
        }

        function imgField(i, val) {
            return '<div style="display:flex;gap:8px;align-items:center;">' +
                '<input type="url" class="regular-text slide-img" data-i="' + i + '" value="' + escAttr(val||'') + '" placeholder="' + <?php echo wp_json_encode( __( 'https://...（建议4:3图片）', 'zhuoer' ) ); ?> + '" style="flex:1;" />' +
                '<button type="button" class="button upload-img-btn" data-i="' + i + '">' + <?php echo wp_json_encode( __( '上传', 'zhuoer' ) ); ?> + '</button>' +
                (val ? '<img src="' + escAttr(val) + '" alt="' + <?php echo wp_json_encode( __( '幻灯片', 'zhuoer' ) ); ?> + (i+1) + <?php echo wp_json_encode( __( '预览', 'zhuoer' ) ); ?> + '" style="height:40px;border-radius:4px;border:1px solid #ccc;" />' : '') +
                '</div>';
        }

        function escAttr(s){ return s.replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

        function save() {
            var items = [];
            document.querySelectorAll('.hero-slide-item').forEach(function(div){
                items.push({
                    title: div.querySelector('.slide-title').value,
                    sub:   div.querySelector('.slide-sub').value,
                    cta:   div.querySelector('.slide-cta').value,
                    url:   div.querySelector('.slide-url').value,
                    img:   div.querySelector('.slide-img').value,
                });
            });
            input.value = JSON.stringify(items);
        }

        addBtn.addEventListener('click', function(){
            if (slides.length >= 5) return;
            slides.push({title:'', sub:'', cta:'', url:'', img:''});
            render();
        });

        wrap.addEventListener('click', function(e){
            if (e.target.classList.contains('slide-del')) {
                var idx = parseInt(e.target.dataset.i);
                slides.splice(idx, 1);
                render();
            }
            if (e.target.classList.contains('upload-img-btn')) {
                var idx = parseInt(e.target.dataset.i);
                var field = document.querySelector('.slide-img[data-i="'+idx+'"]');
                var tb = wp.media({title: <?php echo wp_json_encode( __( '选择配图', 'zhuoer' ) ); ?>,multiple:false});
                tb.on('select', function(){ var att = tb.state().get('selection').first().toJSON(); field.value = att.url; save(); render(); });
                tb.open();
            }
        });

        wrap.addEventListener('input', save);

        render();
    })();
    </script>
    <style>
    .hero-slide-item { background:#f9f9f9; border:1px solid #e0e0e0; border-radius:6px; padding:12px 16px; margin-bottom:12px; }
    .hero-slide-num { font-weight:600; font-size:13px; color:#333; margin-bottom:8px; }
    .slide-del { color:#dc3232 !important; margin-top:4px; }
    </style>
    <?php
}

/* ── Customizer CSS ── */
/**
 * Inject frontend CSS variables derived from zhuoer_primary_color.
 * Delegates all color computation to zhuoer_compute_colors() (functions.php)
 * to keep a single source of truth. Uses a 12h transient cache internally.
 *
 * @since 1.0.0
 */
add_action( 'wp_head', function () {
    $primary = get_option( 'zhuoer_primary_color', '#1a73e8' );
    $hex     = ltrim( $primary, '#' );
    if ( strlen( $hex ) !== 6 ) {
        $primary = '#1a73e8';
        $hex     = '1a73e8';
    }

    $c = zhuoer_compute_colors( $hex );
    if ( ! $c ) return;

    // Light-mode CSS variables
    printf(
        '<style id="zhuoer-customizer-css" type="text/css">
:root{
--zhuoer-color-link:%1$s;
--zhuoer-color-link-hover:%2$s;
--zhuoer-color-accent:%1$s;
--zhuoer-color-primary-bg:%3$s;
--zhuoer-color-primary-border:%4$s;
--zhuoer-color-primary-light:%2$s;
--zhuoer-color-primary-light-bg:%5$s;
--zhuoer-color-primary-light-border:%6$s;
--zhuoer-color-primary-dark-bg:%7$s;
--zhuoer-color-primary-dark-text:%8$s;
--zhuoer-color-primary-dark-border:%9$s;
--zhuoer-color-primary-alpha:%10$s;
--zhuoer-color-primary-alpha-2:%11$s;
--zhuoer-hero-grad-1:%12$s;
--zhuoer-hero-grad-2:%13$s;
--zhuoer-hero-grad-3:%14$s;
--zhuoer-cta-shadow:%15$s;
--zhuoer-cta-shadow-hover:%16$s;
}
</style>',
        esc_attr( $c['link'] ), esc_attr( $c['link-hover'] ), esc_attr( $c['primary-bg'] ), esc_attr( $c['border'] ),
        esc_attr( $c['light-bg'] ), esc_attr( $c['light-border'] ), esc_attr( $c['dark-bg'] ), esc_attr( $c['dark-text'] ),
        esc_attr( $c['dark-border'] ), esc_attr( $c['alpha'] ), esc_attr( $c['alpha-2'] ),
        esc_attr( $c['hero-grad-1'] ), esc_attr( $c['hero-grad-2'] ), esc_attr( $c['hero-grad-3'] ),
        esc_attr( $c['cta-shadow'] ), esc_attr( $c['cta-shadow-hover'] )
    );

    // Dark-mode CSS variables
    printf(
        '<style id="zhuoer-customizer-css-dark" type="text/css">
[data-theme="dark"]{
--zhuoer-color-bg:%1$s;
--zhuoer-color-surface:%2$s;
--zhuoer-color-surface-hover:%3$s;
--zhuoer-color-border:%4$s;
--zhuoer-color-text:%5$s;
--zhuoer-color-text-muted:%6$s;
--zhuoer-color-text-subtle:%7$s;
--zhuoer-color-link:%8$s;
--zhuoer-color-link-hover:%9$s;
--zhuoer-color-accent:%8$s;
--zhuoer-hero-grad-1:%10$s;
--zhuoer-hero-grad-2:%11$s;
--zhuoer-hero-grad-3:%12$s;
}
</style>',
        'hsl(30, 8%, 10%)',   // dark-bg
        'hsl(30, 6%, 14%)',   // dark-surface
        'hsl(30, 6%, 18%)',   // dark-surface-hover
        'hsl(30, 4%, 22%)',   // dark-border
        'hsl(30, 10%, 92%)',  // dark-text
        'hsl(30, 4%, 58%)',   // dark-text-muted
        'hsl(30, 3%, 42%)',   // dark-text-subtle
        esc_attr( $c['link-dark'] ), esc_attr( $c['link-hover-dark'] ),
        esc_attr( $c['hero-grad-dark-1'] ), esc_attr( $c['hero-grad-dark-2'] ), esc_attr( $c['hero-grad-dark-3'] )
    );
}, 999 );
