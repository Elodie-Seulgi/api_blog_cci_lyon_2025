<?php

namespace App\Dto\Article;

use App\Entity\User;
use App\Dto\Interfaces\ArticleRequestInterface;
use App\Entity\Article;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity(
    fields: ['title'],
    entityClass: Article::class,
    message: 'Ce titre existe déjà.',
)]
class CreateArticleDto implements ArticleRequestInterface
{
    public function __construct(
        #[Assert\NotBlank(
            message: 'Le titre est requis',
        )]
        #[Assert\Length(
            min: 3,
            max: 255,
            minMessage: 'Le titre doit contenir au moins {{ limit }} caractères',
            maxMessage: 'Le titre ne peux dépasser {{ limit }} caractères',
        )]
        private readonly ?string $title = null,

        #[Assert\NotBlank(
            message: 'Le contenu ne peut être vide',
        )]
        #[Assert\Length(
            min: 10,
            minMessage: 'Le contenu doit contenir au moins {{ limit }} caractères',
        )]
        private readonly ?string $content = null,


        #[Assert\NotBlank(
            message: 'La preview ne peut être vide',
        )]
        #[Assert\Length(
            min: 5,
            max: 255,
            minMessage: 'La preview  doit contenir au moins {{ limit }} caractères',
            maxMessage: 'La preview  ne peux dépasser {{ limit }} caractères',
        )]
        private readonly ?string $shortContent = null,

        private readonly bool $enable = false,

        #[Assert\NotBlank(message: 'L\'user ne peut être vide', )]
        #[Assert\Positive(message: 'L\'utilisateur doit etre un identifiant valide', )]
        private readonly ?int $user = null
    ) {
    }

    /**
     * Get the value of title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Get the value of content
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Get the value of ShortContent
     */
    public function getShortContent(): ?string
    {
        return $this->shortContent;
    }

    /**
     * Get the value of enable
     */
    public function isEnable(): bool
    {
        return $this->enable;
    }

    /**
     * Get the value of user
     */
    public function getUser(): ?int
    {
        return $this->user;
    }
}