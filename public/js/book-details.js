function like(bookId) {
    $.ajax({
        url: '/book/' + bookId + '/like',
        type: 'POST',
        success: function () {
            // Handle the response if needed
            console.log('Likes incremented successfully');

            var likesElement = $('#likesCount');
            var currentLikes = parseInt(likesElement.text());
            likesElement.text(currentLikes + 1);

            // Update the like button
            var likeButton = $('#likeButton');
            likeButton.prop('disabled',true);
            likeButton.text('LIKED');
            likeButton.addClass('disabled-button');
        },
        error: function (xhr, status, error) {
            // Handle the error if needed
            console.error('Error incrementing likes: ' + error);
        }
    });
    // <--- AJAX request to BookController.php <---
}