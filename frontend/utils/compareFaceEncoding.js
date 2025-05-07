/**
 * Compare a face encoding with the stored encoding for a student
 * @param {string} studentNumber - The student's ID number
 * @param {Object} encodingData - Object containing the encoding array
 * @param {string} apiKey - API key for authentication
 * @returns {Promise<boolean>} - True if the face matches the stored encoding, false otherwise
 */
export async function compareFaceEncoding(studentNumber, encodingData, apiKey) {
  try {
    // Check for valid encoding data
    if (!encodingData || !encodingData.encoding || !Array.isArray(encodingData.encoding)) {
      console.error('Invalid encoding data:', encodingData);
      return false;
    }
    
    // Use the encoding array directly from the encodingData object
    const encodingArray = encodingData.encoding;
    
    if (!encodingArray.length) {
      console.error('Empty encoding array');
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