<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function homePage()
    {
        $closting = 'Eustacio Sasoin';
        $animals = ['Meowsalot', 'Barksalot', 'Seymourasses'];

        return view('homePage', ['name' => $closting, 'allAnimals' => $animals]);
    }

    public function aboutPage()
    {
        return view('singlePost');
    }
}
