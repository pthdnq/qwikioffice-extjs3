/**
 * FF error fox : Permission denied to access property 'dom' from a non-chrome context
 * @link http://www.extjs.com/forum/showthread.php?t=74765&page=6
 * 
 */
Ext.override(Ext.Element, {
    contains: function() {
    	try {
        var isXUL = Ext.isGecko ? function(node) {
            return Object.prototype.toString.call(node) == '[object XULElement]';
        } : Ext.emptyFn;

        return function(el) {
            return !this.dom.firstChild || // if this Element has no children, return false immediately
                   !el ||
                   isXUL(el) ? false : Ext.lib.Dom.isAncestor(this.dom, el.dom ? el.dom : el);
        };
    	}catch(e){}
    }(),
});