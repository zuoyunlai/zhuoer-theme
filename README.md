# ZHUOER WordPress 主题

> 简约极客美学 × 动态主题色系统 × 完整 SEO 体系，专为中文内容创作者打造。

**版本：** 1.0.75 | **演示站：** [www.yily.top](https://www.yily.top) | **主题文档：** [飞书评测文章](https://feishu.cn/docx/MAVodyefjoEW51xxwRec79b4nFf)

---

## 核心特性

### 🎨 动态主题色系统
选择一个主色调，主题通过 HSL → RGB 算法**自动生成**完整配色体系：

- 链接色、悬停色
- 背景浅色（10% 透明度）
- 边框色（30% 透明度）
- CTA 按钮投影与悬停色
- Hero 区域渐变色（跟随主色调动态变化）
- 暗色模式下的亮色强调色

> 换主题色只需 5 秒，无需任何 CSS 知识。

### 🌙 暗色模式
- 根据系统偏好自动切换（`prefers-color-scheme`）
- 手动切换（Header 右上角月亮图标）
- 切换状态持久化（localStorage）
- 暗色模式强调色同样来自主题色算法，亮/暗两套配色协调统一

### 🚀 SEO 优化体系
内置完整 SEO 功能，无需额外插件：

| 功能 | 说明 |
|------|------|
| Meta 标签 | 首页标题、描述自定义输出 |
| Open Graph | 微信/微博/QQ 分享卡片（标题+描述+图片+类型） |
| Twitter Card | Summary Card with Large Image |
| JSON-LD Schema | WebSite + Article 结构化数据（支持 Google/Bing） |
| Canonical URL | 自动输出规范链接，防止重复内容 |
| 自动站点地图 | 访问 `/sitemap.xml` 自动生成 |
| 百度统计 | 直接填入 ID，无需修改模板 |
| Google Analytics 4 | 直接填入 ID，自动输出 gtag.js |
| Breadcrumbs | 调用 `zhuoer_breadcrumbs()` 函数即可 |
| 百度验证 | 一键填入验证代码 |

### 📱 全平台自适应
- **桌面端**：最大宽度 1080px，内容区 720px + 侧边栏双栏布局
- **平板/移动端**：自动折叠为单栏，触摸友好的间距与按钮尺寸
- 图片自动懒加载（`loading="lazy"`），首图优先加载

### ⚡ 性能优化
- Emoji 表情脚本禁用（大幅减少 JS 体积）
- Google Fonts 国内镜像加速（fonts.googleapis.cn）
- Gravatar 头像可选 CDN 替换
- CSS/JS 资源自动版本号（基于文件修改时间）
- 打印样式表独立加载
- Service Worker PWA 支持（可离线访问已浏览页面）

---

## 模板函数

主题提供的模板函数，可在任意模板文件中调用：

```php
// 文章元信息
zhuoer_posted_on();          // 发布日期（带图标）
zhuoer_posted_by();           // 作者名称
zhuoer_posted_in();           // 分类标签
zhuoer_reading_time($post_id);// 阅读时间估算
zhuoer_entry_tags();         // 文章标签

// 文章内容
zhuoer_post_thumbnail();     // 特色图片（带懒加载）
zhuoer_author_bio();         // 作者信息卡片

// 导航
zhuoer_posts_pagination();    // 数字分页导航（1 2 3 ... N）
zhuoer_post_navigation();    // 上一篇 / 下一篇导航

// 其他
zhuoer_breadcrumbs();        // 面包屑导航
zhuoer_social_links();       // 社交链接（自定义面板配置）
zhuoer_toc($post_id);        // 文章目录（可选功能）
```

---

## 自定义面板设置项

进入 **外观 → 自定义**，共 5 个标签页：

### SEO 与统计分析
- 首页 Meta 标题（留空使用网站名称）
- 首页 Meta 描述（建议 120-150 字符）
- OG 分享图 URL（微信/微博，尺寸建议 1200×630px）
- 百度验证代码
- 百度统计 ID
- Google Analytics 4 ID
- ICP 备案号

### 首页设置
- Hero 区域大标题
- Hero 副标题
- CTA 按钮文字与链接
- Hero 幻灯片管理（多张轮播图，可视化编辑）

### 社交链接
- 微博 / 知乎 / B站 / 小红书 / GitHub / Twitter / Facebook / Instagram / LinkedIn

### 文章显示
- 是否显示特色图片
- 是否显示作者信息卡片

### 布局设置
- 首页是否显示侧边栏
- 分类/标签/归档页是否显示侧边栏
- 文章详情页是否显示侧边栏

---

## 技术规格

| 项目 | 要求 |
|------|------|
| WordPress | 6.0+ |
| PHP | 8.0+ |
| 测试至 | WordPress 6.7 |
| 许可证 | GPL v2+ |

### 文件结构

```
zhuoer/
├── style.css                 # 主样式表（含 CSS 变量定义）
├── functions.php             # 主题核心（Hook、Customizer、SEO）
├── front-page.php            # 首页模板（Hero + 最新文章）
├── index.php                 # 归档/博客列表模板
├── single.php                # 文章详情
├── page.php                  # 静态页面
├── archive.php               # 分类/标签/作者/日期归档
├── singular.php              # 文章与页面共用模板
├── search.php                # 搜索结果
├── 404.php                   # 404 页面
├── header.php                # HEAD 与导航
├── footer.php                # 页脚与脚本
├── sidebar.php               # 侧边栏
├── comments.php              # 评论模板
├── searchform.php            # 搜索表单
├── page-nav-template.php     # 自定义页面类型
├── screenshot.png            # 主题截图
├── favicon.svg               # SVG 网站图标
├── inc/
│   ├── customizer.php        # 自定义面板后台
│   ├── template-tags.php     # 模板辅助函数
│   ├── seo.php              # SEO 标签注入
│   └── class-zhuoer-recent-comments-widget.php
└── assets/
    ├── css/                  # 模块化样式表
    └── js/                   # 主题脚本
```

---

## 安装与配置

### 安装步骤

1. 下载主题包，上传 `zhuoer` 文件夹至 `/wp-content/themes/`
2. 进入 **外观 → 主题**，激活 ZHUOER
3. 进入 **外观 → 自定义**，按标签页配置各项设置
4. （可选）安装 WooCommerce 以启用商城功能

### 创建子主题

```php
/*
 Theme Name:   ZHUOER Child
 Template:     zhuoer
*/

@import url('../zhuoer/style.css');

/* 在此覆盖父主题样式或函数 */
```

---

## 更新日志

### 1.0.75 (2026-05-06)
- **修复**: style.css 花括号平衡（2处多余 `}` ）
- **修复**: TOC 目录点击跳转不生效（`is_singular('post')` → `is_singular()`）
- **修复**: 首页幻灯片布局对齐（`grid-template-columns: 3fr 2fr`，图片 `aspect-ratio: 4/3`）
- **修复**: 商城排序重复（移除 `archive-product.php` / `taxonomy-product_cat.php` 重复调用）
- **修复**: TOC 锚点被 sticky header 遮挡（`scroll-margin-top`）
- **修复**: 移动端菜单底部被遮（`max-height` + `overflow-y: auto`）
- **修复**: 移动端卡片间距（`margin-bottom: 1.5rem`）
- **修复**: 移动端幻灯片图片比例（`aspect-ratio: 4/3`）+ 指示点恢复
- **修复**: 移动端汉堡图标去外圈
- **国际化**: comments.php / content-single-product.php / customizer.php / Product Widget 全部 i18n
- **优化**: Widget 加 Transient 12h 缓存
- **优化**: Service Worker 改为 `wp_enqueue_script` 注册
- **优化**: Google Fonts 加 preload hint
- **优化**: `content_width` 720→900
- **优化**: enhanced.css 去重（4条完全重复规则移除）
- **清理**: search.php 死代码移除
- **配色系统重构** — 主色调通过 HSL→RGB 算法生成完整派生配色：链接、悬停、背景、边框、阴影
- **Hero 渐变色** — 首屏背景渐变现在跟随主色调动态变化
- **暗色模式配色** — 暗色模式链接/强调色来自主题色算法（亮色调），替代硬编码的通用蓝色
- **分类标签样式** — 文章分类徽章使用主题色浅色背景
- **日历组件** — 文章日期从黑色实心圆改为透明背景+主题色边框
- **阅读更多按钮** — 悬停状态使用正确的主题派生色
- **媒体上传器修复** — Hero 幻灯片图片上传按钮在自定义面板中正常工作

### 1.0.69
- 首个公开发布版本

---

## 适用场景

| 场景 | 推荐度 |
|------|--------|
| 个人技术博客 | ⭐⭐⭐⭐⭐ |
| 中文内容创作 | ⭐⭐⭐⭐⭐ |
| SEO 需求站点 | ⭐⭐⭐⭐⭐ |
| 企业官网 | ⭐⭐⭐⭐ |
| 多语言站点 | ⭐⭐⭐ |

---

## 致谢

ZHUOER 基于 WordPress Minimalist Theme 框架构建。

---

**GNU General Public License v2 or later** — 可自由使用、修改和分发。
