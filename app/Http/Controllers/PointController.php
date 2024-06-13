<?php

namespace App\Http\Controllers;
use App\Models\Points;
use Illuminate\Http\Request;

class PointController extends Controller
{
    public function __construct()
    {
        $this->point = new Points();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $points = $this->point->points();


        foreach ($points as $p) {
            $feature[] = [
                'type' => 'Feature',
                'geometry' => json_decode($p->geom),
                'properties' => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'description' => $p->description,
                    'image' => $p->image,
                    'created_at' => $p->created_at,
                    'updated_at' => $p->updated_at
                ]
                ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $feature,
        ]);

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
        //validate request
        $request->validate( [
            'name' => 'required',
            'geom' => 'required',
            'image'=> 'mimes:png,jpg,jpeg,gif|max:10000' //10MB
        ],
        [
            'name.required' => 'Name is required',
            'geom.required' => 'Location is required',
            'image.mimes' => 'image must be a file of type : png,jpg,jpeg,gif', //utk menentukan format file
            'image.max' => 'image must not exceed 10MB' //utk membatasi ukuran file
        ]);

        //create folder image
        if (!is_dir('storage/image')) { //tanda seru berarti sistem akan mengecek apakh ada folder image di dalam folder storege (isdir), jika folder image tidak tersedia maka ia akan membaut direktori (makedire) dengan permission code 0777
            mkdir('storage/image', 0777);
        }

        //upload image
        if ($request->hasFile('image')){
            $image = $request->file('image');
            $filename = time() . '_point' . $image->getClientOriginalExtension();
            $image->move('storage/image', $filename);
        } else {
            $filename = null;
        }
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'geom' => $request->geom,
            'image'=>$filename
        ];


        // Create Point
        if (!$this->point->create($data)){
            return redirect()-> back()->with('error', 'Failed to create point');
        }

        // Redirect to Map
        return redirect()->back()->with('success', 'Point created succesfullt');


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $point = $this->point->point($id); //menggunakan function di model point dengan nama points


        foreach ($point as $p) {
            $feature[] = [
                'type' => 'Feature',
                'geometry' => json_decode($p->geom),
                'properties' => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'description' => $p->description,
                    'image' => $p->image,
                    'created_at' => $p->created_at,
                    'updated_at' => $p->updated_at
                ]
                ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $feature,
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $point = $this->point->find($id);
        $data = [
            'title' => 'Edit Point',
            'point' => $point,
            'id' => $id
        ];

        return view('edit-point', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //validate request
        $request->validate( [
            'name' => 'required',
            'geom' => 'required',
            'image'=> 'mimes:png,jpg,jpeg,gif|max:10000' //10MB
        ],
        [
            'name.required' => 'Name is required',
            'geom.required' => 'Location is required',
            'image.mimes' => 'image must be a file of type : png,jpg,jpeg,gif', //utk menentukan format file
            'image.max' => 'image must not exceed 10MB' //utk membatasi ukuran file
        ]);

        //create folder image
        if (!is_dir('storage/image')) { //tanda seru berarti sistem akan mengecek apakh ada folder image di dalam folder storege (isdir), jika folder image tidak tersedia maka ia akan membaut direktori (makedire) dengan permission code 0777
            mkdir('storage/image', 0777);
        }

        //upload image
        if ($request->hasFile('image')){
            $image = $request->file('image');
            $filename = time() . '_point' . $image->getClientOriginalExtension();
            $image->move('storage/image', $filename);

            //delete image
            $image_old = $request->image_old;
            if ($image_old !=null) {
                unlink('storage/image/' .$image_old);
            }
        } else {
            $filename = $request->image_old; //utk penamaan image file agar sesuai dengan nama file yang lama
        }
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'geom' => $request->geom,
            'image'=>$filename
        ];


        // Update Point
        if (!$this->point->find($id)->update($data)){
            return redirect()-> back()->with('error', 'Failed to update point');
        }

        // Redirect to Map
        return redirect()->back()->with('success', 'Point update succesfullt');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //get image
        $image = $this->point->find($id)->image;

        //delete image
        if ($image !=null) {
            unlink('storage/image/' .$image);
        }

        //delete point
        if (!$this->point->destroy($id))
        {
        return redirect()->back()->with('error', 'Failed Delete Point');
    }

        //redirect to map
        return redirect()->back()->with('success', 'Point Deleted Succesfully');
    }

    public function table()
    {
        $points = $this->point->points();

        $data = [
            'title' => 'Table Point',
            'points' => $points,
        ];

        return view('table-point', $data);
    }
}
