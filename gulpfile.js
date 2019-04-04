const packagejson = require('./package.json');
const plugins = require('gulp-load-plugins')({
    pattern: ['*'],
    scope: ['devDependencies']
});
const timestamp = Math.round(Date.now() / 1000);


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
            timestamp: timestamp,
            log: function() {}
        })
            .on('glyphs', function(glyphs, options) {
                glyphs.forEach(function (glyph) {
                    glyph.unicode = glyph.unicode[0].charCodeAt(0).toString(16).toUpperCase();
                });

                plugins.pump([
                    plugins.gulp.src(packagejson.paths.theme + '/' + packagejson.paths.styles + '/src/configs/_fonticon.twig'),
                    plugins.consolidate('twig', {
                        fontName: options.fontName,
                        fontVersion: timestamp,
                        glyphs: glyphs
                    }),
                    plugins.rename('_fonticon.scss'),
                    plugins.gulp.dest(packagejson.paths.theme + '/' + packagejson.paths.styles + '/dist')
                ], pumpCallback);
            }),
        plugins.gulp.dest(packagejson.paths.theme + '/' + packagejson.paths.fonts)
    ], pumpCallback);
}

// Compression images
function images(pumpCallback) {
    return plugins.pump([
        plugins.gulp.src(packagejson.paths.theme + '/' + packagejson.paths.images + '/**/*.{png,jpg}'),
        plugins.imagemin({
            progressive: true,
            interlaced: true,
            optimizationLevel: 7,
            verbose: true,
            use: []
        }),
        plugins.gulp.dest(packagejson.paths.theme + '/' + packagejson.paths.images)
    ], pumpCallback);
}

// Alias
exports.sass = sass;
exports.fonticon = plugins.gulp.series(fonticon, sass);
exports.images = images;
exports.default = plugins.gulp.series(sass, watchsass);
