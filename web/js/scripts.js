if (typeof pistol88 == "undefined" || !pistol88) {
    var pistol88 = {};
}

pistol88.gallery = {
    init: function () {
        $('.pistol88-gallery-item a.delete').on('click', this.deleteProductImage);
        $('.pistol88-gallery img').on('click', this.setMainProductImage);
    },
    setMainProductImage: function () {
        pistol88.gallery._sendData($(this).data('action'), $(this).parents('li').data());
        $('.pistol88-gallery > li').removeClass('main');
        $(this).parents('li').addClass('main');
        return false;
    },
    deleteProductImage: function () {
        if (confirm('realy?')) {
            pistol88.gallery._sendData($(this).data('action'), $(this).parents('.pistol88-gallery-item').data());
            $(this).parents('.pistol88-gallery-item').hide('slow');
        }
        return false;
    },
    _sendData: function (action, data) {
        return $.post(
            action,
            {image: data.image, id: data.id, model: data.model},
            function (answer) {
                var json = $.parseJSON(answer);
                if (json.result == 'success') {
                    
                }
                else {
                    alert(json.error);
                }
            }
        );
    }
}

pistol88.gallery.init();