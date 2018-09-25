const package = require('./package.json');
const plugins = require('gulp-load-plugins')({
    pattern: ['*'],
    scope: ['devDependencies']
});

// Sass Front
plugins.gulp.task('sass', function () {
    let themePath = (process.argv.indexOf('--admin') !== -1) ? package.paths.theme_admin : package.paths.theme;

    return plugins.gulp.src(themePath + '/' + package.paths.styles + '/src/**/*.scss')
        .pipe(plugins.sass().on('error', plugins.sass.logError))
        .pipe(plugins.postcss([
            plugins.autoprefixer(package.autoprefixer),
            plugins.postcssPxtorem(package.pxtorem)
        ]))
        .pipe(plugins.rename('theme.css'))
        .pipe(plugins.gulp.dest(themePath + '/' + package.paths.styles + '/dist/'));
});

// Watch SASS Front
plugins.gulp.task('watchsass', function () {
    let themePath = (process.argv.indexOf('--admin') !== -1) ? package.paths.theme_admin : package.paths.theme;

    plugins.gulp.watch(themePath + '/' + package.paths.styles + '/src/**/*.scss', ['sass']);
});

// Alias
plugins.gulp.task('default', ['sass', 'watchsass']);