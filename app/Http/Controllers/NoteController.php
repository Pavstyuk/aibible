<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{

    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $data = [
            'user_name' => '',
            'translation' => 'rbo',
            'book_num' => $request->cookie('book_num', 1),
            'chapter_num' => $request->cookie('chapter_num', 1),
            'seo_title' => '',
            'seo_description' => '',
            'theme' => $_COOKIE['apptheme'] ?? '' ?? '',
        ];

        return view('note.index', $data);
    }

    public function userNotes(Request $request, string $user_name)
    {
        $data = [
            'user_name' => mb_ucfirst($user_name),
            'translation' => 'rbo',
            'book_num' => $request->cookie('book_num', 1),
            'chapter_num' => $request->cookie('chapter_num', 1),
            'seo_title' => '',
            'seo_description' => '',
            'theme' => $_COOKIE['apptheme'] ?? '' ?? '',
        ];

        return view('note.index', $data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note, Request $request, string $user_name, string $note_slug)
    {
        $data = [
            'note_id' => 123,
            'user' => $user_name,
            'user_name' => mb_ucfirst($user_name),
            'note_slug' => $note_slug,
            'translation' => 'rbo',
            'book_num' => $request->cookie('book_num', 1),
            'chapter_num' => $request->cookie('chapter_num', 1),
            'seo_title' => '',
            'seo_description' => '',
            'theme' => $_COOKIE['apptheme'] ?? '' ?? '',
        ];

        return view('note.show', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, string $user_name)
    {

        $data = [
            'user' => $user_name,
            'user_name' => mb_ucfirst($user_name),
            'translation' => 'rbo',
            'book_num' => $request->cookie('book_num', 1),
            'chapter_num' => $request->cookie('chapter_num', 1),
            'seo_title' => '',
            'seo_description' => '',
            'theme' => $_COOKIE['apptheme'] ?? '' ?? '',
        ];
        
        return view('note.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note, Request $request, string $user_name, int $note_id)
    {
         $data = [
            'note_id' => $note_id,
            'note_slug' => $note_slug ?? 'test-note-one',
            'user' => $user_name,
            'user_name' => mb_ucfirst($user_name),
            'translation' => 'rbo',
            'book_num' => $request->cookie('book_num', 1),
            'chapter_num' => $request->cookie('chapter_num', 1),
            'seo_title' => '',
            'seo_description' => '',
            'theme' => $_COOKIE['apptheme'] ?? '' ?? '',
        ];
        
        return view('note.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        //
    }
}