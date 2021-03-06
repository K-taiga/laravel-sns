<?php

namespace App\Http\Controllers;

use App\Article;
use App\Tag;
use App\Http\Requests\ArticleRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
class ArticleController extends Controller {
    public function __construct()
    {
        // リソースコントローラはaruthorizeResourceのmethodでpolicyとcontrollerをマッピングできる
        // ArticleControllerでArticlePolicyを使う
        $this->authorizeResource('App\Article','article');
    }

    public function index() {
        $articles = Article::all()->sortByDesc('created_at');
        return view('articles.index', compact('articles'));
    }

    public function create() {
        $allTagNames = Tag::all()->map(function ($tag) {
            return ['text' => $tag->name];
        });

        return view('articles.create', [
            'allTagNames' => $allTagNames,
        ]);
    }

    public function store(ArticleRequest $request, Article $article) {
        $article->fill($request->all());
        $article->user_id = $request->user()->id;
        $article->save();
        // collectionのクロージャ関数のeachを呼ぶ(foreach asのイメージ)
        // クロージャの外側の変数を呼び出せないためuseで定義する
        $request->tags->each(function ($tagName) use ($article) {
            // テーブルにタグがすでにあるかチェックしなければ作成しそのモデルを返す
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            // 記事とそのtagをattach
            $article->tags()->attach($tag);
        });
        return redirect()->route('articles.index');
    }

    public function edit(Article $article)
    {
        // vue側から受け取ったときと同じ形に整形して返す
        $tagNames = $article->tags->map(function ($tag) {
            return ['text' => $tag->name];
        });

        $allTagNames = Tag::all()->map(function ($tag) {
            return ['text' => $tag->name];
        });

        return view('article.edit',[
            'article' => $article,
            'tagNames' => $tagNames,
            'allTagNames' => $allTagNames,
        ]);
    }

    public function update(ArticleRequest $request, Article $article)
    {
        $article->fill($request->all())->save();
        $article->tags()->detach();
        $request->tags->each(function ($tagName) use ($article) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $article->tags()->attach($tag);
        });
        return redirect()->route('articles.index');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index');
    }

    public function show(Article $article)
    {
        return view('articles.show',['article' => $article]);
    }

    public function like(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);
        $article->likes()->attach($request->user()->id);

        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }

    public function unlike(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);

        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }
    
}
