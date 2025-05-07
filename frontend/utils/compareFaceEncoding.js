/**
 * Compare a face encoding with the stored encoding for a student
 * @param {string} studentNumber - The student's ID number
 * @param {string} encodingResponse - The raw encoding response from the /encode endpoint
 * @param {string} apiKey - API key for authentication
 * @returns {Promise<boolean>} - True if the face matches the stored encoding, false otherwise
 */
export async function compareFaceEncoding(studentNumber, encodingResponse, apiKey) {
  try {
    // Parse the encoding from the response
    // Example format: "Face encoding: {'success': True, 'encoding': [-0.112, 0.154, ...], 'debug_logs': []}"
    if (!encodingResponse || !encodingResponse.encoding) {
      console.error('Invalid encoding response:', encodingResponse);
      return false;
    }
    
    // Extract the encoding array from the response string
    const encodingMatch = encodingResponse.encoding.match(/\'encoding\':\s*\[(.*?)\]/);
    if (!encodingMatch || !encodingMatch[1]) {
      console.error('Could not extract encoding array from response');
      return false;
    }
    
    // Parse the encoding string into an array of numbers
    const encodingArrayString = encodingMatch[1];
    const encodingArray = encodingArrayString.split(',').map(num => parseFloat(num.trim()));
    
    if (!encodingArray.length) {
      console.error('Failed to parse encoding array');
      return false;
    }
    
    console.log(`Sending face comparison request for student ${studentNumber} with ${encodingArray.length} encoding values`);
    
    // Send the request to the recognition API
    const response = await fetch('https://sp-j16t.onrender.com/api/face_encodings/recognize', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-API-KEY': apiKey // Add the API key
      },
      body: JSON.stringify({
        student_number: studentNumber,
        scanned_face: encodingArray
      })
    });
    
    if (!response.ok) {
      console.error(`API error: ${response.status} ${response.statusText}`);
      return false;
    }
    
    const data = await response.json();
    console.log('Face comparison result:', data);
    
    // Return the match result
    return data.match === true;
    
  } catch (error) {
    console.error('Error comparing face encodings:', error);
    return false;
  }
}