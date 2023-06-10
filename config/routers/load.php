<?php
// Here is a place for the list of application routers

// When one has a router for making nice human readable URLs to articles,
// the router should be listed here:
//
//  Atk14Url::AddRouter("ArticlesRouter");
//  Atk14Url::AddRouter("blog","ArticlesRouter"); // adding the same ArticlesRouter to a namespace blog

Atk14Url::AddRouter("AdminRouter");

// Keep the DefaultRouter at the end of the list
Atk14Url::AddRouter("DefaultRouter");
