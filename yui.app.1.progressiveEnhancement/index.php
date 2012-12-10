<?php
/**
 * This file is a tutorial for YUI Application framework used for a very simple progressive enhancement
 * single page website using pjax.
 * @author Philippe Le Van (twitter : @plv)
 */
// extract page name and rootUrl
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$pathTab = explode('/', $path);
$path = array_pop($pathTab);
$rootUrl = implode('/', $pathTab);
$path = str_replace('\\', '', $path);
$fileName = dirname(__FILE__).'/page/'.substr($path, 5).'.php';


// little routing system
$result = null;
if ( (strpos($path, 'page-')===0) && is_file($fileName) ) {
    ob_start();
    include ($fileName);
    $result = ob_get_clean();
}

// check if the request is a PJAX or a standard request
$displayTemplate = true;
$headers = apache_request_headers();
if (isset($headers["X-PJAX"])) {
    $displayTemplate = false;
}
?>

<?php if ($displayTemplate) { // Display entire page, if not a pjax request ?>
<html>
    <head>
        <title>Tuto Y.App</title>
        <script src="http://yui.yahooapis.com/3.8.0pr2/build/yui/yui-min.js"></script>
    </head>
    <body>
        <h1>Tuto Y.App</h1>
        <h2>navigation</h2>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="page-about">About</a></li>
            <li><a href="page-list">List</a></li>
        </ul>
        <h2>Contenu à changer</h2>
        <div id="content" style="border: 1px solid #DDD; padding: 5px;background-color: #F8F8F8;">
 <?php } ?>

<!-- real content of the page -->
<?php if ($displayTemplate) { echo "<noscript>";} ?>
<div style="background-color: <?php echo $displayTemplate ? "#FFFF80": "#80FF80"?>; padding: 2px;margin: 5px;">
    <pre style="font-size: 0.8em;color: #999;">
    <?php echo "path=$path, file=$fileName"; ?>
    </pre>
    <?php if ($result) { echo $result; } else { ?>
        Page d'accueil
    <?php } ?>
</div>
<?php if ($displayTemplate) { echo "</noscript>";} ?>
<!-- / real content of the page -->

<?php if ($displayTemplate) { // display if the request is not a pjax request ?>
        </div>
        <script type="text/javascript">
            YUI().use('app', 'node', function (Y) {
                var app = new Y.App({
                    views: {
                        '/index.php': {},
                        '/page-about': {},
                        '/page-list': {},
                        '/page-detail': {parent:"/page-list"}
                    },
                    transitions: true,
                    viewContainer: '#content'
                });
                app.set("root", "<?php echo $rootUrl; ?>");
                app.route('*', 'loadContent', function(req, res, next) {
                    this.showContent(res.content.node, {view: req.path});
                });
                app.render().dispatch();
            });
        </script>
    </body>
</html>
<?php } ?>