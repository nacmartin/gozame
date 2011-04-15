README
======

This is a tiny webapp written in [Silex](http://silex-project.org/) to share bookmarks with people in the same room or in the phone. When you want to share a bookmark, simply use this bookmarklet:

    javascript:q=(escape(document.location.href));(function(){window.open('http://example.com/new?url='+q);})();

(Substitute example.com with the url of the app).

When somebody points his browser to the url of the app, he will go to the last bookmark saved. As simple as that.

Setup
===========
You will need silex.phar in the same directory as gozame. Remember to add this directive in apache conf to allow urls as parameters:
   
   AllowEncodedSlashes On

Enjoy!
