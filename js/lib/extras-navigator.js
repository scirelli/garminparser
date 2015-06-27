"use strict";//Just don't cross the streams!!

//------------------------------------------------
// Returns the version of Internet Explorer or a -1
// (indicating the use of another browser).
// TODO: Create a better way to determine IE
//------------------------------------------------
navigator.getInternetExplorerVersion = function()
{
    var rv = -1; // Return value assumes failure.
    if (navigator.appName == 'Microsoft Internet Explorer')
    {
        var ua = navigator.userAgent;
        var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
        if (re.exec(ua) != null)
        rv = parseFloat( RegExp.$1 );
    }
    return rv;
}
