<?php
/**
 * 导航主页模板
 * Template Name: 导航主页
 * 与 zhuoer 主题风格对齐 + 复用导航模块卡片样式
 */
if (!defined('ABSPATH')) exit;

// Guard: require ZuoAIPlus plugin for NavigationSite meta
if ( ! class_exists( '\\ZuoAIPlus\\Models\\NavigationSite' ) ) {
    get_header();
    echo '<main id="main-content" style="max-width:720px;margin:80px auto;text-align:center;"><h2>导航主页需要 Zuo AI Plus 插件</h2><p>请安装并激活插件后刷新页面。</p></main>';
    get_footer();
    return;
}

$cats = get_terms([
    'taxonomy'   => 'nav_category',
    'hide_empty' => true,
    'orderby'    => 'count',
    'order'      => 'DESC',
]);

$queryArgs = [
    'post_type'      => 'nav_site',
    'post_status'    => 'publish',
    'posts_per_page' => 100,
    'orderby'        => ['meta_value' => 'DESC', 'date' => 'DESC'],
    'meta_key'       => 'nav_views',
];
$query = new \WP_Query($queryArgs);

$sitesByCat = [];
$featured = [];
while ($query->have_posts()) {
    $query->the_post();
    $meta = \ZuoAIPlus\Models\NavigationSite::getMeta(get_the_ID());
    $postCats = get_the_terms(get_the_ID(), 'nav_category');
    $isFeatured = ($meta['status'] === 'featured');

    if ($isFeatured) {
        $featured[] = ['post' => get_post(), 'meta' => $meta];
        continue;
    }

    if ($postCats && !is_wp_error($postCats)) {
        foreach ($postCats as $cat) {
            $sitesByCat[$cat->term_id]['term'] = $cat;
            $sitesByCat[$cat->term_id]['sites'][] = ['post' => get_post(), 'meta' => $meta];
        }
    } else {
        $sitesByCat[0]['term'] = (object)['term_id' => 0, 'name' => '未分类'];
        $sitesByCat[0]['sites'][] = ['post' => get_post(), 'meta' => $meta];
    }
}
wp_reset_postdata();

get_header();
wp_enqueue_style('zuo-nav-css', plugins_url('zuo-ai-plus/Assets/css/nav.v2.css'));
?>

<style>
/* 导航主页布局 — 基于 zhuoer 主题变量 */
#nav-page-wrap {
  max-width: var(--zhuoer-wide-width, 1080px);
  margin: 0 auto;
  padding: 0 var(--zhuoer-spacing-lg, 24px) 60px;
  font-family: var(--zhuoer-font-family, 'Noto Sans SC', -apple-system, BlinkMacSystemFont, sans-serif);
  color: var(--zhuoer-color-text, #202124);
}

