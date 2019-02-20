<?php

namespace App\Models;

class User extends AbstractModel
{
    /**
     * 与模型关联的数据表.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * 数组中的属性会被隐藏.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * [Activity log] 变更记录中记录的字段.
     *
     * @var array
     */
    protected static $logAttributes = ['name', 'email', 'password'];
}
