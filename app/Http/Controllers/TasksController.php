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
    $tasks = Task::where('user_id', Auth::user()->id)
      ->orderBy('deadline', 'asc')
      ->get();
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
    // Eloquentモデル
    $task = new Task;
    $task->user_id = Auth::user()->id;
    $task->task = $request->task;
    $task->deadline = $request->deadline;
    $task->comment = $request->comment;
    $task->save();
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
    //   dd($task);
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
    $task = Task::find($id);
    $task->task   = $request->task;
    $task->deadline = $request->deadline;
    $task->comment = $request->comment;
    $task->save();
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
    $task = Task::find($id);
    $task->delete();
    return redirect()->route('tasks.index');
  }
}
