<?php

namespace App\Http\Controllers;

use App\Enums\ReadingPlanStatus;
use App\Http\Requests\ReadingPlan\StoreReadingPlanRequest;
use App\Http\Requests\ReadingPlan\UpdateReadingPlanRequest;
use App\Models\Book;
use App\Models\ReadingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReadingPlanController extends Controller
{
    /**
     * 読書計画一覧を表示する
     *
     * @param  Request  $requesst
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $currentStatus = $request->input('status');

        $query = ReadingPlan::where('user_id', $user->id)->with('book');

        if ($currentStatus) {
            $query->where('status', $currentStatus);
        }

        $readingPlans = $query->orderBy('target_date', 'asc')->get();

        return view('reading-plans.index', compact('readingPlans', 'currentStatus'));
    }

    /**
     * 読書計画の新規作成画面を表示する
     */
    public function create(): View
    {
        $books = Book::all();

        return view('reading-plans.create', compact('books'));
    }

    public function store(StoreReadingPlanRequest $request): RedirectResponse
    {
        $user = Auth::user();

        ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $request->validated('book_id'),
            'target_date' => $request->validated('target_date'),
            'status' => ReadingPlanStatus::NotStarted,
        ]);

        return redirect()->route('reading-plans.index')->with('success', '読書計画を作成しました');
    }

    /**
     * 読書計画の編集画面を表示する
     */
    public function edit(ReadingPlan $readingPlan): View
    {
        $this->authorize('update', $readingPlan);

        $books = Book::all();

        return view('reading-plans.edit', compact('readingPlan', 'books'));
    }

    /**
     * 読書計画を更新する
     */
    public function update(UpdateReadingPlanRequest $request, ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('update', $readingPlan);

        $readingPlan->update([
            'target_date' => $request->validated('target_date'),
        ]);

        return redirect()->route('reading-plans.index')->with('success', '読書計画を更新しました');
    }

    /**
     * 読書計画のステータスを完了状態にする
     */
    public function complete(ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('complete', $readingPlan);

        $readingPlan->update([
            'status' => ReadingPlanStatus::Completed,
            'completed_at' => now(),
        ]);

        return redirect()->route('reading-plans.index')->with('success', '読書計画のステータスを完了にしました');
    }

    /**
     * 読書計画を削除する
     */
    public function destroy(ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('delete', $readingPlan);

        $readingPlan->delete();

        return redirect()->route('reading-plans.index')->with('success', '読書計画を削除しました');
    }
}
