<?php 

namespace App\Dto\Interfaces;

interface ArticleRequestInterface
{
    public function getTitle(): ?string;

    public function getContent(): ?string;

    public function getShortContent(): ?string;

    public function isEnable(): ?bool;

    public function getUser(): ?int;
}