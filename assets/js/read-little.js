jQuery(document).ready(function($){
    $(document).on("click", '.open-pdf-popup-btn', function(event) {
        event.preventDefault();
        
        $.fancybox.open({
            src: $('.pdf-thumbnails'),
            type: 'inline',
            fitToView: false,
            autoSize: true,
            autoDimensions: false,
            clickOutside: false, 
            drag:false,
            touch:false,
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
        $('.pdf-thumbnails-container').removeClass('hidden');
    });
});