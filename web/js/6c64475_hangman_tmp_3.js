//global vars
var canvas = $("#stage");
var word = $("#word");
var letters = $("#letters");
var wordLength, wordToGuess, triesLeft, correctGuesses, gameStatus, gameData;

$(document).ready(function() {
    init();
})

var init = function() {
    $('#loading').hide();

    //add event listeners
    $('#newGame').css('display', 'inline-block').click(newGame);
    $('#resetGame').css('display', 'inline-block').click(resetGame);

    showGameStatus();
}

var resetGame = function() {
    triesLeft = 8;
    correctGuesses = 0;
    $("#word").empty();
    $("#letters").empty();
    canvas.width = canvas.width;
    updateGameStatus();
}

var newGame = function() {
    resetGame();
    //get word
    getWord();

    var placeholders = '',
        abc = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
    triesLeft = 8;
    correctGuesses = 0;
    wordLength = wordToGuess.length;

    for (var i = 0; i < wordLength; i++) {
        placeholders += '_';
    }

    $("#word").html(placeholders);

    //// create an alphabet pad to select letters
    $("#letters").html('');
    for (i = 0; i < 26; i++) {
        var letterElement = $('<div></div>').html(abc[i]).click(checkLetter);
        $("#letters").append(letterElement);
    }

    drawCanvas();
    updateGameStatus("busy");
}

var drawCanvas = function () {
    var canvas = $("#stage")[0];

    var context = canvas.getContext('2d');

    context.beginPath();
    context.moveTo(100, 150);
    context.lineTo(450, 50);
    context.stroke();

    var c = canvas.getContext('2d');
    // reset the canvas and set basic styles
    canvas.width = canvas.width;
    c.lineWidth = 10;
    c.strokeStyle = 'green';
    c.font = 'bold 24px Optimer, Arial, Helvetica, sans-serif';
    c.fillStyle = 'red';
    // draw the ground
    drawLine(c, [20,190], [180,190]);
    // start building the gallows if there's been a bad guess

    if (triesLeft < 8) {
        // create the upright
        c.strokeStyle = '#A52A2A';
        drawLine(c, [30, 185], [30, 10]);
        if (triesLeft < 7) {
            // create the arm of the gallows
            c.lineTo(150, 10);
            c.stroke();
        }
        if (triesLeft < 6) {
            c.strokeStyle = 'black';
            c.lineWidth = 3;
            // draw rope
            drawLine(c, [145,15], [145,30]);
            // draw head
            c.beginPath();
            c.moveTo(160, 45);
            c.arc(145, 45, 15, 0, (Math.PI/180)*360);
            c.stroke();
        }
        if (triesLeft < 5) {
            // draw body
            drawLine(c, [145,60], [145,130]);
        }
        if (triesLeft < 4) {
            // draw left arm
            drawLine(c, [145,80], [110,90]);
        }
        if (triesLeft < 3) {
            // draw right arm
            drawLine(c, [145,80], [180,90]);
        }
        if (triesLeft < 2) {
            // draw left leg
            drawLine(c, [145,130], [130,170]);
        }
        if (triesLeft < 1 && gameStatus == "fail") {
            updateGameStatus(gameStatus);
            // draw right leg and end game
            drawLine(c, [145,130], [160,170]);
            c.fillText('Game over', 45, 110);

            // remove the alphabet pad
            letters.innerHTML = '';

            $("#letters").children().off();
        }
    }

    // if the word has been guessed correctly, display message,
    // update score of games won, and then show score after 2 seconds
    if (correctGuesses == wordLength) {
        letters.innerHTML = '';
        c.fillText('You won!', 45,110);
        $("#letters").children().off();
    }
}

function drawLine(context, from, to) {
    context.beginPath();
    context.moveTo(from[0], from[1]);
    context.lineTo(to[0], to[1]);
    context.stroke();
}

var updateGameStatus = function(status) {
    gameStatus = status;
    showGameStatus();
}

var showGameStatus = function() {
    var won = localStorage.getItem('won');
    var lost = localStorage.getItem('lost');

    if (gameStatus == null || gameStatus == undefined) {
        $("#gameStatus").html("no game running");
    } else {
        $("#gameStatus").html("Gamestatus: " + gameStatus);
    }
}

var getWord = function() {
    $.ajax({
        type: "POST",
        url: "games",
        dataType: "JSON",
        async: false
    }).done(function(data) {
        wordToGuess = data.word;
    });
}

var getLetter = function() {
    checkLetter($(this).text());
    $(this).html = '&nbsp;'
    $(this).addClass("visited");
    $(this).off();
}

var checkLetter = function() {
    var letterElement = $(this);
    var letter = letterElement.text();
    letterElement.html('&nbsp;');
    letterElement.off();
    var placeholders = $("#word").html();

    //guess letter
    $.ajax({
        type: "PUT",
        url: "games/" + letter.toLowerCase(),
        dataType: "json",
        async: false
    }).done(function(data) {
        gameData = data;
    });

    if (triesLeft == gameData.tries_left) {
        letterElement.addClass("correct");
        //we guessed correctly
        placeholders = placeholders.split('');
        //set letter to correct place
        for (var i = 0; i < wordLength; i++) {
            // if the selected letter matches one in the word to guess,
            // replace the underscore and increase the number of correct guesses
            if (wordToGuess.charAt(i) == letter.toLowerCase()) {
                placeholders[i] = letter;
            }
        }

        $("#word").html(placeholders.join(''));

        if (gameData.status == "success") {
            updateGameStatus(gameData.status)
            // redraw the canvas only if all letters have been guessed
            drawCanvas();
        }
    } else {
        letterElement.addClass("incorrect");
        triesLeft = gameData.tries_left;
        gameStatus = gameData.status;
        drawCanvas();
    }
}