Modernizr.load([{
    test: Modernizr.canvastext,
    nope: ['canvas.text.js', 'optimer-bold-normal.js']
},
    {
        test: Modernizr.localstorage,
        nope: ['json2.js', 'storage_polyfill.js'],
        both: ['jquery-1.7.min.js', 'hangman.js'],
        complete: function() {
            init();
        }
    }]
);