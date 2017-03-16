
var form;
var formOpened = false;
var replyAll = document.querySelectorAll('.reply');

if (replyAll) {
    form = document.createElement('form');
    /*form.setAttribute('action', '/response-comment');
     form.setAttribute('method', 'post');*/
    form.style.display = 'flex';
    form.style.width = '100%';
    form.style.alignItems = 'center';
    form.style.marginTop = '8px';

    var textarea = document.createElement('textarea');
    textarea.style.width = '75%';
    textarea.setAttribute('name', 'content');

    var submit = document.createElement('input');
    submit.setAttribute('type', 'submit');
    submit.setAttribute('value', 'Envoyer');

    form.appendChild(textarea);
    form.appendChild(submit);

    var commentId;
    var articleId;

    replyAll.forEach(function (reply) {
        reply.children[2].addEventListener('click', function (e) {
            e.stopPropagation();
            e.preventDefault();

            reply.parentNode.insertBefore(form, reply.nextSibling);
            commentId = this.parentNode.getAttribute('data-id');
            articleId = this.parentNode.getAttribute('data-article_id');

            if(formOpened) {
                $(form).each(function() {
                    $(this.firstChild).animate({
                        height: 0
                    }, "normal");
                });

                setTimeout(function () {
                    form.remove();
                }, 300);

                formOpened = false;
            } else {
                $(form).each(function () {
                    $(this.firstChild).animate({
                        height: 100
                    }, "normal");
                });
            }
            formOpened = true;
        });
    });

    form.addEventListener('submit', function (e) {
        var content = this.firstChild.value;
        var self = this;

        e.preventDefault();

        $.post('/response-comment', {
            id_parent: commentId,
            id_article: articleId,
            content: content
        }).done(function () {
            self.firstChild.value = '';
            window.top.location.reload();
        })
    });

    form.addEventListener('click', function (e) {
        e.stopPropagation();
    });

    document.body.addEventListener('click', function () {
        if(formOpened) {
            $(form).each(function() {
                $(this.firstChild).animate({
                    height: 0
                }, "normal");
            });
            setTimeout(function () {
                form.remove();
            }, 300);

            formOpened = false;
        }
    });
}