<?php namespace Nht\Hocs\Posts;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DbPostRepository implements PostRepository
{
    /**
     * Post model
     * @var Eloquent
     */
    protected $model;

    /**
     * Constructor
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->model = $post;
    }

    /**
     * Get all post
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get a specify post
     * @param  int $id Post ID
     * @return Post
     */
    public function getById($id)
    {
        if (!$post = $this->model->find($id))
        {
            throw new NotFoundHttpException('Post not found');
        }
        return $post;
    }

    /**
     * Create a post
     * @param  array $data
     * @return new Post
     */
    public function store($data)
    {
        $post = $this->model->create($data);
        return $post;
    }

    /**
     * Update a post
     * @param  int $id Post ID
     * @param  array $data
     * @return Post
     */
    public function update($id, $data)
    {
        $post = $this->getById($id);
        $post->fill($data)->save();
        return $post;
    }

    /**
     * Delete a post
     * @param  int $id Post ID
     * @return bool
     */
    public function delete($id)
    {
        $post = $this->getById($id);
        return $post->delete();
    }
}
