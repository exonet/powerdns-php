<?php

declare(strict_types=1);

namespace Exonet\Powerdns\Resources;

class Comment
{
    /**
     * @var string The actual comment.
     */
    private $content;

    /**
     * @var string Name of an account that added the comment.
     */
    private $account;

    /**
     * @var int Timestamp of the last change to the comment.
     */
    private $modifiedAt;

    /**
     * Get the content.
     *
     * @return string The actual comment.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Set the content.
     *
     * @param string $content The content.
     *
     * @return $this The current Comment instance.
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the name of an account that added the comment.
     *
     * @return string Name of an account that added the comment.
     */
    public function getAccount(): string
    {
        return $this->account;
    }

    /**
     * Set the name of the account who created this comment.
     *
     * @param string $account The account name.
     *
     * @return $this The current Comment instance.
     */
    public function setAccount(string $account): self
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get the timestamp of the last change to the comment.
     *
     * @return int Timestamp of the last change to the comment.
     */
    public function getModifiedAt(): int
    {
        return $this->modifiedAt;
    }

    /**
     * Set the timestamp of the last change to the comment.
     *
     * @param int $modifiedAt The timestamp.
     *
     * @return $this The current Comment instance.
     */
    public function setModifiedAt(int $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }
}
