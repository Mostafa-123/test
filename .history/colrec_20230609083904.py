
public function getRecommendations2(Request $request)
{
    $targetUserId = Auth::guard('user-api')->id();

    // Execute the Python recommendation system code
    $scriptPath = '/path/to/your/python/script.py';
    $process = new Process(["python", $scriptPath, $targetUserId]);

    try {
        $process->mustRun();
        $recommendedHallsJson = $process->getOutput();

        // Return the recommended halls JSON as the API response
        return response()->json(json_decode($recommendedHallsJson));
    } catch (ProcessFailedException $exception) {
        throw new \RuntimeException($exception->getMessage());
    }
}
