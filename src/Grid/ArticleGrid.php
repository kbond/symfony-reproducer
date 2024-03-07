<?php

namespace App\Grid;

use App\Entity\Article;
use Zenstruck\Collection\Grid\GridBuilder;
use Zenstruck\Collection\Grid\GridDefinition;
use Zenstruck\Collection\Symfony\Attributes\ForObject;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[ForObject(Article::class)]
final class ArticleGrid implements GridDefinition
{
    public function configure(GridBuilder $builder): void
    {
        $builder
            ->addColumn('id', defaultSort: 'asc')
            ->addColumn('title', searchable: true, autofilter: true)
            ->addColumn('description', searchable: true, autofilter: true)
            ->addColumn('body', searchable: true)
            ->addColumn('publishedAt',
                sortable: true,
                autofilter: true,
            )
            ->addColumn('views', sortable: true, autofilter: true)
        ;
    }
}
