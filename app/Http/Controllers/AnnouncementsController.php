public function store(Request $request)
{
    // 1. Enforce validation for ALL required fields based on your test cases
    $request->validate([
        'title'    => 'required|string|max:255',
        'content  => 'required|string',
        'office'   => 'required|array|min:1',
        'category' => 'required|array|min:1',
    ], [
        // Custom error messages with the warning sign you requested
        'content.required'  => '⚠️ The content field is required to publish.',
        'office.required'   => '⚠️ Please select at least one target office.',
        'category.required' => '⚠️ Please select at least one category.',
    ]);

    $announcement = new Announcement();
    $announcement->user_id = auth()->id();
    $announcement->title = $request->title;
    $announcement->content = $request->content;

    // Handle arrays for office and category (Fallback removed since validation enforces it)
    $announcement->office = $request->office;
    $announcement->category = $request->category;

    // Optional: Only save scheduled_date if it actually exists in your form
    if ($request->has('scheduled_date')) {
        $announcement->scheduled_date = $request->scheduled_date;
    }

    $announcement->save();

    return back()->with('success', 'Announcement successfully published!');
}