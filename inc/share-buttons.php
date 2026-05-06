<?php
/**
 * Unified Social Share Buttons - Local QR Generation
 * 使用 WooCommerce jquery.qrcode 库
 *
 * @package ZHUOER
 */

defined( 'ABSPATH' ) || exit;

/* ── 1. 注册 QR 库 ─────────────────────────────── */

function zhuoer_share_enqueue_assets() {
    // 使用 WooCommerce 的 qrcode 库（如果存在）
    if ( function_exists( 'WC' ) ) {
        wp_enqueue_script( 'jquery-qrcode', WC()->plugin_url() . '/assets/js/jquery-qrcode/jquery.qrcode.min.js', array( 'jquery' ), WC_VERSION, true );
    }

    wp_enqueue_style(
        'zhuoer-share-buttons',
        get_template_directory_uri() . '/assets/css/share-buttons.css',
        array( 'zhuoer-style' ),
        ZHUOER_VERSION
    );
}
add_action( 'wp_enqueue_scripts', 'zhuoer_share_enqueue_assets', 20 );

/* ── 2. 分享弹窗 HTML + JS（事件委托，无内联 onclick） ────────────── */

function zhuoer_share_footer() {
    ?>
    <div class="zhuoer-share-overlay" id="zhuoer-share-overlay" data-share-action="close-overlay">
        <div class="zhuoer-share-modal" data-share-action="stop-propagation">
            <div class="zhuoer-share-modal__title" id="zhuoer-share-title"><?php esc_html_e( '分享', 'zhuoer' ); ?></div>
            <div class="zhuoer-share-modal__grid">
                <button class="zhuoer-share-modal__btn" data-share-action="platform" data-platform="wechat"><span class="zhuoer-share-modal__btn-icon">💬</span><span class="zhuoer-share-modal__btn-label"><?php esc_html_e( '微信', 'zhuoer' ); ?></span></button>
                <button class="zhuoer-share-modal__btn" data-share-action="platform" data-platform="weibo"><span class="zhuoer-share-modal__btn-icon">🔴</span><span class="zhuoer-share-modal__btn-label"><?php esc_html_e( '微博', 'zhuoer' ); ?></span></button>
                <button class="zhuoer-share-modal__btn" data-share-action="platform" data-platform="qq"><span class="zhuoer-share-modal__btn-icon">🐧</span><span>QQ</span></button>
                <button class="zhuoer-share-modal__btn" data-share-action="platform" data-platform="facebook"><span class="zhuoer-share-modal__btn-icon">📘</span><span class="zhuoer-share-modal__btn-label">Facebook</span></button>
                <button class="zhuoer-share-modal__btn" data-share-action="platform" data-platform="x"><span class="zhuoer-share-modal__btn-icon">𝕏</span><span class="zhuoer-share-modal__btn-label">X</span></button>
                <button class="zhuoer-share-modal__btn" data-share-action="platform" data-platform="pinterest"><span class="zhuoer-share-modal__btn-icon">📌</span><span class="zhuoer-share-modal__btn-label">Pinterest</span></button>
                <button class="zhuoer-share-modal__btn" data-share-action="platform" data-platform="linkedin"><span class="zhuoer-share-modal__btn-icon">💼</span><span class="zhuoer-share-modal__btn-label">LinkedIn</span></button>
                <button class="zhuoer-share-modal__btn" data-share-action="copy"><span class="zhuoer-share-modal__btn-icon">📋</span><span class="zhuoer-share-modal__btn-label"><?php esc_html_e( '复制链接', 'zhuoer' ); ?></span></button>
            </div>
            <button class="zhuoer-share-modal__close" data-share-action="close"><?php esc_html_e( '关闭', 'zhuoer' ); ?></button>
        </div>
    </div>
    <div class="zhuoer-qr-overlay" id="zhuoer-qr-overlay" data-share-action="close-qr-overlay">
        <div class="zhuoer-qr-modal" data-share-action="stop-propagation">
            <h4><?php esc_html_e( '扫码分享到微信', 'zhuoer' ); ?></h4>
            <div id="zhuoer-qr-container"></div>
            <p><?php esc_html_e( '打开微信，扫一扫', 'zhuoer' ); ?></p>
        </div>
    </div>
    <script>
(function(){
    'use strict';
    if ( window.zhuoerShare ) return;

    var shareData = { title: '', url: '' };
    var copiedMsg = <?php echo wp_json_encode( __( '链接已复制', 'zhuoer' ) ); ?>;
    var shareTitlePrefix = <?php echo wp_json_encode( __( '分享：', 'zhuoer' ) ); ?>;

    function openModal( title, url ) {
        shareData.title = title;
        shareData.url   = url;
        document.getElementById('zhuoer-share-title').textContent = shareTitlePrefix + ' ' + title;
        document.getElementById('zhuoer-share-overlay').classList.add('is-active');
    }

    function closeModal() {
        document.getElementById('zhuoer-share-overlay').classList.remove('is-active');
    }

    function closeQR() {
        document.getElementById('zhuoer-qr-overlay').classList.remove('is-active');
    }

    function copyLink() {
        if ( navigator.clipboard ) {
            navigator.clipboard.writeText( shareData.url ).then( function() { alert( copiedMsg ); } );
        } else {
            var i = document.createElement('input');
            i.value = shareData.url;
            i.style.opacity = '0';
            document.body.appendChild( i );
            i.select();
            document.execCommand('copy');
            document.body.removeChild( i );
            alert( copiedMsg );
        }
        closeModal();
    }

    function openPlatform( platform ) {
        var url   = encodeURIComponent( shareData.url );
        var title = encodeURIComponent( shareData.title );
        var dest  = '';

        if ( platform === 'wechat' ) {
            var container = document.getElementById('zhuoer-qr-container');
            container.innerHTML = '';
            if ( typeof jQuery !== 'undefined' && jQuery.fn.qrcode ) {
                jQuery( container ).qrcode({ text: shareData.url, width: 200, height: 200 });
            } else {
                var canvas = document.createElement('canvas');
                canvas.width = 200;
                canvas.height = 200;
                zhuoerSimpleQR( canvas, shareData.url );
                container.appendChild( canvas );
            }
            document.getElementById('zhuoer-qr-overlay').classList.add('is-active');
            closeModal();
            return;
        } else if ( platform === 'weibo' ) {
            dest = 'https://service.weibo.com/share/share.php?url=' + url + '&title=' + title;
        } else if ( platform === 'qq' ) {
            dest = 'https://connect.qq.com/widget/shareqq/index.html?url=' + url + '&title=' + title + '&source=' + title;
        } else if ( platform === 'facebook' ) {
            dest = 'https://www.facebook.com/sharer/sharer.php?u=' + url;
        } else if ( platform === 'x' ) {
            dest = 'https://x.com/intent/tweet?url=' + url + '&text=' + title;
        } else if ( platform === 'pinterest' ) {
            dest = 'https://pinterest.com/pin/create/button/?url=' + url + '&description=' + title;
        } else if ( platform === 'linkedin' ) {
            dest = 'https://www.linkedin.com/sharing/share-offsite/?url=' + url;
        }

        if ( dest ) {
            window.open( dest, '_blank', 'width=600,height=500' );
        }
        closeModal();
    }

    // Event delegation for all share buttons
    document.addEventListener('click', function( e ) {
        var btn = e.target.closest('[data-share-action]');
        if ( ! btn ) return;

        var action = btn.getAttribute('data-share-action');

        if ( action === 'open' ) {
            e.preventDefault();
            var title = btn.getAttribute('data-share-title') || document.title;
            var url   = btn.getAttribute('data-share-url')   || location.href;
            openModal( title, url );
            return;
        }

        if ( action === 'platform' || action === 'direct-platform' ) {
            e.preventDefault();
            var platform = btn.getAttribute('data-platform');
            var title = btn.getAttribute('data-share-title') || document.title;
            var url   = btn.getAttribute('data-share-url') || location.href;
            shareData.title = title;
            shareData.url = url;
            if ( platform ) openPlatform( platform );
            return;
        }

        if ( action === 'copy' ) {
            e.preventDefault();
            copyLink();
            return;
        }

        if ( action === 'close' ) {
            e.preventDefault();
            closeModal();
            return;
        }

        if ( action === 'close-overlay' ) {
            closeModal();
            return;
        }

        if ( action === 'close-qr-overlay' ) {
            closeQR();
            return;
        }

        if ( action === 'stop-propagation' ) {
            e.stopPropagation();
        }
    });

    window.zhuoerShare = {
        open: openModal,
        close: closeModal,
        closeQR: closeQR
    };

    // 简化 QR fallback（仅当 jQuery.qrcode 不可用）
    function zhuoerSimpleQR( canvas, text ) {
        var ctx = canvas.getContext('2d'), size = canvas.width;
        var len = text.length, mod = Math.ceil( Math.sqrt( len * 8 + 20 ) );
        var cell = size / mod, arr = [];
        for ( var i = 0; i < mod; i++ ) {
            arr[i] = [];
            for ( var j = 0; j < mod; j++ ) arr[i][j] = false;
        }
        for ( var k = 0; k < 7; k++ ) {
            arr[0][k] = arr[k][0] = arr[mod - 1][k] = arr[k][mod - 1] = arr[mod - 8][k] = arr[k][mod - 8] = true;
        }
        for ( var r = 2; r < 5; r++ ) {
            for ( var c = 2; c < 5; c++ ) arr[r][c] = true;
        }
        arr[mod - 7][mod - 8] = true;
        var bits = [];
        for ( var i = 0; i < text.length; i++ ) {
            var b = text.charCodeAt( i );
            for ( var j = 7; j >= 0; j-- ) bits.push( ( b >> j ) & 1 );
        }
        var idx = 0;
        for ( var col = mod - 1; col > 0; col -= 2 ) {
            if ( col === 6 ) col = 5;
            for ( var row = 0; row < mod; row++ ) {
                for ( var c = 0; c < 2; c++ ) {
                    if ( idx < bits.length && ! arr[row][col - c] ) {
                        arr[row][col - c] = bits[idx++] === 1;
                    }
                }
            }
        }
        ctx.fillStyle = '#fff';
        ctx.fillRect( 0, 0, size, size );
        ctx.fillStyle = '#000';
        for ( var r = 0; r < mod; r++ ) {
            for ( var c = 0; c < mod; c++ ) {
                if ( arr[r][c] ) ctx.fillRect( c * cell, r * cell, cell, cell );
            }
        }
    }
})();
    </script>
    <?php
}
add_action( 'wp_footer', 'zhuoer_share_footer', 100 );

