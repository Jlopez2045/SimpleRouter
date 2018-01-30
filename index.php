<?php 
require_once 'router/router.php';
//require_once 'user.php';
$router = new Router();

$router->map(
	"/",
	 function(){
		 echo "Homepage";
	 	//require 'app/views/home.php';
});

$router->map(
	"/About",
	 function(){
	 	echo "About";
});

$router->map(
	"/Contact",
	 function(){
	 	echo "Contact";
});

$router->map(
	"/Products",
	 function(){
	 	//require_once 'app/views/product.php';
});

$router->map(
	"/article/[a:articleid]/post/[s:slug]",
	 function($articleid, $slug){
	 	echo "Article ID: $articleid, Slug of the Article: $slug";
});

$router->map(
	"/post/[i:year]/[i:month]",
	 function($year, $month){
	 	echo 'post from date: ' . $year . '/' . $month;
});

$router->map("/redirect", function(){
	if(isset($_GET['url'])){
		Router::redirect($_GET['url']);
	}
});
/*
$router->map(
	"/user/[a:userid]/[a:username]",
	'User::show'
);*/
$router->respond();

$router->getRoutes("printr");
?>