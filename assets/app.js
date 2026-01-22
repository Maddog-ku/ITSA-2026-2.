$(function () {
    $('.js-auto-submit').on('change', function () {
        $(this).closest('form').trigger('submit');
    });

    let typingTimer = null;
    $('.js-auto-submit-text').on('input', function () {
        const $form = $(this).closest('form');
        if (typingTimer) {
            clearTimeout(typingTimer);
        }
        typingTimer = setTimeout(function () {
            $form.trigger('submit');
        }, 400);
    });

    $('.js-delete').on('submit', function (event) {
        const message = $(this).data('confirm') || '確認要刪除這筆資料嗎？';
        if (!confirm(message)) {
            event.preventDefault();
        }
    });
});
