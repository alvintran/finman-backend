<?php

namespace Nht\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Nht\Http\Controllers\Api\ApiController;
use Nht\Http\Transformers\BillTransformer;
use Nht\Hocs\Bills\BillRepository;

class BillController extends ApiController
{
    /**
     * Bill repository
     * @var BillRepository
     */
    protected $model;

    /**
     * Bill transformer
     * @var BillTransformer
     */
    protected $transformer;

    /**
     * Validation rules
     * @var array
     */
    protected $validationRules = [
        'payer_id' => 'required|numeric|min:1',
        'board_id' => 'required|numeric|min:1',
        'amount'   => 'required|numeric|min:500',
    ];

    /**
     * Constructor
     * @param BillRepository  $bill
     * @param BillTransformer $transformer
     */
    public function __construct(BillRepository $bill, BillTransformer $transformer)
    {
        $this->model = $bill;
        $this->transformer = $transformer;
    }

    /**
     * Get all Bill
     * @return json
     */
    public function index()
    {
        return $this->listResponse($this->model->getAll(), $this->transformer);
    }

    /**
     * Get a specify Bill
     * @param  int $id      Bill ID
     * @return json
     */
    public function show($id)
    {
        $bill = $this->model->getById($id);
        return $this->showResponse($bill, $this->transformer);
    }

    /**
     * Create a new Bill
     * @param  Request $request
     * @return json
     */
    public function store(Request $request)
    {
        // Check has payees
        if ($request->only('payees') && ! $request->only('payees')['payees'])
        {
            return $this->clientErrorResponse([
                'form_validations' => ['payees' => 'The payees must be at least 1 person.']
            ]);
        }

        if ($v = $this->validRequest($request, $this->validationRules)) {
            return $this->clientErrorResponse($v);
        }

        $bill = $this->model->store($request->except('payees'));
        return $this->showResponse($bill, $this->transformer);
    }

    /**
     * Edit a Bill
     * @param  Request $request
     * @param  int  $id     Bill ID
     * @return json
     */
    public function update(Request $request, $id)
    {
        // Check has payees
        if ($request->only('payees') && ! $request->only('payees')['payees'])
        {
            return $this->clientErrorResponse([
                'form_validations' => ['payees' => 'The payees must be at least 1 person.']
            ]);
        }

        if ($v = $this->validRequest($request, $this->validationRules)) {
            return $this->clientErrorResponse($v);
        }

        $bill = $this->model->update($id, $request->except('payees'));
        return $this->showResponse($bill, $this->transformer);
    }

    /**
     * Delete a Bill
     * @param  int $id      Bill ID
     * @return json
     */
    public function destroy($id)
    {
        $this->model->delete($id);
        return $this->deletedResponse();
    }
}
