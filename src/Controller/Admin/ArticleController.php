<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Dto\User\UpdateArticleByAdminDto;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Image;

#[Route('/api/admin/articles', name: 'api_admin_articles_')]
class ArticleController extends AbstractController
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private readonly EntityManagerInterface $em

    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json(
            $this->articleRepository->findAll(),
            Response::HTTP_OK,
            context: ['groups' => ['common:index', 'articles:index', 'articles:show']]
        );

    }

    //    //create
    // #[Route('', name: 'create', methods: ['POST'])]
    // public function create()


    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Article $article): JsonResponse
    {
        return $this->json(
            $article,
            Response::HTTP_OK,
            context: ['groups' => ['common:index', 'articles:index', 'articles:show']]
        );
    }




    //! update
    // #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    // public function update(
    //     Article $article,
    //     #[MapRequestPayload]
    //     UpdateArticleByAdminDto $dto,
    // ): JsonResponse {
    //     $this->articleMapper->map($dto, $article);

    //     $this->em->flush();

    //     return $this->json(
    //         $article,
    //         Response::HTTP_OK,
    //         context: ['groups' => ['common:index', 'articles:index', 'articles:show']]
    //     );
    // }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Article $article): JsonResponse
    {
        $this->em->remove($article);
        $this->em->flush();

        return $this->json(
            null,
            Response::HTTP_NO_CONTENT,
        );
    }

    #[Route('/{id}/upload', name: 'upload', methods: ['POST'])]
    public function upload(
        Article $article,
        #[MapUploadedFile(
            new Image(
                maxSize: '8M',
                maxSizeMessage: 'The image is too large. Maximum size is {{ limit }} {{ suffix }}.',
                mimeTypes: [
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'image/webp',
                    'image/svg+xml',
                    'image/jpg',
                    'image/avif',

                ],
                mimeTypesMessage: 'Please upload a valid image (jpeg, png, gif, webp, svg, jpg, avif).',
                detectCorrupted: true,
            )
        )]
        UploadedFile $image
    ): JsonResponse {
        $article->setImageFile($image);

        $this->em->flush();

        return $this->json(
            $article,
            Response::HTTP_OK,
            context: ['groups' => ['common:index', 'articles:index', 'articles:show']]
        );
    }

}



