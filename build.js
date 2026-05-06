/**
 * ZHUOER Theme Build Script
 * 生成 .min.css / .min.js 文件，无需额外依赖
 * 用法: node build.js
 */

const fs = require('fs');
const path = require('path');

// 基本 CSS 压缩
function minifyCSS(css) {
    return css
        .replace(/\/\*[\s\S]*?\*\//g, '')     // 删除注释
        .replace(/\s+/g, ' ')                  // 合并空白
        .replace(/;\s*}/g, '}')               // 删除最后一个分号
        .replace(/\{\s+/g, '{')
        .replace(/;\s+/g, ';')
        .replace(/,\s+/g, ',')
        .replace(/:\s+/g, ':')
        .replace(/\{\s*/g, '{')
        .replace(/\}\s*/g, '}')
        .replace(/\s*\{\s*/g, '{')
        .replace(/;\s*\}/g, '}')
        .trim();
}

// 基本 JS 压缩（安全模式：保留关键空格）
function minifyJS(js) {
    return js
        .replace(/\/\*[\s\S]*?\*\//g, '')     // 多行注释
        .replace(/\/\/.*$/gm, '')             // 单行注释
        .replace(/^\s+/gm, '')                // 行首空白
        .replace(/\n+/g, '\n')                // 合并空行
        .trim();
}

// 处理文件
function processFile(src, dest, minifier) {
    if (!fs.existsSync(src)) {
        console.log(` 跳过（不存在）: ${src}`);
        return;
    }
    const content = fs.readFileSync(src, 'utf8');
    const minified = minifier(content);
    fs.writeFileSync(dest, minified);
    const saved = content.length - minified.length;
    const pct = ((saved / content.length) * 100).toFixed(1);
    console.log(` ✓ ${path.basename(dest)} (${minified.length} bytes, 节省 ${pct}%)`);
}

console.log('🦞 ZHUOER Theme Build');
console.log('=====================\n');

const assets = [
    { src: 'assets/css/print.css',   dest: 'assets/css/print.min.css',   type: 'css' },
    { src: 'assets/css/mobile.css',  dest: 'assets/css/mobile.min.css',  type: 'css' },
    { src: 'assets/css/header.css',  dest: 'assets/css/header.min.css',  type: 'css' },
    { src: 'assets/js/main.js',      dest: 'assets/js/main.min.js',      type: 'js' },
];

assets.forEach(item => {
    const minifier = item.type === 'css' ? minifyCSS : minifyJS;
    processFile(item.src, item.dest, minifier);
});

console.log('\n✅ 构建完成！');
console.log('   生产环境（WP_DEBUG=false）会自动加载 .min 文件');
