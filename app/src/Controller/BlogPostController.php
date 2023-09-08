<?php

namespace Crimsoncircle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Crimsoncircle\Entity\BlogPost;

class BlogPostController
{
    public function create(Request $request): JsonResponse
    {

        if ($request->getMethod() !== 'POST') {
            return new JsonResponse(['Error' => 'Method invalidate']);
        }

        require_once '../app/orm.php';

        $blogPost = new BlogPost();
        $blogPost->setTitle($request->request->get('title'));
        $blogPost->setContent($request->request->get('content'));
        $blogPost->setAuthor($request->request->get('author'));
        $blogPost->setSlug($request->request->get('slug'));
        $blogPost->setCreatedAt(new \DateTime());

        $entityManager->persist($blogPost);

        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            $msg = '### Message ### \n' . $e->getMessage() . '\n### Trace ### \n' . $e->getTraceAsString();
            return new JsonResponse(['Error' => $msg]);
        }

        $data = [
            'title' => $blogPost->getTitle(),
            'content' => $blogPost->getContent(),
            'author' => $blogPost->getAuthor(),
            'slug' => $blogPost->getSlug(),
        ];

        return new JsonResponse($data);
    }

    public function findOrDeteleForSlug(Request $request, string $slug = null): JsonResponse
    {

        $data = ['Error' => 'Method invalidate'];

        if ($request->isMethod('GET')) {
            $data = $this->findForSlug($slug);
        }

        if ($request->isMethod('DELETE')) {
            $data = $this->deleteForSlug($slug);
        }

        return new JsonResponse($data);
    }

    public function findForSlug($slug)
    {
        require_once '../app/orm.php';

        $blogPost = $entityManager->getRepository(BlogPost::class)->findOneBy(array('slug' => '/' . $slug));

        if (empty($blogPost)) {
            return ['Error' => 'Not find: ' . $slug];
        }

        $data = [
            'id' => $blogPost->getId(),
            'title' => $blogPost->getTitle(),
            'content' => $blogPost->getContent(),
            'author' => $blogPost->getAuthor(),
            'slug' => $blogPost->getSlug(),
            'created_at' => $blogPost->getCreatedAt()->format('Y-m-d H:i:s'),
        ];

        return $data;
    }

    public function deleteForSlug($slug)
    {
        require_once '../app/orm.php';

        $blogPost = $entityManager->getRepository(BlogPost::class)->findOneBy(array('slug' => '/' . $slug));

        if (empty($blogPost)) {
            return ['Error' => 'Not find: ' . $slug];
        }

        $data = [
            'message' => 'delete element',
            'id' => $blogPost->getId(),
            'slug' => $blogPost->getSlug()
        ];

        $entityManager->remove($blogPost);

        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            $msg = '### Message ### \n' . $e->getMessage() . '\n### Trace ### \n' . $e->getTraceAsString();
            return ['Error' => $msg];
        }

        return $data;
    }
}