<?php namespace Nht\Hocs\Boards;

interface BoardRepository
{
    public function getAll();
    public function getById($id);
    public function store($data);
    public function update($id, $data);
    public function delete($id);
}
