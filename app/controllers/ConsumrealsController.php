<?php

class ConsumrealsController extends \BaseController {

	/**
	 * Display a listing of consumreals
	 *
	 * @return Response
	 */
	public function index()
	{
		$consumreals = Consumreal::all();

		return View::make('consumreals.index', compact('consumreals'));
	}

	/**
	 * Show the form for creating a new consumreal
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('consumreals.create');
	}

	/**
	 * Store a newly created consumreal in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Consumreal::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Consumreal::create($data);

		return Redirect::route('consumreals.index');
	}

	/**
	 * Display the specified consumreal.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$consumreal = Consumreal::findOrFail($id);

		return View::make('consumreals.show', compact('consumreal'));
	}

	/**
	 * Show the form for editing the specified consumreal.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$consumreal = Consumreal::find($id);

		return View::make('consumreals.edit', compact('consumreal'));
	}

	/**
	 * Update the specified consumreal in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$consumreal = Consumreal::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Consumreal::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$consumreal->update($data);

		return Redirect::route('consumreals.index');
	}

	/**
	 * Remove the specified consumreal from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Consumreal::destroy($id);

		return Redirect::route('consumreals.index');
	}

}
