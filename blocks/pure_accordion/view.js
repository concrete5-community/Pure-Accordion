document.addEventListener('DOMContentLoaded', function() {

function Accordion(el)
{
    if (el._pureAccordion) {
        return;
    }
    var my = this;
    el._pureAccordion = my;
    my.el = el;
    my.groupHandle = el.getAttribute('data-group') || '';
    my.header = el.querySelector(':scope>.header');
    my.permalink = el.querySelector(':scope>.permalink');
    my.content = el.querySelector(':scope>.content');
    if (!my.header || !my.content) {
        return;
    }
    my.updateContentHeight();
    my.header.addEventListener('click', function(e) {
        my.toggle();
        e.preventDefault();
    });
    if (window.location.hash && my.permalink && my.permalink.href.endsWith(window.location.hash) && !my.el.classList.contains('open')) {
        my.toggle();
    }
    if (window.ResizeObserver) {
        my.resizeObserver = new ResizeObserver(function() {
            if (my.el.classList.contains('open')) {
                my.updateContentHeight();
            }
        });
        my.resizeObserver.observe(my.content);
    }
    Object.defineProperty(my, 'isOpen', {
        get: function() {
            return my.el.classList.contains('open');
        }
    });
}
Accordion.prototype = {
    toggle: function() {
        this.el.classList.toggle('open');
        this.updateContentHeight();
        if (this.groupHandle && this.isOpen) {
            this.closeOthers();
        }
    },
    updateContentHeight: function() {
        if (this.el.classList.contains('open')) {
            this.content.style.maxHeight = this.content.scrollHeight + 'px';
        } else {
            this.content.style.maxHeight =' 0px';
        }
    },
    closeOthers: function() {
        if (!this.groupHandle) {
            return;
        }
        var elements = document.querySelectorAll('.pure-accordion-block-container');
        for (var index = 0; index < elements.length; index++) {
            var accordion = elements[index]._pureAccordion;
            if (accordion && accordion.groupHandle === this.groupHandle && accordion !== this && accordion.isOpen) {
                accordion.toggle();
            }
        }
    },
};

var elements = document.querySelectorAll('.pure-accordion-block-container');
for (var index = 0; index < elements.length; index++) {
    new Accordion(elements[index]);
}

});
