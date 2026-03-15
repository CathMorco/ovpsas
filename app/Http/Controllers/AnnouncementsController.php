public function store(Request $request)
{
    // 1. This will stop the process if 'scheduled_date' is missing or empty
    $request->validate([
        'title' => 'required|string|max:255',
        'scheduled_date' => 'required|date',
    ]);

    $announcement = new Announcement();
    $announcement->user_id = auth()->id();
    $announcement->title = $request->title;
    $announcement->content = $request->content;

    // 2. Explicitly set the date from the request
    $announcement->scheduled_date = $request->scheduled_date;

    // 3. Handle arrays for office and category
    $announcement->office = $request->office ?? ['General'];
    $announcement->category = $request->category ?? ['Events'];

    $announcement->save();

    return back()->with('success', 'Event successfully scheduled for ' . $request->scheduled_date);
}