/**
 * 单个分享按钮（触发弹窗）
 * 使用 data-* 属性 + 事件委托，无内联 onclick
 */
function zhuoer_share_button( $url = '', $title = '', $label = '', $class = null, $echo = true ) {
    if ( ! $url )   $url   = get_permalink();
    if ( ! $title ) $title = get_the_title();
    if ( ! $label ) $label = __( '分享', 'zhuoer' );

    $extra  = $class ? ' ' . esc_attr( $class ) : '';
    $html   = '<button type="button" class="zhuoer-share-trigger' . $extra . '"'
            . ' data-share-action="open"'
            . ' data-share-title="' . esc_attr( $title ) . '"'
            . ' data-share-url="'   . esc_url( $url )   . '"'
            . ' aria-label="'       . esc_attr( $label ) . '">'
            . esc_html( $label )
            . '</button>';

    if ( $echo ) {
        echo $html;
    }
    return $html;
}

/**
 * 行内分享按钮组（微信/微博/QQ）
 * 使用 data-* 属性 + 事件委托，无内联 onclick
 */
function zhuoer_share_buttons( $context = null, $label = '', $echo = true ) {
    if ( is_a( $context, 'WC_Product' ) ) {
        $share_url   = get_permalink( $context->get_id() );
        $share_title = $context->get_name();
    } else {
        $share_url   = get_permalink();
        $share_title = get_the_title();
    }
    if ( ! $label ) {
        $label = __( '分享', 'zhuoer' );
    }

    $output  = '<div class="zhuoer-share">';
    if ( $label ) {
        $output .= '<span class="zhuoer-share__label">' . esc_html( $label ) . '</span>';
    }

    $platforms = array(
        'wechat' => __( '微信', 'zhuoer' ),
        'weibo'  => __( '微博', 'zhuoer' ),
        'qq'     => 'QQ',
        'facebook' => 'Facebook',
        'x'        => 'X',
        'pinterest' => 'Pinterest',
        'linkedin' => 'LinkedIn',
    );

    // SVG icons for each platform
    $icons = array(
        'wechat' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8.691 2.188C3.891 2.188 0 5.476 0 9.53c0 2.212 1.17 4.203 3.002 5.55a.59.59 0 0 1 .213.665l-.39 1.48c-.019.07-.048.141-.048.213 0 .163.13.295.29.295a.326.326 0 0 0 .167-.054l1.903-1.114a.864.864 0 0 1 .717-.098 10.2 10.2 0 0 0 2.837.403c.276 0 .543-.027.811-.05-.857-2.578.157-4.972 1.932-6.446 1.703-1.415 3.882-1.98 5.853-1.838-.576-3.583-4.196-6.348-8.596-6.348zM5.785 5.991c.642 0 1.162.529 1.162 1.18a1.17 1.17 0 0 1-1.162 1.178A1.17 1.17 0 0 1 4.623 7.17c0-.651.52-1.18 1.162-1.18zm5.813 0c.642 0 1.162.529 1.162 1.18a1.17 1.17 0 0 1-1.162 1.178 1.17 1.17 0 0 1-1.162-1.178c0-.651.52-1.18 1.162-1.18z"/><path d="M23.992 14.387c0-3.546-3.267-6.424-7.294-6.424-4.028 0-7.295 2.878-7.295 6.424 0 2.01 1.042 3.818 2.69 5.023a.64.64 0 0 1 .228.57l-.35 1.326c-.017.063-.043.127-.043.191 0 .146.117.264.26.264a.29.29 0 0 0 .149-.048l1.708-1a.775.775 0 0 1 .643-.088 9.11 9.11 0 0 0 2.55.36c.248 0 .488-.024.73-.044-.77-2.305.14-4.447 1.727-5.764 1.524-1.263 3.476-1.768 5.237-1.642zm-9.924-2.194c.575 0 1.04.474 1.04 1.058a1.05 1.05 0 0 1-1.04 1.057 1.05 1.05 0 0 1-1.04-1.057c0-.584.465-1.058 1.04-1.058zm5.2 0c.575 0 1.04.474 1.04 1.058a1.05 1.05 0 0 1-1.04 1.057 1.05 1.05 0 0 1-1.04-1.057c0-.584.465-1.058 1.04-1.058z"/></svg>',
        'weibo' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M10.098 20.323c-3.977.391-7.414-1.406-7.672-4.02-.259-2.609 2.759-5.047 6.74-5.441 3.979-.394 7.413 1.404 7.671 4.018.259 2.6-2.759 5.049-6.739 5.443zM9.05 17.219c-.384.616-1.208.884-1.829.602-.612-.279-.793-.991-.406-1.593.379-.595 1.176-.861 1.793-.601.622.263.82.972.442 1.592zm1.27-1.627c-.141.237-.449.353-.689.253-.236-.09-.313-.361-.177-.586.138-.227.436-.346.672-.24.239.09.315.36.194.573zm.176-2.719c-1.893-.493-4.033.45-4.857 2.118-.836 1.704-.026 3.591 1.886 4.21 1.983.64 4.318-.341 5.132-2.179.8-1.793-.201-3.642-2.161-4.149zm7.563-1.224c-.346-.105-.578-.18-.405-.649.388-1.068.425-1.982.003-2.613-.793-1.185-2.933-1.115-5.369-.034 0 0-.773.337-.576-.274.381-1.219.326-2.216-.267-2.786-1.353-1.297-4.573.05-7.219 3.008C1.388 10.684 0 13.241 0 15.28 0 18.733 3.597 21.473 8.223 21.473c5.53 0 9.277-3.311 9.277-5.926 0-1.527-1.259-2.457-2.441-2.838zm2.903-4.547c-.535-.677-1.243-.866-1.769-.424-.523.438-.387 1.256.322 1.962.712.709 1.63.914 2.161.464.533-.441.395-1.271-.714-2.002zm2.188-2.377c-1.069-1.354-2.483-1.732-3.534-.854-1.046.873-.773 2.506.652 3.913 1.427 1.408 3.318 1.803 4.367.928 1.053-.875.793-2.63-.685-3.987z"/></svg>',
        'qq' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.003 2c-2.265 0-6.29 1.364-6.29 7.325v1.195S3.55 14.96 3.55 17.474c0 .665.17 1.025.28 1.025.112 0 .614-.182.614-.182s.086.477.478 1.096c.235.376.596.668.596.668s-.247.453-.247 1.096c0 .74.904 1.026 1.83 1.026 1.12 0 2.063-.352 2.063-.352s.59.392 2.836.47c2.246.078 2.866-.468 2.866-.468s.946.352 2.063.352c.927 0 1.83-.286 1.83-1.026 0-.643-.247-1.096-.247-1.096s.36-.292.596-.668c.392-.619.478-1.096.478-1.096s.502.182.614.182c.11 0 .28-.36.28-1.025 0-2.514-2.166-6.954-2.166-6.954V9.325C18.29 3.364 14.268 2 12.003 2zm1.566 4.976c.516 0 .935.423.935.945a.939.939 0 0 1-.935.946.938.938 0 0 1-.935-.946c0-.522.42-.945.935-.945zm-3.13 0c.516 0 .935.423.935.945a.939.939 0 0 1-.935.946.938.938 0 0 1-.934-.946c0-.522.42-.945.934-.945z"/></svg>',
        'x' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.683l7.797-9.421-8.097-10.079H7.89l4.702 6.233zM17.114 20.19h1.75L6.77 3.89H4.92z"/></svg>',
        'facebook' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M15.997 9.798h-2.499a.31.31 0 0 0-.309.31v2.488h2.798l-.499 2.99h-2.299v8.969H10.39V15.58h-1.998V12.59h1.998v-2.48c0-1.757.799-3.516 3.197-3.516 1.599 0 2.798.399 2.798.399l-.001 2.799z"/></svg>',
        'x' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.683l7.797-9.421-8.097-10.079H7.89l4.702 6.233zM17.114 20.19h1.75L6.77 3.89H4.92z"/></svg>',
        'pinterest' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.72.72 0 0 1 .166.694c-.06.252-.195.796-.222.907-.035.146-.116.177-.268.107-1-.465-1.624-1.926-1.624-3.1 0-2.523 1.834-4.84 5.286-4.84 2.775 0 4.932 1.968 4.932 4.62 0 2.757-1.739 4.976-4.151 4.976-.81 0-1.573-.421-1.834-.92l-.498 1.903c-.18.69-.669 1.597-.996 2.141.746.23 1.531.355 2.356.355 6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z"/></svg>',
        'linkedin' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
    );

    foreach ( $platforms as $key => $plat_label ) {
        $icon = isset($icons[$key]) ? $icons[$key] : esc_html($plat_label);
        $output .= '<button type="button"'
                 . ' class="zhuoer-share__btn zhuoer-share__btn--' . esc_attr( $key ) . '"'
                 . ' data-share-action="direct-platform"'
                 . ' data-platform="'    . esc_attr( $key ) . '"'
                 . ' data-share-title="' . esc_attr( $share_title ) . '"'
                 . ' data-share-url="'   . esc_url( $share_url ) . '"'
                 . ' title="'           . esc_attr( $plat_label ) . '">'
                 . $icon
                 . '</button>';
    }

    $output .= '</div>';



    if ( $echo ) {
        echo $output;
    }
    return $output;
}
