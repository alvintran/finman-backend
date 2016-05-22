<?php namespace Nht\Hocs\Boards;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DbBoardRepository implements BoardRepository
{
    /**
     * Board model
     * @var Eloquent
     */
    protected $model;

    /**
     * Constructor
     * @param Board $board
     */
    public function __construct(Board $board)
    {
        $this->model = $board;
    }

    /**
     * Get all Board
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get a specify Board
     * @param  int $id Board ID
     * @return Board
     */
    public function getById($id)
    {
        if (!$board = $this->model->find($id))
        {
            throw new NotFoundHttpException('Board not found');
        }
        return $board;
    }

    /**
     * Create a Board
     * @param  array $data
     * @return new Board
     */
    public function store($data)
    {
        $board = $this->model->create($data);
        return $board;
    }

    /**
     * Update a Board
     * @param  int $id Board ID
     * @param  array $data
     * @return Board
     */
    public function update($id, $data)
    {
        $board = $this->getById($id);
        $board->fill($data)->save();
        return $board;
    }

    /**
     * Delete a Board
     * @param  int $id Board ID
     * @return bool
     */
    public function delete($id)
    {
        $board = $this->getById($id);
        return $board->delete();
    }
}
