<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class TweetReplyDTO
{
    /**
     * @Assert\Type("string", groups={"create"})
     * @Assert\NotNull(groups={"create"})
     */
    public $message;
}