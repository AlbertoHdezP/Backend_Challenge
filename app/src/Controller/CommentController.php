<?php

namespace Crimsoncircle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Crimsoncircle\Entity\Comment;
use Crimsoncircle\Entity\BlogPost;
use Doctrine\ORM\Tools\Pagination\Paginator;

class CommentController
{
    public function create(Request $request): JsonResponse
    {

        if ($request->getMethod() !== 'POST') {
            return new JsonResponse(['Error' => 'Method invalidate']);
        }

        require_once './orm.php';

        $postId = $request->request->get('post_id');

        $blogPost = $entityManager->getRepository(BlogPost::class)->find($postId);

        if (empty($blogPost)) {
            return ['Error' => 'Not post find id: ' . $postId];
        }

        $comment = new Comment();
        $comment->setBlogPost($blogPost);
        $comment->setContent($request->request->get('content'));
        $comment->setAuthor($request->request->get('author'));
        $comment->setCreatedAt(new \DateTime());

        $entityManager->persist($comment);

        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            $msg = '### Message ### \n' . $e->getMessage() . '\n### Trace ### \n' . $e->getTraceAsString();
            return new JsonResponse(['Error' => $msg]);
        }

        $data = [
            'post_id' => $comment->getBlogPost()->getId(),
            'id' => $comment->getId(),
            'content' => $comment->getContent(),
            'author' => $comment->getAuthor(),
        ];

        return new JsonResponse($data);
    }

    public function findOrDeteleForId(Request $request, string $id = null): JsonResponse
    {

        $data = ['Error' => 'Method invalidate'];

        if ($request->isMethod('GET')) {
            $data = $this->findForId($id);
        }

        if ($request->isMethod('DELETE')) {
            $data = $this->deleteForId($id);
        }

        return new JsonResponse($data);
    }

    public function findForId($id)
    {
        require_once './orm.php';

        $comment = $entityManager->getRepository(Comment::class)->find($id);

        if (empty($comment)) {
            return ['Error' => 'Not find: ' . $id];
        }

        $data = [
            'id' => $comment->getId(),
            'post_id' => $comment->getBlogPost()->getId(),
            'content' => $comment->getContent(),
            'author' => $comment->getAuthor(),
            'created_at' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
        ];

        return $data;
    }

    public function deleteForId($id)
    {
        require_once './orm.php';

        $comment = $entityManager->getRepository(Comment::class)->find($id);

        if (empty($comment)) {
            return ['Error' => 'Not find: ' . $id];
        }

        $data = [
            'message' => 'delete element',
            'id' => $comment->getId(),
            'post_id' => $comment->getBlogPost()->getId(),
            'content' => $comment->getContent()
        ];

        $entityManager->remove($comment);

        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            $msg = '### Message ### \n' . $e->getMessage() . '\n### Trace ### \n' . $e->getTraceAsString();
            return ['Error' => $msg];
        }

        return $data;
    }

    public function getCommentsForPost(Request $request, int $post_id): JsonResponse
    {
        if ($request->getMethod() !== 'GET') {
            return new JsonResponse(['Error' => 'Method invalidate']);
        }
        $page = 1;
        $limit = 10;
        if ($request->query->get('page')) {

            $page = $request->query->get('page');
        }

        require_once './orm.php';

        $dql = "SELECT c FROM Crimsoncircle\Entity\Comment as c WHERE c.blogPost = :post_id";
        $query = $entityManager->createQuery($dql)
            ->setParameter('post_id', $post_id)
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        $paginator = new Paginator($query, $fetchJoinCollection = false);

        $data = [];

        foreach ($paginator as $comment) {
            $data[] = [
                'id' => $comment->getId(),
                'post_id' => $comment->getBlogPost()->getId(),
                'content' => $comment->getContent(),
                'author' => $comment->getAuthor(),
                'created_at' => $comment->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        }

        return new JsonResponse($data);
    }
}