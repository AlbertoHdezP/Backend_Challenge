<?php

use Symfony\Component\Routing;

$routes = new Routing\RouteCollection();
$routes->add('leap_year', new Routing\Route('/is_leap_year/{year}', [
    'year' => null,
    '_controller' => 'Crimsoncircle\Controller\LeapYearController::index',
]));

$routes->add('blog_create', new Routing\Route('/blog', [
    '_controller' => 'Crimsoncircle\Controller\BlogPostController::create',
]));

$routes->add('blog_findordelete', new Routing\Route('/blog/{slug}', [
    'slug' => null,
    '_controller' => 'Crimsoncircle\Controller\BlogPostController::findOrDeteleForSlug',
]));

$routes->add('comment', new Routing\Route('/comment', [
    '_controller' => 'Crimsoncircle\Controller\CommentController::create',
]));

$routes->add('comment_findordelete', new Routing\Route('/comment/{id}', [
    '_controller' => 'Crimsoncircle\Controller\CommentController::findOrDeteleForId',
]));

$routes->add('comment_post', new Routing\Route('/comment/post/{post_id}', [
    '_controller' => 'Crimsoncircle\Controller\CommentController::getCommentsForPost',
]));

return $routes;

