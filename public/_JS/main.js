/**
 * Created by los on 9/25/16.
 */
$(function () {
    var StickyFooter = {
        init: function ()
        {
            StickyFooter.compareWindow2Body();
            $(window).resize(function () { StickyFooter.compareWindow2Body(); })
        },
        compareWindow2Body: function ()
        {
            if ($(window).height() > $('body').height()) { StickyFooter.addSticky(); }
            else { StickyFooter.removeSticky(); }
        },
        addSticky: function () { $('body > footer').addClass('StickyFooter'); },
        removeSticky: function () { $('body > footer').removeClass('StickyFooter'); }
    };
    StickyFooter.init();
});