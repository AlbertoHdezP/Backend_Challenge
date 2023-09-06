<?php

namespace Crimsoncircle\Controller;

use Crimsoncircle\Model\Blog;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BlogController
{
    public function index()
    {
        $blogs = new Blog();

        foreach ($blogs->getBlogs() as $blog) ;
        {
            $data[] = [
                'title' => $blog->title
            ];
        }

        header('Content-type: application/json');
        return json_encode($data);
    }

    public function create(Request $request): JsonResponse
    {


        $blog = new Blog();
        $blog->setTitle($request->request->get('title'));
        $blog->setContent($request->request->get('content'));
        $blog->setAuthor($request->request->get('author'));
        $blog->setSlug($request->request->get('slug'));

        $entityManager->persist($blog);
        $entityManager->flush();

        $data = [
            'id' => $blog->getId(),
            'title' => $blog->getTitle(),
            'content' => $blog->getContent(),
            'author' => $blog->getAuthor(),
            'slug' => $blog->getSlug(),
        ];

        return $this->json($data);
    }

}