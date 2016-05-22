<?php namespace Nht\Hocs\Bills;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DbBillRepository implements BillRepository
{
    /**
     * Bill model
     * @var Eloquent
     */
    protected $model;

    /**
     * Constructor
     * @param Bill $bill
     */
    public function __construct(Bill $bill)
    {
        $this->model = $bill;
    }

    /**
     * Get all Bill
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get a specify Bill
     * @param  int $id Bill ID
     * @return Bill
     */
    public function getById($id)
    {
        if (!$bill = $this->model->find($id))
        {
            throw new NotFoundHttpException('Bill not found');
        }
        return $bill;
    }

    /**
     * Create a Bill
     * @param  array $data
     * @return new Bill
     */
    public function store($data)
    {
        $bill = $this->model->create($data);
        return $bill;
    }

    /**
     * Update a Bill
     * @param  int $id Bill ID
     * @param  array $data
     * @return Bill
     */
    public function update($id, $data)
    {
        $bill = $this->getById($id);
        $bill->fill($data)->save();
        return $bill;
    }

    /**
     * Delete a Bill
     * @param  int $id Bill ID
     * @return bool
     */
    public function delete($id)
    {
        $bill = $this->getById($id);
        return $bill->delete();
    }
}
