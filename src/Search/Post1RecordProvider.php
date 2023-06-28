<?php

namespace App\Search;

use Zenstruck\Algolia\Search\Index\AsRecordProvider;
use Zenstruck\Algolia\Search\Record;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsRecordProvider('post')]
final class Post1RecordProvider implements \IteratorAggregate
{
    public function getIterator(): \Traversable
    {
        yield new Record('post_1', [
            'title' => 'Post 1',
            'content' => 'Post 1 content',
        ]);

        yield new Record('post_2', [
            'title' => 'Post 2',
            'content' => 'Post 2 content',
        ]);
    }
}
