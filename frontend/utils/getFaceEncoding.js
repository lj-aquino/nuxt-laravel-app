// Define standard messages to ensure consistency
export const MESSAGES = {
  SCANNING: "Scanning face, position your face in the square...",
  CAMERA_NOT_READY: "Camera not ready. Please try again.",
  ENCODING_FAILED: "Failed to encode face. Please try again.",
  ENCODING_SUCCESS: "Face encoding captured successfully!",
  ERROR_PROCESSING: "Error processing face. Please try again.",
  API_NOT_FOUND: "API endpoint not found. Please check server configuration.",
  SERVER_ERROR: "Server error processing the request. Check server logs.",
  GENERIC_SERVER_ERROR: "Server error: {status}. Please try again."
};

/**
 * Process face encoding from webcam and return the encoding array
 * @param {Object} options - Configuration options
 * @param {HTMLVideoElement} options.webcam - Reference to webcam element
 * @param {Function} options.updateMessage - Function to update processing message
 * @returns {Promise<Object|null>} - Promise resolving to encoding object or null on failure
 */
export const getFaceEncoding = async ({ webcam, updateMessage }) => {
  try {
    updateMessage(MESSAGES.SCANNING);
    
    // Create a canvas to capture the current webcam frame
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    
    // Make sure webcam is initialized and has dimensions
    if (!webcam || !webcam.videoWidth) {
      updateMessage(MESSAGES.CAMERA_NOT_READY);
      return null;
    }
    
    canvas.width = webcam.videoWidth;
    canvas.height = webcam.videoHeight;
    
    // Draw the current webcam frame on the canvas
    context.drawImage(webcam, 0, 0, canvas.width, canvas.height);
    
    // Convert the canvas to a base64 image
    const imageData = canvas.toDataURL('image/jpeg');
    
    // Use the local API URL
    const localApiUrl = 'http://localhost:8000/api';
    
    // Use FormData instead of JSON
    const formData = new FormData();
    formData.append('image', imageData);
    
    console.log(`Sending request to: ${localApiUrl}/encode`);
    
    const response = await fetch(`${localApiUrl}/encode`, {
      method: 'POST',
      body: formData,
    });
    
    console.log("Response status:", response.status);
    
    // For any non-OK response, try to capture detailed error information
    if (!response.ok) {
      let errorDetails = "";
      try {
        const errorText = await response.text();
        errorDetails = errorText.substring(0, 200);
        console.error("Server error details:", errorDetails);
      } catch (textError) {
        console.error("Could not read error response body");
      }
      
      if (response.status === 404) {
        updateMessage(MESSAGES.API_NOT_FOUND);
      } else if (response.status === 500) {
        updateMessage(MESSAGES.SERVER_ERROR);
        console.error("Server returned 500 error. Possible causes:", errorDetails);
      } else {
        updateMessage(MESSAGES.GENERIC_SERVER_ERROR.replace('{status}', response.status));
      }
      
      return null;
    }
    
    const data = await response.json();
    console.log("Response data:", data);
    
    // Extract the success value and encoding array from the response
    const match = data.encoding ? data.encoding.match(/'success':\s*(true|false)/i) : null;
    const success = match ? match[1].toLowerCase() === 'true' : false;
    
    if (!success) {
      updateMessage(MESSAGES.ENCODING_FAILED);
      return null;
    }
    
    // Extract the encoding array from the response string
    const encodingMatch = data.encoding.match(/\'encoding\':\s*\[(.*?)\]/);
    if (!encodingMatch || !encodingMatch[1]) {
      console.error('Could not extract encoding array from response');
      updateMessage(MESSAGES.ENCODING_FAILED);
      return null;
    }
    
    // Parse the encoding string into an array of numbers
    const encodingArrayString = encodingMatch[1];
    const encodingArray = encodingArrayString.split(',').map(num => parseFloat(num.trim()));
    
    if (!encodingArray.length) {
      console.error('Failed to parse encoding array');
      updateMessage(MESSAGES.ENCODING_FAILED);
      return null;
    }
    
    updateMessage(MESSAGES.ENCODING_SUCCESS);
    
    // Return the extracted face encoding array
    return {
      success: true,
      encoding: encodingArray
    };
    
  } catch (error) {
    console.error('Error processing face encoding:', error);
    updateMessage(MESSAGES.ERROR_PROCESSING);
    return null;
  }
};