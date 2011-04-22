<?php
error_reporting(E_ALL);

require __DIR__.'/silex.phar';

$app = new Silex\Application();

$app['db'] = $app->share(function () {
    $dbh = new PDO('sqlite:'.__DIR__.'/db/bookmarks.db3', null, null, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $dbh->exec("CREATE TABLE IF NOT EXISTS bookmarks (id INTEGER PRIMARY KEY, bookmark VARCHAR(255), created_at TIMESTAMP)");
    return $dbh;
});

$app->get('/new', function() use ($app) { 
    $request = $app['request'];
    $url = $request->get('url');
    $url = urldecode($url);
    $app['db']->prepare("INSERT INTO bookmarks (bookmark, created_at) VALUES (?, DATETIME('NOW'))")->execute(array($url));
    return $app->escape($url)." Bookmarked";
}); 

$app->get('/', function() use ($app) { 
    $request = $app['request'];
    $num = $request->get('num');
    $rcount = $app['db']->query('SELECT COUNT (*) as num FROM bookmarks');
    $count = $rcount->fetch();
    $num = ($num && $num <= $count['num']) ? $num : 0;
    $res = $app['db']->query('SELECT * FROM bookmarks ORDER BY created_at DESC LIMIT 1 OFFSET '.$num);
    $row = $res->fetch();
    return '<!DOCTYPE html>
        <html><head>  
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

        <style type="text/css" media="all">
    html, body {
      height: 100%
    }
    body {
      background-color: #1a1a1a;
      margin: 0;
      overflow: hidden;
      color: #999;
      font-size: 12px;
    }
    #topbar {
      height: 50px;
      width: 100%;
      border-bottom: 1px solid #666
    }
    #iframe {
      height: 100%;
      width: 100%;
      border-width: 0
    }
    #logo {
      padding-top: 4px;
      padding-left: 10px;
      height: 45px;
      width: 90px;
      float: left;
    }
    #title {
        margin: 0 auto;
        width: 800px;
    }
    body a{
        color: #aaa;
        text-decoration: none;
        font-weight: bold;
        font-size: 16px;
    }
    body a:hover {
        color: #bbb;
        text-decoration: underline;
    }
    #nav{
        float:right;
        padding-top: 10px;
        padding-right: 10px;
    }
    #nav a{
        font-size: 24px;
    }
  </style>
</head>
<body>

<div id="topbar"><div id="logo"><img src="logo.png"/></div><div id="nav">'.($num > $count['num']?'':'<a href="?num='.($num+1).'">«</a>').($num <= 0?'':'<a href="?num='.($num-1).'">»</a>').'</div><div id="title"><a href="'.$row['bookmark'].'">'.$row['bookmark'].'</a>&nbsp;'.$row['created_at'].'</div></div><iframe id="iframe" src="'.$row['bookmark'].'" frameborder=0 noresize="noresize"> </iframe></body></html>'; 
}); 
// definitions

$app->run();

