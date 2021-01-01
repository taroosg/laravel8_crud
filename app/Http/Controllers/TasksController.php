<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

use Validator;
use Auth;

class TasksController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth']);
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    // return view('tasks.index');
    // 締切早い順にソートしてデータを取得
    $tasks = Task::getMyAllOrderByDeadline();
    return view('tasks.index', [
      'tasks' => $tasks
    ]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('tasks.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    // バリデーション
    $validator = Validator::make($request->all(), [
      'task' => 'required|max:255',
      'deadline' => 'required',
    ]);
    // バリデーション:エラー
    if ($validator->fails()) {
      return redirect()
        ->route('tasks.create')
        ->withInput()
        ->withErrors($validator);
    }
    $data = $request->merge(['user_id' => Auth::user()->id])->all();
    // create()は最初から用意されている関数
    // 戻り値は挿入されたレコードの情報
    $result = Task::create($data);
    // ルーティング「tasks.index」にリクエスト送信（一覧ページに移動）
    return redirect()->route('tasks.index');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $task = Task::find($id);
    return view('tasks.show', ['task' => $task]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $task = Task::find($id);
    return view('tasks.edit', ['task' => $task]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    //バリデーション
    $validator = Validator::make($request->all(), [
      'task' => 'required|max:255',
      'deadline' => 'required',
    ]);
    //バリデーション:エラー
    if ($validator->fails()) {
      return redirect()
        ->route('tasks.edit', $id)
        ->withInput()
        ->withErrors($validator);
    }
    //データ更新処理
    // updateは更新する情報がなくても更新が走る（updated_atが更新される）
    $task = Task::find($id)->update($request->all());
    // fill()save()は更新する情報がない場合は更新が走らない（updated_atが更新されない）
    // $task = Task::find($id)->fill($request->all())->save();
    return redirect()->route('tasks.index');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $result = Task::find($id)->delete();
    return redirect()->route('tasks.index');
  }
}
