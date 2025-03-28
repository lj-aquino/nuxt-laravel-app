<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Log; // Import Laravel's Log facade

class FaceController extends Controller
{
    public function encode(Request $request)
    {
        // Log the start of the process
        Log::debug("Starting face encoding process...");

        // Validate the incoming request, expecting a base64 image string
        $request->validate([
            'image' => 'required|string',  // Base64 string
        ]);

        // Get the base64 image data
        $imageData = $request->input('image');
        Log::debug("Received base64 image string.");

        // Remove the data URL prefix (if exists)
        if (strpos($imageData, 'data:image/jpeg;base64,') === 0) {
            $imageData = substr($imageData, strlen('data:image/jpeg;base64,'));
            Log::debug("Removed base64 prefix.");
        }

        // Decode the base64 string to an image
        $image = base64_decode($imageData);
        Log::debug("Base64 image decoded.");

        // Save the image temporarily
        $imagePath = storage_path('app/temp_image.jpg');
        if (file_put_contents($imagePath, $image)) {
            Log::debug("Image saved to {$imagePath}");
        } else {
            Log::error("Failed to save the image to {$imagePath}");
        }

        // Prepare the command to run the Python script (face recognition)
        $process = new Process(['python3', storage_path('app/python-scripts/face_encoding.py'), $imagePath]);
        Log::debug("Running Python script...");

        // Run the process and capture output
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error("Python process failed: " . $process->getErrorOutput());
            throw new ProcessFailedException($process);
        }

        // Get the output (face encoding) from the Python script
        $encoding = $process->getOutput();
        Log::debug("Face encoding received from Python script.");

        // Log the face encoding output (you can limit this to the first few characters if needed)
        Log::debug("Face encoding output: " . substr($encoding, 0, 100));

        // Return the face encoding as JSON
        return response()->json(['encoding' => $encoding]);
    }
}
