<?php

namespace App\Http\Controllers;

use App\Jobs\SubscribersNotification;
use App\Models\Post;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

class PublishController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $topic)
    {
        //validate incoming data
        $request->validate([
            'body' => ['required', 'json']
        ]);

        // create new record
        $new_post        = new Post();
        $new_post->topic = $topic;
        $new_post->body  = $request->body;
        $result          = $new_post->save();

        // get subscribers who subscribed to this topic
        $subscribers = Subscriber::where('topic', $new_post->topic)->get();

        // dispatch queue worker that makes request to all each subscriber
        foreach($subscribers as $subscriber){
            SubscribersNotification::dispatch($subscriber, $new_post);
        }
        
        // check if record was created successfully
        if($result){
            return response()->json($new_post, 200);
        }
        
        // if there was an error while saving
        return response()->json(['message' => 'We were unable to process your request. Please try again'], 500); 
    }

}
