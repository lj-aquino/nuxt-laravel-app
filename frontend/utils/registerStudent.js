import { getFaceEncoding, MESSAGES } from './getFaceEncoding.js';

/**
 * Register a new student with face encoding
 * @param {Object} options - Registration options
 * @param {HTMLVideoElement} options.webcam - Reference to webcam element
 * @param {string} options.studentId - Student ID to register
 * @param {string} options.studentName - Student name to register
 * @param {Function} options.updateMessage - Function to update processing message
 * @param {string} options.apiKey - API key for authentication
 * @returns {Promise<Object>} - Promise resolving to registration result {success, message, data}
 */
export const registerStudent = async ({ webcam, studentId, studentName, updateMessage, apiKey }) => {
  console.log('Starting registerStudent function', { studentId, studentName });
  try {
    console.log('Webcam element available:', !!webcam);
    updateMessage("Starting face registration process...");
    
    // First, get the face encoding using the updated utility
    console.log('Calling getFaceEncoding...');
    const faceEncodingResult = await getFaceEncoding({
      webcam,
      updateMessage
    });
    console.log('getFaceEncoding result:', { success: faceEncodingResult?.success });
    
    // If face encoding failed
    if (!faceEncodingResult || !faceEncodingResult.success) {
      console.error('Face encoding failed:', faceEncodingResult);
      return { 
        success: false, 
        message: "Could not capture face encoding. Please try again."
      };
    }
    
    console.log('Face encoding captured successfully, length:', faceEncodingResult.encoding.length);
    updateMessage("Face captured successfully. Registering student...");
    
    // Make API call to register the student with the face encoding
    const apiUrl = 'https://sp-j16t.onrender.com/api/face_encodings';
    console.log('Making API request to:', apiUrl);
    
    const requestBody = {
      student_number: studentId,
      student_name: studentName,
      encoding: faceEncodingResult.encoding
    };
    console.log('API request body (partial):', {
      ...requestBody,
      encoding: `[Array of length ${requestBody.encoding.length}]`
    });
    
    console.log('API key provided:', !!apiKey);
    const response = await fetch(apiUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-API-KEY': apiKey
      },
      body: JSON.stringify(requestBody)
    });
    
    console.log('API response status:', response.status);
    const data = await response.json();
    console.log('API response data:', data);
    
    if (!response.ok) {
      console.error("Registration API error:", { status: response.status, data });
      return {
        success: false,
        message: data.message || `Registration failed (${response.status})`
      };
    }
    
    // Registration succeeded
    console.log('Student registration successful:', data.data);
    return {
      success: true,
      message: "Student registered successfully!",
      data: data.data
    };
    
  } catch (error) {
    console.error("Error in registerStudent:", error);
    console.error("Error stack:", error.stack);
    return {
      success: false,
      message: "An unexpected error occurred during registration."
    };
  }
};