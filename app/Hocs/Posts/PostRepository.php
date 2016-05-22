<?php namespace Nht\Hocs\Posts;

interface PostRepository
{
    public function getAll();
    public function getById($id);
    public function store($data);
    public function update($id, $data);
    public function delete($id);
}
