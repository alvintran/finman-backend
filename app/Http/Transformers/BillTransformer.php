<?php namespace Nht\Http\Transformers;

use League\Fractal\TransformerAbstract;
use Nht\Hocs\Bills\Bill;

class BillTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'payer',
        'payees',
        'board',
        'category'
    ];

    /**
     * Transform
     * @param  Bill   $bill
     * @return array
     */
    public function transform(Bill $bill)
    {
        return [
            'id'          => (int) $bill->id,
            'payer_id'    => (int) $bill->payer_id,
            'board_id'    => (int) $bill->board_id,
            'category_id' => (int) $bill->category_id,
            'amount'      => (float) $bill->amount,
            'note'        => $bill->note,
            'created'     => date('d/m/Y', strtotime($bill->created_at)),
            'updated'     => date('d/m/Y', strtotime($bill->updated_at))
        ];
    }

    /**
     * Include payer
     * @param  Bill   $bill
     * @return League\Fractal\ItemResource
     */
    public function includePayer(Bill $bill)
    {
        $payer = $bill->payer();
        return $this->item($payer, new UserTransformer);
    }

    /**
     * Include payees
     * @param  Bill   $bill
     * @return League\Fractal\CollectionResource
     */
    public function includePayees(Bill $bill)
    {
        $payees = $bill->payees();
        return $this->collection($payees, new UserTransformer);
    }

    /**
     * Include board
     * @param  Bill   $bill
     * @return League\Fractal\ItemResource
     */
    public function includeBoard(Bill $bill)
    {
        $board = $bill->board();
        return $this->item($board, new BoardTransformer);
    }

    /**
     * Include category
     * @param  Bill   $bill
     * @return League\Fractal\ItemResource
     */
    public function includeCategory(Bill $bill)
    {
        $category = $bill->category();
        return $this->item($category, new UserTransformer);
    }

}
