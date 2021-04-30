<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $topic)
    {
        // validate data 
        $request->validate([
            'url' => ['required', 'url']
        ]);

        // create new subscriber record
        $new_sub        = new Subscriber();
        $new_sub->url   = $request->url;
        $new_sub->topic = $topic;
        $result         = $new_sub->save();

        // check if the new subscriber was created sccessfully and then return success response containing the newly created record
        if($result){
            return response()->json($new_sub, 200);
        }

        // if the record was not created successfuly then return the server error message
        return response()->json(['message' => 'We were unable to process your request. Please try again'], 500);
    }

}