/* 顶部搜索区 */
.nav-page-header {
  text-align: center;
  padding: var(--zhuoer-spacing-xl, 40px) 0 var(--zhuoer-spacing-lg, 24px);
}
.nav-page-header h1 {
  font-size: clamp(1.75rem, 5vw, 2.5rem);
  font-weight: 700;
  margin: 0 0 var(--zhuoer-spacing-sm, 8px);
  color: var(--zhuoer-color-text, #202124);
  letter-spacing: -0.02em;
}
.nav-page-header p {
  margin: 0 0 var(--zhuoer-spacing-md, 16px);
  font-size: 0.9375rem;
  color: var(--zhuoer-color-text-muted, #5f6368);
}
.nav-page-search {
  max-width: 520px;
  margin: 0 auto;
}
.nav-page-search input {
  width: 100%;
  padding: 14px 20px;
  font-size: 0.9375rem;
  border: 2px solid var(--zhuoer-color-border, #e8e8e8);
  border-radius: 28px;
  outline: none;
  transition: border-color var(--zhuoer-transition, 0.2s);
  background: var(--zhuoer-color-bg, #fff);
  font-family: inherit;
  color: var(--zhuoer-color-text, #202124);
}
.nav-page-search input:focus {
  border-color: var(--zhuoer-color-accent, #1a73e8);
  box-shadow: 0 0 0 3px rgba(26,115,232,0.15);
}

.nav-page-empty {
  text-align: center;
  padding: var(--zhuoer-spacing-xl, 40px) 0;
  color: var(--zhuoer-color-text-muted, #5f6368);
  font-size: 0.9375rem;
  display: none;
}
.nav-page-empty.show { display: block; }

/* 分类横向滚动 */
.nav-cat-scroll {
  overflow-x: auto;
  margin-bottom: var(--zhuoer-spacing-lg, 24px);
  padding-bottom: 6px;
}
.nav-cat-scroll::-webkit-scrollbar { height: 4px; }
.nav-cat-scroll::-webkit-scrollbar-thumb { background: var(--zhuoer-color-border, #e8e8e8); border-radius: 2px; }
.nav-cat-list {
  display: flex;
  gap: 8px;
  padding: 4px 2px;
  min-width: max-content;
}
.nav-cat-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 18px;
  background: var(--zhuoer-color-bg, #fff);
  border: 1px solid var(--zhuoer-color-border, #e8e8e8);
  border-radius: 20px;
  font-size: 0.8125rem;
  cursor: pointer;
  transition: all var(--zhuoer-transition, 0.2s);
  color: var(--zhuoer-color-text-muted, #5f6368);
  user-select: none;
  font-family: inherit;
}
.nav-cat-btn:hover, .nav-cat-btn.active {
  background: var(--zhuoer-color-accent, #1a73e8);
  color: var(--zhuoer-color-bg, #fff);
  border-color: var(--zhuoer-color-accent, #1a73e8);
}
.nav-cat-count { font-size: 0.6875rem; opacity: .75; }

/* 推荐区 & 分类区块标题 */
.nav-featured { margin-bottom: var(--zhuoer-spacing-xxl, 40px); }
.nav-featured-title, .nav-cat-block-title {
  font-size: 1rem;
  font-weight: 700;
  margin: 0 0 var(--zhuoer-spacing-md, 16px);
  padding-bottom: var(--zhuoer-spacing-sm, 8px);
  border-bottom: 2px solid var(--zhuoer-color-accent, #1a73e8);
  color: var(--zhuoer-color-text, #202124);
}
.nav-cat-block-title { border-bottom-width: 1px; }
.nav-cat-block-title a { color: inherit; text-decoration: none; }
.nav-cat-block-title a:hover { color: var(--zhuoer-color-accent, #1a73e8); }
.nav-cat-title-count {
  font-weight: 400;
  color: var(--zhuoer-color-text-muted, #5f6368);
  font-size: 0.8125rem;
  margin-left: 8px;
  opacity: .6;
}

/* 推荐区卡片grid — 使用 nav.v2.css 的 .nav-grid */
.nav-featured-grid, .nav-cat-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

/* 分类区块 */
.nav-cat-block { margin-bottom: var(--zhuoer-spacing-xl, 40px); }

/* 底部 */
.nav-page-footer {
  margin-top: var(--zhuoer-spacing-xl, 40px);
  padding-top: var(--zhuoer-spacing-md, 16px);
  border-top: 1px solid var(--zhuoer-color-border, #e8e8e8);
  text-align: center;
  font-size: 0.75rem;
  color: var(--zhuoer-color-text-muted, #5f6368);
  opacity: .7;
}

.nav-cat-block.hidden, #section-featured.hidden { display: none; }

/* 暗色模式 */
[data-theme="dark"] #nav-page-wrap { color: var(--zhuoer-color-text, #e8e8e8); }
[data-theme="dark"] .nav-page-search input {
  background: var(--zhuoer-color-surface, #252525);
  border-color: var(--zhuoer-color-border, #333);
  color: var(--zhuoer-color-text, #e8e8e8);
}
[data-theme="dark"] .nav-cat-btn {
  background: var(--zhuoer-color-surface, #252525);
  border-color: var(--zhuoer-color-border, #333);
  color: var(--zhuoer-color-text-muted, #888);
}
[data-theme="dark"] .nav-cat-btn:hover, [data-theme="dark"] .nav-cat-btn.active {
  background: var(--zhuoer-color-accent, #4dabf7);
  color: #1a1a1a;
  border-color: var(--zhuoer-color-accent, #4dabf7);
}
[data-theme="dark"] .nav-featured-title { border-color: var(--zhuoer-color-accent, #4dabf7); }
[data-theme="dark"] .nav-cat-block-title { border-color: var(--zhuoer-color-border, #333); }
[data-theme="dark"] .nav-page-footer { border-color: var(--zhuoer-color-border, #333); }

/* 响应式 */
@media (max-width: 1100px) {
  .nav-featured-grid, .nav-cat-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
  .nav-featured-grid, .nav-cat-grid { grid-template-columns: repeat(2, 1fr); }
  .nav-page-header { padding: var(--zhuoer-spacing-lg, 24px) 0 var(--zhuoer-spacing-md, 16px); }
}
@media (max-width: 480px) {
  .nav-featured-grid, .nav-cat-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
}
</style>

<div id="nav-page-wrap">
    <!-- 搜索 -->
    <div class="nav-page-header">
        <h1>🌐 网址导航</h1>
        <p>发现优质网站，收藏精彩内容</p>
        <div class="nav-page-search">
            <input type="search" id="nav-search" placeholder="搜索网站名称或关键词..." autocomplete="off">
        </div>
    </div>
    <div class="nav-page-empty" id="nav-empty">未找到匹配结果</div>

    <!-- 分类快捷入口 -->
    <div class="nav-cat-scroll">
        <div class="nav-cat-list">
            <span class="nav-cat-btn active" data-cat="all">全部<span class="nav-cat-count"><?php echo $query->found_posts; ?></span></span>
            <?php foreach ($cats as $cat): ?>
            <span class="nav-cat-btn" data-cat="<?php echo $cat->term_id; ?>">
                <?php echo esc_html($cat->name); ?><span class="nav-cat-count"><?php echo $cat->count; ?></span>
            </span>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 推荐网站 — 使用 nav.v2.css 卡片样式 -->
    <?php if (!empty($featured)): ?>
    <div class="nav-featured" id="section-featured">
        <h2 class="nav-featured-title">⭐ 推荐网站</h2>
        <div class="nav-featured-grid">
            <?php foreach ($featured as $item):
                $p = $item['post']; $m = $item['meta'];
                $url = $m['url'] ?: '#';
                $name = $m['name'] ?: $p->post_title;
                $desc = $m['description'] ?: wp_strip_all_tags($p->post_content);
                $tags = get_the_terms($p->ID, 'nav_tag');
                $postCats = get_the_terms($p->ID, 'nav_category');
            ?>
            <article class="nav-card" data-cat="featured" data-keywords="<?php echo esc_attr($m['keywords'] ?? ''); ?>" data-name="<?php echo esc_attr($name); ?>">
                <a href="<?php echo esc_url(get_permalink($p->ID)); ?>" class="nav-card-main">
                    <div class="nav-card-media">
                        <?php if ($m['logo']): ?>
                        <div class="blur-bg" style="background-image:url('<?php echo esc_url($m['logo']); ?>')"></div>
                        <img src="<?php echo esc_url($m['logo']); ?>" alt="<?php echo esc_attr($name); ?>" class="nav-card-img" loading="lazy">
                        <?php else: ?>
                        <span class="nav-card-letter"><?php echo esc_html(mb_substr($name, 0, 1, 'UTF-8')); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="nav-card-body">
                        <h3 class="nav-card-title"><b><?php echo esc_html($name); ?></b></h3>
                        <?php if ($desc): ?>
                        <div class="nav-card-desc"><?php echo esc_html($desc); ?></div>
                        <?php endif; ?>
                    </div>
                </a>
                <div class="nav-card-footer">
                    <div class="nav-card-tags">
                        <?php if ($postCats && !is_wp_error($postCats)): ?>
                            <?php foreach (array_slice($postCats, 0, 1) as $c): ?>
                            <a href="<?php echo esc_url(get_term_link($c)); ?>" class="nav-card-tag tag-cat"><?php echo esc_html($c->name); ?></a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if ($tags && !is_wp_error($tags)): ?>
                            <?php foreach (array_slice($tags, 0, 2) as $t): ?>
                            <a href="<?php echo esc_url(get_term_link($t)); ?>" class="nav-card-tag"><?php echo esc_html($t->name); ?></a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener" class="nav-card-togo" title="直达">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- 分类网站 — 使用 nav.v2.css 卡片样式 -->
    <?php foreach ($sitesByCat as $catId => $group):
        $term = $group['term'];
        $sites = $group['sites'];
    ?>
    <div class="nav-cat-block" data-cat="<?php echo $term->term_id; ?>">
        <h2 class="nav-cat-block-title">
            <?php if ($term->term_id): ?>
                <a href="<?php echo esc_url(get_term_link($term)); ?>"><?php echo esc_html($term->name); ?></a>
            <?php else: ?>
                未分类
            <?php endif; ?>
            <span class="nav-cat-title-count">(<?php echo count($sites); ?>)</span>
        </h2>
        <div class="nav-cat-grid">
            <?php foreach ($sites as $item):
                $p = $item['post']; $m = $item['meta'];
                $url = $m['url'] ?: '#';
                $name = $m['name'] ?: $p->post_title;
                $desc = $m['description'] ?: wp_strip_all_tags($p->post_content);
                $tags = get_the_terms($p->ID, 'nav_tag');
                $postCats = get_the_terms($p->ID, 'nav_category');
            ?>
            <article class="nav-card" data-cat="<?php echo $term->term_id; ?>" data-keywords="<?php echo esc_attr($m['keywords'] ?? ''); ?>" data-name="<?php echo esc_attr($name); ?>">
                <a href="<?php echo esc_url(get_permalink($p->ID)); ?>" class="nav-card-main">
                    <div class="nav-card-media">
                        <?php if ($m['logo']): ?>
                        <div class="blur-bg" style="background-image:url('<?php echo esc_url($m['logo']); ?>')"></div>
                        <img src="<?php echo esc_url($m['logo']); ?>" alt="<?php echo esc_attr($name); ?>" class="nav-card-img" loading="lazy">
                        <?php else: ?>
                        <span class="nav-card-letter"><?php echo esc_html(mb_substr($name, 0, 1, 'UTF-8')); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="nav-card-body">
                        <h3 class="nav-card-title"><b><?php echo esc_html($name); ?></b></h3>
                        <?php if ($desc): ?>
                        <div class="nav-card-desc"><?php echo esc_html($desc); ?></div>
                        <?php endif; ?>
                    </div>
                </a>
                <div class="nav-card-footer">
                    <div class="nav-card-tags">
                        <?php if ($postCats && !is_wp_error($postCats)): ?>
                            <?php foreach (array_slice($postCats, 0, 1) as $c): ?>
                            <a href="<?php echo esc_url(get_term_link($c)); ?>" class="nav-card-tag tag-cat"><?php echo esc_html($c->name); ?></a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if ($tags && !is_wp_error($tags)): ?>
                            <?php foreach (array_slice($tags, 0, 2) as $t): ?>
                            <a href="<?php echo esc_url(get_term_link($t)); ?>" class="nav-card-tag"><?php echo esc_html($t->name); ?></a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener" class="nav-card-togo" title="直达">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (empty($featured) && empty($sitesByCat)): ?>
    <div style="text-align:center;padding:80px 0;color:var(--zhuoer-color-text-muted, #5f6368);">
        <p style="font-size:1.25rem;margin:0 0 8px;">暂无导航网站</p>
        <p style="font-size:0.875rem;">请在后台添加网站后刷新页面</p>
    </div>
    <?php endif; ?>

    <div class="nav-page-footer">
        址导航 · <?php echo wp_date('Y'); ?> · <?php bloginfo('name'); ?>
    </div>
</div>

<script>
(function() {
    var searchInput = document.getElementById('nav-search');
    var emptyMsg = document.getElementById('nav-empty');
    var catBtns = document.querySelectorAll('.nav-cat-btn');
    var catBlocks = document.querySelectorAll('.nav-cat-block');
    var featSection = document.getElementById('section-featured');
    var currentCat = 'all';

    catBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            currentCat = this.getAttribute('data-cat');
            catBtns.forEach(function(b){ b.classList.remove('active'); });
            this.classList.add('active');
            filterAll();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', filterAll);
    }

    function filterAll() {
        var keyword = searchInput ? searchInput.value.trim().toLowerCase() : '';
        var visibleCount = 0;

        catBlocks.forEach(function(block) {
            var catMatch = (currentCat === 'all') || (block.getAttribute('data-cat') === currentCat);
            block.classList.toggle('hidden', !catMatch);
        });

        document.querySelectorAll('.nav-card').forEach(function(card) {
            var name = (card.getAttribute('data-name') || '').toLowerCase();
            var kw = (card.getAttribute('data-keywords') || '').toLowerCase();
            var cat = card.getAttribute('data-cat');
            var catMatch = (currentCat === 'all') || (cat === currentCat) || (cat === 'featured' && currentCat === 'all');
            var searchMatch = !keyword || name.includes(keyword) || kw.includes(keyword);
            card.style.display = (catMatch && searchMatch) ? '' : 'none';
            if (catMatch && searchMatch) visibleCount++;
        });

        if (featSection) {
            var hasVisible = Array.from(featSection.querySelectorAll('.nav-card')).some(function(c){ return c.style.display !== 'none'; });
            featSection.classList.toggle('hidden', !hasVisible || currentCat !== 'all');
        }

        emptyMsg.classList.toggle('show', visibleCount === 0 && (keyword || currentCat !== 'all'));
    }
})();
</script>

<?php get_footer(); ?>