'use strict';

const del = require('del'),
  gulp = require('gulp'),
  sass = require('gulp-sass'),
  autoprefixer = require('gulp-autoprefixer'),
  browserSync = require('browser-sync').create(),
  notify = require('gulp-notify'),
  sourcemaps = require('gulp-sourcemaps'),
  path = require('path'),
  svgmin = require('gulp-svgmin'),
  iconfont = require('gulp-iconfont'),
  consolidate = require('gulp-consolidate'),
  svgSprite = require('gulp-svg-sprite'),
  argv = require('yargs').argv,
  minify = require("gulp-minify"),
  plumber = require("gulp-plumber"),
  concat = require('gulp-concat-util'),
  fs = require('node-fs');

const config = require('./sites_config.json');
const siteConfig = config[(argv.site === undefined) ? 'base' : argv.site];


const r = Math.random().toString(36).substring(7);
const fontName = 'ho-iconfont-' + r;


/*  IconFont
 *  Iconfont only needs to be created for the base theme
 */

function makeFont(done) {
  del(['fonts/iconfont/**'], {
    'force': true
  });

  var iconStream = gulp.src('sourcefiles-gc/img/icons/*.svg')
    .pipe(svgmin())
    .pipe(iconfont({
      fontName: fontName,
      fontHeight: 1001,
      normalize: true
    }));

  const Glyphs = function (cb) {
    iconStream.on('glyphs', function (glyphs, options) {
      gulp.src('sourcefiles-gc/scss/base/_icons.scss')
        .pipe(consolidate('lodash', {
          glyphs: glyphs,
          fontName: fontName
        }))
        .pipe(gulp.dest('sourcefiles-gc/scss/abstracts'))
    });

    cb();
  };

  const handleFont = function (cb) {
    iconStream
      .pipe(gulp.dest('../fonts/iconfont/'))
    cb();
  };

  iconStream.on('finish', function () {
    done();
  });

  return gulp.parallel(Glyphs, handleFont)();
}

/*
 * SVG Sprite
 * Creates an SVG sprite for icons
 */

// Basic configuration example
const svgSpriteConfig = {
  log: 'info',
  shape: {
    dimension: {
      maxWidth: 100,
      maxHeight: 100
    }
  },
  mode: {
    defs: true,
  }
};

function getFolders(dir) {
  return fs.readdirSync(dir)
    .filter(function (file) {
      return fs.statSync(path.join(dir, file)).isDirectory();
    });
}

function makeSprites(done) {
  var folders = getFolders('img/sprites/');

  if (folders) {
    console.log(folders);

    folders.map(function (folder) {

      return gulp.src('imgs/sprites/' + folder + '/*.svg')
        .pipe(svgmin())
        .pipe(svgSprite(svgSpriteConfig)).on('error', function (error) {
          gutil.log(gutil.colors.red(error));
        })
        .pipe(gulp.dest('../images/svg/' + folder))
        .pipe(notify({message: folder + ' SVG Sprite generated'}));
    });

    done();

  } else {
    console.log(folders + 'not found');
    done();
  }
}

function js(done) {
  del(['dist/js/*.js'], {force: true});

  gulp.src('js/components/*.js')
    .pipe(concat('main.js'))
    .pipe(concat.header('(function ($, document, window) { $(document).ready(function() {'))
    .pipe(concat.footer('}); })(jQuery, document, window);'))
    .pipe(plumber())
    .pipe(minify({
      noSource: true,
      ext: {
        min: '-min.js'
      },
    }))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest(siteConfig.dest + 'dist/js'))
    .pipe(notify({message: 'Theme JS Task complete'}));

  done();
}


function styles(done) {
  console.log('dest: ' + siteConfig.dest);

  gulp
    .src(siteConfig.path + 'scss/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer('last 2 version'))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(siteConfig.dest + 'dist/css'))
    .pipe(notify({message: 'Theme Styles task complete'}))
    .pipe(browserSync.stream());

  done();
}

function baseStyles(done) {
  console.log('dest: ' + 'dist/css');

  gulp
    .src('./scss/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer('last 2 version'))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('dist/css'))
    .pipe(notify({message: 'Regular Styles task complete'}))
    .pipe(browserSync.stream());

  done();
}


function prod(done) {
  console.log(siteConfig.path + 'scss/*.scss');

  gulp
    .src(siteConfig.path + 'scss/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer('last 2 version'))
    .pipe(gulp.dest(siteConfig.path + 'dist/css'))
    .pipe(notify({message: 'Prod CSS task complete'}))
  done();
}

function prodAll(done) {

  for (var obj in config) {
    var path = config[obj].path;
    var name = config[obj].name;

    var pathExists = fs.existsSync(path);

    try {

      if (pathExists) {

        const siteProd = function (cb) {
          gulp.src(path + 'scss/*.scss')
            .pipe(sass().on('error', sass.logError))
            .pipe(autoprefixer('last 2 version'))
            .pipe(gulp.dest(path + 'dist/css'))
            .pipe(notify({message: name + ' css task complete'}))
          cb();
        };

        return siteProd();
      } else {
        console.log('Site ' + name + ' not found');
      }
    } catch (error) {
      done();
    }
  }
  done();
}

// Watch files
function watch() {
  console.log(siteConfig.path + 'scss/**/*.scss');
  console.log(siteConfig.name);

  browserSync.init({
    proxy: siteConfig.proxy
  });

  if(!(siteConfig.shortname === 'gcbase')){
    gulp.watch('scss/**/*.scss', gulp.series(baseStyles, styles));
  }

  gulp.watch(siteConfig.path + 'scss/**/*.scss', styles);
  gulp.watch('js/components/*.js', js);

  gulp.watch('img/icons/*.svg', gulp.series(makeFont, styles));

  //gulp.watch('styleguide/**/*.scss', styleGuide);
  //gulp.watch('styleguide/**/*.html', styleGuide);
}


exports.iconfont = gulp.series(makeFont, prodAll);
exports.prod = prod;
exports.styles = styles;
exports.js = js;
exports.sprites = makeSprites;
exports.default = watch;
exports.all = prodAll;
//exports.styleguide = styleGuide;

