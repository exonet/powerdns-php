<?php

namespace Exonet\Powerdns\tests\Resources;

use Exonet\Powerdns\Resources\Comment;
use Exonet\Powerdns\Transformers\CommentTransformer;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class CommentTest extends TestCase
{
    public function testSettersAndGetters(): void
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

    public function testTransformer(): void
    {
        $comment = (new Comment())
            ->setAccount('test account')
            ->setContent('test content')
            ->setModifiedAt(1234);

        $transformer = new CommentTransformer($comment);

        $this->assertEquals((object) [
            'modified_at' => 1234,
            'account' => 'test account',
            'content' => 'test content'
        ], $transformer->transform());
    }
}
