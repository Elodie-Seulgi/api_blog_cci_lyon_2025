<?php

namespace App\Mapper;

use App\Entity\Article;
use App\Dto\Interfaces\ArticleRequestInterface;
use App\Repository\UserRepository;

class ArticleMapper
{

    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }
    public function map(ArticleRequestInterface $dto, Article $article = null): Article
    {
        $article ??= new Article();

        if (null !== $dto->getTitle()) {
            $article->setTitle($dto->getTitle());
        }
        if (null !== $dto->getContent()) {
            $article->setContent($dto->getContent());
        }

        if (null !== $dto->getShortContent()) {
            $article->setShortContent($dto->getShortContent());
        }

        if (null !== $dto->isEnable()) {
            $article->setEnabled($dto->isEnable());
        }

        // if (null !== $dto->getUser()) {
        //     $article->setUser($dto->getUser());


        // }

        return $article;
    }
}