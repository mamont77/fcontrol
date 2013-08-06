var fControl = fControl || { 'settings':{}, 'behaviors':{}, 'locale':{} };

// Allow other JavaScript libraries to use $.
jQuery.noConflict();

(function ($) {

    $("[rel='tooltip']").tooltip();

    /**
     * Override jQuery.fn.init to guard against XSS attacks.
     *
     * See http://bugs.jquery.com/ticket/9521
     */
    var jquery_init = $.fn.init;
    $.fn.init = function (selector, context, rootjQuery) {
        // If the string contains a "#" before a "<", treat it as invalid HTML.
        if (selector && typeof selector === 'string') {
            var hash_position = selector.indexOf('#');
            if (hash_position >= 0) {
                var bracket_position = selector.indexOf('<');
                if (bracket_position > hash_position) {
                    throw 'Syntax error, unrecognized expression: ' + selector;
                }
            }
        }
        return jquery_init.call(this, selector, context, rootjQuery);
    };
    $.fn.init.prototype = jquery_init.prototype;

    /**
     * Attach all registered behaviors to a page element.
     *
     * Behaviors are event-triggered actions that attach to page elements, enhancing
     * default non-JavaScript UIs. Behaviors are registered in the Drupal.behaviors
     * object using the method 'attach' and optionally also 'detach' as follows:
     * @code
     *    Drupal.behaviors.behaviorName = {
 *      attach: function (context, settings) {
 *        ...
 *      },
 *      detach: function (context, settings, trigger) {
 *        ...
 *      }
 *    };
     * @endcode
     *
     * Drupal.attachBehaviors is added below to the jQuery ready event and so
     * runs on initial page load. Developers implementing AHAH/Ajax in their
     * solutions should also call this function after new page content has been
     * loaded, feeding in an element to be processed, in order to attach all
     * behaviors to the new content.
     *
     * Behaviors should use
     * @code
     *   $(selector).once('behavior-name', function () {
 *     ...
 *   });
     * @endcode
     * to ensure the behavior is attached only once to a given element. (Doing so
     * enables the reprocessing of given elements, which may be needed on occasion
     * despite the ability to limit behavior attachment to a particular element.)
     *
     * @param context
     *   An element to attach behaviors to. If none is given, the document element
     *   is used.
     * @param settings
     *   An object containing settings for the current context. If none given, the
     *   global Drupal.settings object is used.
     */
    fControl.attachBehaviors = function (context, settings) {
        context = context || document;
        settings = settings || fControl.settings;
        // Execute all of them.
        $.each(fControl.behaviors, function () {
            if ($.isFunction(this.attach)) {
                this.attach(context, settings);
            }
        });
    };

    /**
     * Detach registered behaviors from a page element.
     *
     * Developers implementing AHAH/Ajax in their solutions should call this
     * function before page content is about to be removed, feeding in an element
     * to be processed, in order to allow special behaviors to detach from the
     * content.
     *
     * Such implementations should look for the class name that was added in their
     * corresponding Drupal.behaviors.behaviorName.attach implementation, i.e.
     * behaviorName-processed, to ensure the behavior is detached only from
     * previously processed elements.
     *
     * @param context
     *   An element to detach behaviors from. If none is given, the document element
     *   is used.
     * @param settings
     *   An object containing settings for the current context. If none given, the
     *   global Drupal.settings object is used.
     * @param trigger
     *   A string containing what's causing the behaviors to be detached. The
     *   possible triggers are:
     *   - unload: (default) The context element is being removed from the DOM.
     *   - move: The element is about to be moved within the DOM (for example,
     *     during a tabledrag row swap). After the move is completed,
     *     Drupal.attachBehaviors() is called, so that the behavior can undo
     *     whatever it did in response to the move. Many behaviors won't need to
     *     do anything simply in response to the element being moved, but because
     *     IFRAME elements reload their "src" when being moved within the DOM,
     *     behaviors bound to IFRAME elements (like WYSIWYG editors) may need to
     *     take some action.
     *   - serialize: When an Ajax form is submitted, this is called with the
     *     form as the context. This provides every behavior within the form an
     *     opportunity to ensure that the field elements have correct content
     *     in them before the form is serialized. The canonical use-case is so
     *     that WYSIWYG editors can update the hidden textarea to which they are
     *     bound.
     *
     * @see fControl.attachBehaviors
     */
    fControl.detachBehaviors = function (context, settings, trigger) {
        context = context || document;
        settings = settings || fControl.settings;
        trigger = trigger || 'unload';
        // Execute all of them.
        $.each(fControl.behaviors, function () {
            if ($.isFunction(this.detach)) {
                this.detach(context, settings, trigger);
            }
        });
    };

// Class indicating that JS is enabled; used for styling purpose.
    $('html').addClass('js');

// 'js enabled' cookie.
    document.cookie = 'has_js=1; path=/';

    /**
     * Additions to jQuery.support.
     */
    $(function () {
        /**
         * Boolean indicating whether or not position:fixed is supported.
         */
        if (jQuery.support.positionFixed === undefined) {
            var el = $('<div style="position:fixed; top:10px" />').appendTo(document.body);
            jQuery.support.positionFixed = el[0].offsetTop === 10;
            el.remove();
        }
    });

//Attach all behaviors.
    $(function () {
        fControl.attachBehaviors(document, fControl.settings);
    });

})(jQuery);
