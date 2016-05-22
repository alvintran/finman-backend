<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Nht\Hocs\Helpers\NhtFractal;
use Nht\Http\Transformers\PostTransformer;
use Illuminate\Support\Collection;

class NhtFractalTest extends TestCase
{

    use DatabaseTransactions;

    public $fractal;

    public function setUp()
    {
        $this->fractal = app(NhtFractal::class);
        parent::setUp();
    }

    /**
     * Get a transform of Post
     *
     * @return void
     */
    public function testTransformAPostItem()
    {
        $post = factory(Nht\Hocs\Posts\Post::class)->make();
        $expected = $this->fractal->getData($post, new PostTransformer);
        $this->assertEquals([
            'data' => [
                'id' => $post->id,
                'title' => $post->title,
                'body' => $post->body,
                'created' => date('d/m/Y', strtotime($post->created_at)),
                'updated' => date('d/m/Y', strtotime($post->created_at))
            ]
        ], $expected);
    }

    public function testTransformAPostList()
    {
        $posts = factory(Nht\Hocs\Posts\Post::class, 3)->make();
        $postList = new Collection($posts);
        $expected = $this->fractal->getData($postList, new PostTransformer);
        $this->assertEquals([
            'data' => [
                [
                    'id' => $posts[0]->id,
                    'title' => $posts[0]->title,
                    'body' => $posts[0]->body,
                    'created' => date('d/m/Y', strtotime($posts[0]->created_at)),
                    'updated' => date('d/m/Y', strtotime($posts[0]->created_at))
                ],
                [
                    'id' => $posts[1]->id,
                    'title' => $posts[1]->title,
                    'body' => $posts[1]->body,
                    'created' => date('d/m/Y', strtotime($posts[1]->created_at)),
                    'updated' => date('d/m/Y', strtotime($posts[1]->created_at))
                ],
                [
                    'id' => $posts[2]->id,
                    'title' => $posts[2]->title,
                    'body' => $posts[2]->body,
                    'created' => date('d/m/Y', strtotime($posts[2]->created_at)),
                    'updated' => date('d/m/Y', strtotime($posts[2]->created_at))
                ]
            ]
        ], $expected);
    }

    public function testWillNotTransform()
    {
        $post = factory(Nht\Hocs\Posts\Post::class)->make();
        $expected1 = $this->fractal->getData($post, null);
        $this->assertEquals([
            'data' => []
        ], $expected1);

        $posts = factory(Nht\Hocs\Posts\Post::class, 3)->make();
        $expected2 = $this->fractal->getData($posts, null);
        $this->assertEquals([
            'data' => []
        ], $expected2);
    }

    public function testPaginateAList()
    {
        $posts = factory(Nht\Hocs\Posts\Post::class, 3)->make();
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator($posts, 3, 1);
        $expected = $this->fractal->getData($paginator, new PostTransformer);
        $this->assertEquals([
            'data' => [
                [
                    'id' => $posts[0]->id,
                    'title' => $posts[0]->title,
                    'body' => $posts[0]->body,
                    'created' => date('d/m/Y', strtotime($posts[0]->created_at)),
                    'updated' => date('d/m/Y', strtotime($posts[0]->created_at))
                ],
                [
                    'id' => $posts[1]->id,
                    'title' => $posts[1]->title,
                    'body' => $posts[1]->body,
                    'created' => date('d/m/Y', strtotime($posts[1]->created_at)),
                    'updated' => date('d/m/Y', strtotime($posts[1]->created_at))
                ],
                [
                    'id' => $posts[2]->id,
                    'title' => $posts[2]->title,
                    'body' => $posts[2]->body,
                    'created' => date('d/m/Y', strtotime($posts[2]->created_at)),
                    'updated' => date('d/m/Y', strtotime($posts[2]->created_at))
                ]
            ],
            'meta' => [
                "pagination" => [
                    "total" => 3,
                    "count" => 3,
                    "per_page" => 1,
                    "current_page" => 1,
                    "total_pages" => 3,
                    "links" => [
                        "next" => "/?page=2"
                    ]
                ]
            ]
        ], $expected);
    }
}
