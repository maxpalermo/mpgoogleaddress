var gulp = require('gulp');
var gutil = require('gulp-util');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');

gulp.task('default', function() {
    return gulp.src('jquery.mapify.js')
        .pipe(rename({
            extname: '.min.js'
        }))
        .pipe(uglify()).on('error', function(error) {
            gutil.log(error.toString());
            this.emit('end')
        })
        .pipe(gulp.dest('.'))
});

gulp.task('watch', ['default'], function() {
    gulp.watch('jquery.mapify.js', ['default']);
});
