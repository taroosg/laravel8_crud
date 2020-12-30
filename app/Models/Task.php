<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Task extends Model
{
  use HasFactory;

  // アプリケーション側でcreateなどできない値を記述
  protected $guarded = [
    'id',
    // 'task',
    'created_at',
    'updated_at',
  ];

  // protected $fillable = [
  //   'user_id',
  //   'task',
  //   'deadline',
  //   'comment',
  // ];

  public static function getAllOrderByDeadline()
  {
    $tasks = self::orderBy('deadline', 'asc')
      ->get();
    return $tasks;
  }

  public static function getMyAllOrderByDeadline()
  {
    $tasks = self::where('user_id', Auth::user()->id)
      ->orderBy('deadline', 'asc')
      ->get();
    return $tasks;
  }
}
