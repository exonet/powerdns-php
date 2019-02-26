<?php

namespace Exonet\Powerdns\tests\Resources;

use Exonet\Powerdns\Resources\Comment;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function testSettersAndGetters() : void
    {
        $comment = new Comment();

        $comment
            ->setAccount('test account')
            ->setContent('test content')
            ->setModifiedAt(1234);

        $this->assertSame('test account', $comment->getAccount());
        $this->assertSame('test content', $comment->getContent());
        $this->assertSame(1234, $comment->getModifiedAt());
    }
}
