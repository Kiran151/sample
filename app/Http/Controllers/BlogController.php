<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Blog::latest()->get();

        return response()->json(['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required',
                'content' => 'required',
            ]);

            $data['title'] = $request->title;
            $data['content'] = $request->content;
            $data['slug'] = strtolower(str_replace(' ', '_', $request->title));

            $image = $request->file('image');
            if ($image) {
                $fileName = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/blogs'), $fileName);
                $data['image'] = $fileName;
            }

            Blog::create($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

        return response()->json(['message' => 'success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {

        $data = Blog::where('slug', $slug)->get();
        if ($data) {
            return response()->json(['data' => $data]);
        } else {
            return response()->json(['error' => 'invalid']);

        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blog $blog)
    {
        $data = Blog::find($blog);
        return response()->json(['data' => $data]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = Blog::find($id);
        $data->title = $request->title;
        $data->content = $request->content;
        $data->slug = strtolower(str_replace(' ', '_', $request->title));


        $image = $request->image;
        if ($image) {
            if ($data->image) {
                unlink('uploads/blogs/' . $data->image);
            }

            $fileName = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/blogs'), $fileName);
            $data->image = $fileName;

        }

        $data->save();
        return response()->json(['message' => 'success']);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Blog::find($id);
        $image = $data->image;
        if ($image) {
            unlink('uploads/blogs/' . $image);
        }
        $data->delete();
        return response()->json(['message' => 'item deleted successfully']);
    }
}