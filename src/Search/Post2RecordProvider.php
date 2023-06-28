<?php

namespace App\Search;

use Zenstruck\Algolia\Search\Index\AsRecordProvider;
use Zenstruck\Algolia\Search\Record;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsRecordProvider('post')]
final class Post2RecordProvider implements \IteratorAggregate, \Countable
{
    public function __toString(): string
    {
        return 'Post 2 Record Provider';
    }

    public function getIterator(): \Traversable
    {
        yield new Record('post_3', [
            'title' => 'Post 3',
            'content' => 'Post 3 content',
        ]);

        yield new Record('post_4', [
            'title' => 'Post 4',
            'content' => 'Post 4 content',
        ]);

        yield new Record('post_5', [
            'title' => 'Post 5',
            'content' => 'Post 5 content',
        ]);
    }

    public function count(): int
    {
        return \iterator_count($this);
    }
}
