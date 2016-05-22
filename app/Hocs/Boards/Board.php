<?php

namespace Nht\Hocs\Boards;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    public $fillable = ['name', 'description', 'owner_id'];

    /**
     * Người tạo board
     * @return Eloquent
     */
    public function owner()
    {
        return $this->belongsTo(\Nht\User::class, 'owner_id');
    }

    /**
     * Danh sách thành viên trong board
     * @return Eloquent
     */
    public function members()
    {
        return $this->belongsToMany(\Nht\User::class);
    }

    /**
     * Danh sách hóa đơn
     * @return Eloquent
     */
    public function bills()
    {
        return $this->hasMany(\Nht\Hocs\Bills\Bill::class);
    }
}
