$(function() {
    $("#post-img-upload").fileupload({
        url: base_url + '/post_img/upload',
        dataType: 'json',
        formData : {post_id : $("#post-img-upload").data('postId')},
        start : function (e) {
            $('#progress .bar').css('width', '0%');
        },
        done: function (e, data) {
            var result = data.result;

            if (result.type == 1) {
                var li = $('<li />' , {
                    id : 'post-img-' + result.postImage.id,
                    data: {postId : result.postImage.post_id}
                });
                li.append($('<a />', {
                        href : '#'
                    })
                    .append($('<img />', {
                            src: './../' + result.postImage.path,
                            width: 90
                        })
                    )
                );

                li.append($('<div />', {
                        class : 'extras'
                    })
                    .append($('<div />', {
                            class : 'extras-inner'
                        })
                        .append($('<a />', {
                                class: '_post-im-make-star',
                                href: base_url + '/post_img/make_star?id=' + result.postImage.id
                            })
                            .append($('<i />', {class : 'icon-star'}))
                        )
                        .append($('<a />', {
                                class: '_post-img-remove',
                                href: base_url + '/post_img/remove?id=' + result.postImage.id
                            })
                            .append($('<i />', {class: 'icon-trash'}))
                        )
                    )
                );

                if (result.postImage.is_main) {
                    $('#_main-img-preview')
                        .attr({'src': './../' +result.postImage.path})
                        .removeClass('no-img')
                        .addClass('main-img');
                }

                $('ul.gallery').append(li).fadeIn('slow');
            }
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .bar').css(
                'width',
                progress + '%'
            );
        }
    });
});
