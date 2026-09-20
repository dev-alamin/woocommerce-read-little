jQuery(document).ready(function($){
    $(document).on("click", '.pdvwc-open-popup-btn', function(event) {
        event.preventDefault();

        $.fancybox.open({
            src: $('.pdvwc-thumbnails'),
            type: 'inline',
            fitToView: false,
            autoSize: true,
            autoDimensions: false,
            clickOutside: false,
            drag: false,
            touch: false,
            i18n: {
                en: {
                    ERROR: 'No Preview has found'
                }
            },
            helpers: {
                overlay: {
                    locked: false
                }
            }
        });
        $('.pdvwc-thumbnails-container').removeClass('hidden');
    });
});