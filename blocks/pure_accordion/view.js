$(document).ready(function() {

    function toggleAccordion(accordion) {
        var accordionContent = accordion.find('.content');
        accordion = $(accordion);
        if (accordion.hasClass('open')) {
            accordionContent.css('height', accordionContent.height());
            accordionContent.animate({
                height: 0
            }, 0, function() {
                accordionContent.css('height', '');
                accordion.removeClass('open');
            });
           /* accordion.find('.content').animateCss('flipOutX', function(){
                accordion.removeClass('open');
            });*/

        } else {

            accordion.addClass('open');
            var contentHeight = accordionContent.height();
            accordionContent.css('height', 0);
            accordionContent.animate({
                height: contentHeight
            }, 0);
            //accordion.find('.content').animateCss('flipInX');

        }
    }

    var hash = window.location.hash.substr(1);
    if (hash.length) {
        var accordion = $('.pure-accordion-block-container[data-pure-accordion-handle="'+hash+'"]');
        if (!accordion.hasClass('open')) {
            toggleAccordion(accordion);
        }
    }

    var pureAccordions = $('.pure-accordion-block-container');
    if (pureAccordions.length) {
        pureAccordions.each(function(index, accordion) {
            $(accordion).find('.header').on('click', function(){
                var accordionID = $(this).data('pureAccordionId');
                var accordion = $('.pure-accordion-block-container[data-pure-accordion-id="'+accordionID+'"]');
                toggleAccordion(accordion);
            });
        });
    }
});