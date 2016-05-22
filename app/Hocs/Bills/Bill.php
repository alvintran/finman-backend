<?php

namespace Nht\Hocs\Bills;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    public $fillable = ['payer_id', 'board_id', 'category_id', 'amount', 'note'];

    /**
     * Người chi tiền cho hóa đơn
     * @return Eloquent
     */
    public function payer()
    {
        return $this->belongsTo(\Nht\User::class, 'payer_id');
    }

    public function payee()
    {
        return $this->belongsToMany(\Nht\User::class);
    }

    /**
     * Board của hóa đơn
     * @return Eloquent
     */
    public function board()
    {
        return $this->belongsTo(\Nht\Hocs\Boards\Board::class);
    }

    /**
     * Category của hóa đơn
     * @return Eloquent
     */
    public function category()
    {
        return $this->belongsTo(\Nht\Categories\Category::class);
    }
}
