/**
    * Object to handle expanding, collapsing, and closing blocks when an icon is clicked on.
    * @constructor
    * @param String id the HTML id for the div.
    * @param String userpref the user preference that records the state of this block.
    * @param String visibletooltip tool tip/alt to show when the block is visible.
    * @param String hiddentooltip tool tip/alt to show when the block is hidden.
    * @param String visibleicon URL of the icon to show when the block is visible.
    * @param String hiddenicon URL of the icon to show when the block is hidden.
*/
function block_title_controls(id, userpref, visibletooltip, hiddentooltip, visibleicon, hiddenicon) {
    this.block = document.getElementById(id);
    var header = this.block.getElementsByTagName('header');
    if (!header || !header[0]) {
        return this;
    }
    header = header[0];
    this.ishidden = YAHOO.util.Dom.hasClass(this.block, 'hidden');

    // Record the pref name
    this.userpref = userpref;
    this.visibletooltip = visibletooltip;
    this.hiddentooltip = hiddentooltip;
    this.visibleicon = visibleicon;
    this.hiddenicon = hiddenicon;

    // Add the icon.
    this.icon = document.createElement('input');
    this.icon.type = 'image';
    this.icon.className = 'hide-show-image';
    this.update_state();
    header.appendChild(this.icon);

    // Hook up the event handler.
    YAHOO.util.Event.addListener(this.icon, 'click', this.handle_click, null, this);
}

/** Handle click on a block show/hide icon. */
block_title_controls.prototype.handle_click = function(e) {
    YAHOO.util.Event.stopEvent(e);
    this.ishidden = !this.ishidden;
    this.update_state();
    set_user_preference(this.userpref, this.ishidden);
}

/** Set the state of the block show/hide icon to this.ishidden. */
block_title_controls.prototype.update_state = function () {
    if (this.ishidden) {
        YAHOO.util.Dom.addClass(this.block, 'hidden');
        this.icon.alt = this.hiddentooltip;
        this.icon.title = this.hiddentooltip;
        this.icon.src = this.hiddenicon;
    } else {
        YAHOO.util.Dom.removeClass(this.block, 'hidden');
        this.icon.alt = this.visibletooltip;
        this.icon.title = this.visibletooltip;
        this.icon.src = this.visibleicon;
    }
}
