# SimpleRouter
a very simple router in PHP using regular expressions with personalized parameters

```php
<?php 
require_once 'router/router.php';

$router = new Router;

$router->map("/article/[i:articleid]/post/[s:slug]", function($request, $articleid, $slug){
	echo "Article ID: $articleid, Slug of the Article: $slug";
});

$router->map("/post/[i:year]/[i:month]", function($request, $year, $month){
	echo 'post from date: ' . $year . '/' . $month;
});

$router->respond();
?>
```
