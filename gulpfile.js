const packagejson = require('./package.json');
const plugins = require('gulp-load-plugins')({
    pattern: ['*'],
    scope: ['devDependencies']
});

// Sass
function sass(pumpCallback) {
    let themePath = process.argv.indexOf('--admin') === -1 ? packagejson.paths.theme : packagejson.paths.theme_admin;

    return plugins.pump([
        plugins.gulp.src(themePath + '/' + packagejson.paths.styles + '/src/**/*.scss'),
        plugins.sass(),
        plugins.postcss([
            plugins.autoprefixer(packagejson.autoprefixer),
            plugins.postcssPxtorem(packagejson.pxtorem)
        ]),
        plugins.rename('theme.css'),
        plugins.gulp.dest(themePath + '/' + packagejson.paths.styles + '/dist/')
    ], pumpCallback);
}

// Watch SASS
function watchsass() {
    let themePath = process.argv.indexOf('--admin') === -1 ? packagejson.paths.theme : packagejson.paths.theme_admin;

    plugins.gulp.watch(themePath + '/' + packagejson.paths.styles + '/src/**/*.scss', plugins.gulp.parallel(sass));
}

// FontIcon
function fonticon(pumpCallback) {
    return plugins.pump([
        plugins.gulp.src(packagejson.paths.theme + '/' + packagejson.paths.fonts + '/icons/*.svg'),
        plugins.iconfont({
            fontName: 'Icons',
            formats: ['woff', 'woff2', 'svg'],
            timestamp: Math.round(Date.now() / 1000)
        }).on('glyphs', function(glyphs, options) {
            // Generate css
            console.log(glyphs);
        }),
        plugins.gulp.dest(packagejson.paths.theme + '/' + packagejson.paths.fonts)
    ], pumpCallback);
}

// Compression Images
function images(pumpCallback) {

}

// Alias
exports.sass = sass;
exports.fonticon = fonticon;
exports.images = images;
exports.default = plugins.gulp.series(sass, watchsass);