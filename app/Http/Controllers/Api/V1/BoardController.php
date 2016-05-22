<?php

namespace Nht\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Nht\Http\Controllers\Api\ApiController;
use Nht\Http\Transformers\BoardTransformer;
use Nht\Hocs\Boards\BoardRepository;
use Tymon\JWTAuth\JWTAuth as Jwt;

class BoardController extends ApiController
{
    /**
     * Board repository
     * @var BoardRepository
     */
    protected $model;

    /**
     * Board transformer
     * @var BoardTransformer
     */
    protected $transformer;

    /**
     * Jwt
     * @var JWT
     */
    protected $jwt;

    /**
     * Validation rules
     * @var array
     */
    protected $validationRules = [
        'name' => 'required'
    ];

    /**
     * Constructor
     * @param BoardRepository  $board
     * @param BoardTransformer $transformer
     */
    public function __construct(BoardRepository $board, BoardTransformer $transformer, Jwt $jwt)
    {
        $this->model = $board;
        $this->transformer = $transformer;
        $this->jwt = $jwt;
    }

    /**
     * Get all Board
     * @return json
     */
    public function index()
    {
        return $this->listResponse($this->model->getAll(), $this->transformer);
    }

    /**
     * Get a specify Board
     * @param  int $id      Board ID
     * @return json
     */
    public function show($id)
    {
        $board = $this->model->getById($id);
        return $this->showResponse($board, $this->transformer);
    }

    /**
     * Create a new Board
     * @param  Request $request
     * @return json
     */
    public function store(Request $request)
    {
        if ($v = $this->validRequest($request, $this->validationRules)) {
            return $this->clientErrorResponse($v);
        }
        // dd(JWTAuth::parseToken()->authenticate());
        if ($user = $this->jwt->parseToken()->authenticate())
        {
            $board = $this->model->store(array_merge($request->all(), ['owner_id' => $user->id]));
            return $this->showResponse($board, $this->transformer);
        }
    }

    /**
     * Edit a Board
     * @param  Request $request
     * @param  int  $id     Board ID
     * @return json
     */
    public function update(Request $request, $id)
    {
        if ($v = $this->validRequest($request, $this->validationRules)) {
            return $this->clientErrorResponse($v);
        }
        $board = $this->model->update($id, $request->all());
        return $this->showResponse($board, $this->transformer);
    }

    /**
     * Delete a Board
     * @param  int $id      Board ID
     * @return json
     */
    public function destroy($id)
    {
        $this->model->delete($id);
        return $this->deletedResponse();
    }
}
