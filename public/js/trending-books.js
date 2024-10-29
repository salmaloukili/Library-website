var currentIndex = 0;

function swipe(direction) {
    if (direction === 'left') {
        console.log('Skipped book:', books[currentIndex].bookTitle);
    } else if (direction === 'right') {
        console.log('Liked book:', books[currentIndex].bookTitle);
        // ---> AJAX request to BookController.php --->
        $.ajax({
            url: '/book/' + books[currentIndex].id + '/like',
            type: 'POST',
            success: function (response) {
                // Handle the response if needed
                console.log('Likes incremented successfully');
            },
            error: function (xhr, status, error) {
                // Handle the error if needed
                console.error('Error incrementing likes: ' + error);
            }
        });
        // <--- AJAX request to BookController.php <---
    }
    currentIndex++;

    if (currentIndex < books.length) {
        showBook(books[currentIndex]);
    } else {
        // TODO: Handle end of book list
        console.log('End of book list');
    }
}


function showBook(book) {
    var cardImage = document.querySelector('.card-image');
    var coverUrl = 'https://covers.openlibrary.org/b/isbn/' + book.ISBN + '-M.jpg';

    var coverImage = new Image();

    coverImage.onload = function() {
        // Check if the loaded image has valid dimensions
        if (coverImage.naturalWidth === 1 && coverImage.naturalHeight === 1) {
            // Invalid cover image, set the default image URL
            cardImage.src = placeholder;
            console.log('INVALID IMAGE FROM OPENLIBRARY');
        } else {
            // Valid cover image, set the URL
            cardImage.src = coverUrl;
            console.log('VALID IMAGE FROM OPENLIBRARY');
        }
        cardImage.classList.add('card-image');
        cardImage.onclick = goToBook;
    };

    coverImage.onerror = function() {
        // Error loading cover image, set the default image
        cardImage.src = placeholder;
        cardImage.classList.add('card-image');
        cardImage.onclick = goToBook;
    };

    // Start loading the cover image
    coverImage.src = coverUrl;

    // Update cardImage
    /*cardImage.innerHTML = '<img src="https://covers.openlibrary.org/b/isbn/' + book.ISBN + '-M.jpg">';*/
    // Replace cardImage
    var newImage = new Image();
    newImage.src = 'https://covers.openlibrary.org/b/isbn/' + book.ISBN + '-M.jpg';
    newImage.addEventListener('load', function() {
        if (cardImage) {
            cardImage.src = newImage.src;
            cardImage.classList.add('card-image');
            cardImage.onclick = goToBook;
        }
    });

    //Update Like Button
    var likeButton = document.querySelector('.btn-like');
    var disButton = document.querySelector('.disabled-button');
    var heartSymbol = document.querySelector('.heart-symbol');
    var buttonText = document.querySelector('.btn-text');

    var isMatch = likedBooks.some(function(likedBook) {
        return likedBook.id === book.id;
    });

    if (isMatch && likeButton) {
        likeButton.disabled = true;
        buttonText.textContent = 'LIKED';
        console.log('book is already liked');
        heartSymbol.style.display = 'none';
        likeButton.classList.replace('btn-like', 'disabled-button');
    }
    else if (!isMatch && likeButton) {
        likeButton.disabled = false;
        buttonText.textContent = 'LIKE';
        heartSymbol.style.display = 'inline';
        console.log('like this book!');
    }
    else if (!isMatch && disButton) {
        disButton.disabled = false;
        buttonText.textContent = 'LIKE';
        console.log('like this book!');
        heartSymbol.style.display = 'inline';
        disButton.classList.replace('disabled-button','btn-like');
    }
    else{
        disButton.disabled = true;
        buttonText.textContent = 'LIKED';
        heartSymbol.style.display = 'none';
        console.log('book is already liked');
    }


    // Update card-content
    var cardContent1 = document.querySelector('.card-content1');
    cardContent1.querySelector('h3').textContent = book.bookTitle;
    var cardContent2 = document.querySelector('.card-content2');
    cardContent2.querySelector('p:nth-child(1)').textContent = 'Author: ' + book.authorName;
    cardContent2.querySelector('p:nth-child(2)').textContent = 'Publisher: ' + book.publisher;
    cardContent2.querySelector('p:nth-child(3)').textContent = 'Number of pages: ' + book.numberOfPages + ' pages';
    cardContent2.querySelector('p:nth-child(4)').textContent = 'Likes: ' + book.likes + ' likes';
}


function goToBook() {
    // Construct the URL for the book page
    var bookUrl = '/BookBinder/book/' + books[currentIndex].id;

    // Redirect to the book page
    window.location.href = bookUrl;
}
