
window.Tippy = require('tippy.js').default;
import 'tippy.js/dist/tippy.css';
import 'tippy.js/animations/scale.css';
import 'tippy.js/animations/scale-subtle.css';
import 'tippy.js/animations/scale-extreme.css';

function initTippy() {
    new Tippy('[data-tippy-content]', {
        allowHTML: true,
        animation: 'scale',
        arrow: true
    });
}
$(document).ready(function() {
    initTippy();
    $('body').on('init.dt', function(e, ctx) {
        initTippy();
    });
});