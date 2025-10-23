$(document).ready(function () {
    $('.subscribe-btn').click(function() {
        let button = $(this);
        let authorId = button.data('author-id');

        button.prop('disabled', true);

        $.post('/author/subscribe?id=' + authorId, function(response) {
            if (response.success) {
                if (response.subscribed) {
                    button.removeClass('btn-primary').addClass('btn-outline-secondary');
                    button.text('Отписаться');
                } else {
                    button.removeClass('btn-outline-secondary').addClass('btn-primary');
                    button.text('Подписаться на автора');
                }
            } else {
                alert(response.message);
            }
        }).fail(function() {
            alert('Произошла ошибка при изменении подписки');
        }).always(function() {
            button.prop('disabled', false);
        });
    });
});