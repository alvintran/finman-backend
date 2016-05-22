<?php namespace Nht\Http\Transformers;

use League\Fractal\TransformerAbstract;
use Nht\Hocs\Boards\Board;

class BoardTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'owner',
        'members',
        'bills'
    ];

    public function transform(Board $board)
    {
        return [
            'id'          => $board->id,
            'name'        => $board->name,
            'description' => $board->description,
            'owner_id'    => $board->owner_id,
            'created'     => date('d/m/Y', strtotime($board->created_at)),
            'updated'     => date('d/m/Y', strtotime($board->updated_at))
        ];
    }

    /**
     * Include owner
     * @param  Bill   $bill
     * @return League\Fractal\ItemResource
     */
    public function includeOwner(Board $board)
    {
        $owner = $board->owner();
        return $this->item($owner, new UserTransformer);
    }

    /**
     * Include members
     * @param  Bill   $bill
     * @return League\Fractal\CollectionResource
     */
    public function includeMembers(Board $board)
    {
        $members = $board->members();
        return $this->collection($members, new UserTransformer);
    }

    /**
     * Include bill
     * @param  Bill   $bill
     * @return League\Fractal\CollectionResource
     */
    public function includeBills(Board $board)
    {
        $bills = $board->bills();
        return $this->collection($bills, new BillTransformer);
    }
}
