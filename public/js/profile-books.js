function goToLikedBook(bookID) {
    // Construct the URL for the book page
    var bookUrl = '/BookBinder/book/' + bookID;

    console.log('Book URL:', bookUrl);

    // Redirect to the book page
    window.location.href = bookUrl;
}