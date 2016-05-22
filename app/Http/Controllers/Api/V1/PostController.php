<?php namespace Nht\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Nht\Http\Controllers\Api\ApiController;
use Nht\Http\Transformers\PostTransformer;
use Nht\Hocs\Posts\PostRepository;

class PostController extends ApiController
{
    /**
     * Post repository
     * @var PostRepository
     */
    protected $model;

    /**
     * Post transformer
     * @var PostTransformer
     */
    protected $transformer;

    /**
     * Validation rules
     * @var array
     */
    protected $validationRules = [
        'title' => 'required'
    ];

    /**
     * Constructor
     * @param PostRepository  $post
     * @param PostTransformer $transformer
     */
    public function __construct(PostRepository $post, PostTransformer $transformer)
    {
        $this->model = $post;
        $this->transformer = $transformer;
    }

    /**
     * Get all post
     * @return json
     */
    public function index()
    {
    	return $this->listResponse($this->model->getAll(), $this->transformer);
    }

    /**
     * Get a specify post
     * @param  int $id      Post ID
     * @return json
     */
    public function show($id)
    {
        $post = $this->model->getById($id);
        return $this->showResponse($post, $this->transformer);
    }

    /**
     * Create a new post
     * @param  Request $request
     * @return json
     */
    public function store(Request $request)
    {
        if ($v = $this->validRequest($request, $this->validationRules)) {
            return $this->clientErrorResponse($v);
        }
        $post = $this->model->store($request->all());
        return $this->showResponse($post, $this->transformer);
    }

    /**
     * Edit a post
     * @param  Request $request
     * @param  int  $id     Post ID
     * @return json
     */
    public function update(Request $request, $id)
    {
        if ($v = $this->validRequest($request, $this->validationRules)) {
            return $this->clientErrorResponse($v);
        }
        $post = $this->model->update($id, $request->all());
    	return $this->showResponse($post, $this->transformer);
    }

    /**
     * Delete a post
     * @param  int $id      Post ID
     * @return json
     */
    public function destroy($id)
    {
    	$this->model->delete($id);
    	return $this->deletedResponse();
    }
}
