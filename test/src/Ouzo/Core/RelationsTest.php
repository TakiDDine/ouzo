<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Relations;
use Ouzo\Tests\CatchException;
use PHPUnit\Framework\TestCase;

class RelationsTest extends TestCase
{
    /**
     * @test
     * @throws Exception
     */
    public function shouldThrowExceptionIfDuplicatedRelation()
    {
        //given
        $params = [
            'hasOne' => [
                'category' => ['class' => 'Test\Category', 'foreignKey' => 'id_category']
            ],
            'belongsTo' => ['category' => ['class' => 'Test\OrderProduct']]
        ];

        //when
        CatchException::inConstructor(Relations::class, ["class", $params, "id"]);

        //then
        CatchException::assertThat()->isInstanceOf(InvalidArgumentException::class);
    }
}