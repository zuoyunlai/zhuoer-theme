<form role="search" method="get" class="zhuoer-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label>
        <span class="screen-reader-text"><?php esc_html_e( 'Search', 'zhuoer' ); ?></span>
        <input type="search" name="s" placeholder="<?php esc_attr_e( 'Search', 'zhuoer' ); ?>" required />
    </label>
    <input type="submit" value="<?php esc_attr_e( 'Search', 'zhuoer' ); ?>" />
</form>
