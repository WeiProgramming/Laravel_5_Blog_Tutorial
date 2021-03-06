<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;
use App\Tag;
use Session;

class PostController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //create a variable and store all blog posts from database into it
        $posts = Post::orderBy('id','desc') -> paginate(5);//plural posts, eloquent grabs a select num with paginate with all() we get all posts  post
        //return a view and pass in the above variable
        return view('posts.index') -> withPosts($posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('posts.create')->withcategories($categories)->withtags($tags);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request); //die and dump, stops the application there and dump all the variable requests onto the screen
        //validate the data, if fail jumps back to create with errors
        $this->validate($request, array(
                'title'         => 'required|max:255',
                'slug'          => 'required|alpha_dash|min:5|max:255|unique:posts,slug',
                'category_id'   => 'required|integer',
                'body'          => 'required'
            ));
        //store in the datbase
        //called new post cause that's the name of the model we created
        $post = new Post;
        $post -> title = $request -> title;
        $post -> category_id = $request -> category_id;
        $post -> slug = $request -> slug;
        $post -> body = $request -> body;

        $post -> save();

        $post->tags()->sync($request->tags,false);

        Session::flash('success','The Blog Post was Successfully Saved!');//only let it exist for one request (key,value_), more permanent use put instead of flash

        //redirect to another page

        return redirect() -> route('posts.show',$post -> id); //this will dynaically have an id off post
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id); //helper method, eloquent to make it easy to play with the database
        return view('posts.show') -> withPost($post); //passing in the variable to post
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //find the post in the database and save it as a variable
            $post = Post::find($id);
        //return the view and pass in that information to fit into the appropriate places of the view
            $categories = Category::all();
            $cats = [];
            foreach ($categories as $category) {
                $cats[$category->id] = $category ->name;
            }

            $tags = Tag::all();
            $tags2 = [];

            foreach($tags as $tag){
                $tags2[$tag->id] = $tag-> name;
            }

            return view('posts.edit') -> withPost($post)->withcats($cats)->withtags($tags2);

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
        $post = Post::find($id);
        // Validate data
        if($request->input('slug') == $post->slug){
        $this->validate($request, array(
                'title' => 'required|max:255',
                'category_id' => 'required|integer',
                'body'  => 'required'
        ));
        }
        else{
        //svalidate the data
        $this->validate($request, array(
                'title' => 'required|max:255',
                'slug' => 'required|alpha_dash|min:5|max:255|unique:posts,slug', //unique from post table column called slug, last cause process is the longest
                'category_id' => 'required|integer',
                'body'  => 'required'
        ));
        }
        // save data to database


        $post -> title = $request -> input('title');
        $post -> slug = $request -> input('slug');
        $post -> category_id = $request -> input('category_id'); //taking in the name of the form and sends it to the database
        $post -> body = $request -> input('body');

        $post -> save(); //this actually commit changes and sends save request to db, will go to updated timestamp and update it itself


        //set flash data with success message

        Session::flash('success','Message successfully changed');
        //redirect with flash data to the posts.show
        return redirect()->route('posts.show', $post ->id); //this will dynaically have an id off post
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post =Post::find($id); //find the id to delete

        $post -> delete();

        Session::flash('success','The post was successfully deleted');

        return redirect()-> route('posts.index');
    }
}
