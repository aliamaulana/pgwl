<?php

namespace App\Http\Controllers;
use App\Models\Polylines;
use Illuminate\Http\Request;

class PolylineController extends Controller
{
    public function __construct()
    {
        $this->polyline = new Polylines();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $polylines = $this->polyline->polylines();


        foreach ($polylines as $p) {
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
            $filename = time() . '_polyline' . $image->getClientOriginalExtension();
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


        // Create Polyline
        if (!$this->polyline->create($data)){
            return redirect()-> back()->with('error', 'Failed to create polyline');
        }

        // Redirect to Map
        return redirect()->back()->with('success', 'Polyline created succesfullt');


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $polyline = $this->polyline->polyline($id); //menggunakan function di model polyline dengan nama polylines


        foreach ($polyline as $p) {
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
        $polyline = $this->polyline->find($id);
        $data = [
            'title' => 'Edit Polyline',
            'polyline' => $polyline,
            'id' => $id
        ];

        return view('edit-polyline', $data);
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
            $filename = time() . '_polyline' . $image->getClientOriginalExtension();
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


        // Update Polyline
        if (!$this->polyline->find($id)->update($data)){
            return redirect()-> back()->with('error', 'Failed to update polyline');
        }

        // Redirect to Map
        return redirect()->back()->with('success', 'Polyline update succesfullt');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //get image
        $image = $this->polyline->find($id)->image;

        //delete image
        if ($image !=null) {
            unlink('storage/image/' .$image);
        }

        //delete polyline
        if (!$this->polyline->destroy($id))
        {
        return redirect()->back()->with('error', 'Failed Delete Polyline');
    }

        //redirect to map
        return redirect()->back()->with('success', 'Polyline Deleted Succesfully');
    }

    public function table()
    {
        $polylines = $this->polyline->polylines();

        $data = [
            'title' => 'Table Polyline',
            'polylines' => $polylines,
        ];

        return view('table-polyline', $data);
    }
}
