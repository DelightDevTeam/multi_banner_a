<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class SiteLogoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sitelogos = SiteLogo::latest()->get();

        return view('admin.sitelogo.index', compact('sitelogos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.sitelogo.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required',
        ]);
        $image = $request->file('image');
        $ext = $image->getClientOriginalExtension();
        $filename = uniqid('logo').'.'.$ext;
        $image->move(public_path('assets/img/sitelogo/'), $filename);
        SiteLogo::create([
            'image' => $filename,
            'agent_id' => Auth::id()
        ]);

        return redirect(route('admin.sitelogo.index'))->with('success', 'Site Logo Added.');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SiteLogo $sitelogo)
    {
        return view('admin.sitelogo.edit', compact('sitelogo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SiteLogo $sitelogo)
    {
        if (! $sitelogo) {
            return redirect()->back()->with('error', 'Site Logo Not Found');
        }
        $request->validate([
            'image' => 'required',
        ]);

        File::delete(public_path('assets/img/sitelogo/'.$sitelogo->image));

        // image
        $image = $request->file('image');
        $ext = $image->getClientOriginalExtension();
        $filename = uniqid('sitelogo').'.'.$ext; // Generate a unique filename
        $image->move(public_path('assets/img/sitelogo/'), $filename); // Save the file

        $sitelogo->update([
            'image' => $filename,
        ]);

        return redirect(route('admin.sitelogo.index'))->with('success', 'Site Logo Updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SiteLogo $sitelogo)
    {
        if (! $sitelogo) {
            return redirect()->back()->with('error', 'SiteLogo Not Found');
        }
        //remove banner from localstorage
        File::delete(public_path('assets/img/sitelogo/'.$sitelogo->image));
        $sitelogo->delete();

        return redirect()->back()->with('success', 'SiteLogo Deleted.');
    }
}
