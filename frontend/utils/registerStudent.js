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
  try {
    updateMessage("Starting face registration process...");
    
    // First, get the face encoding using the updated utility
    const faceEncodingResult = await getFaceEncoding({
      webcam,
      updateMessage
    });
    
    // If face encoding failed
    if (!faceEncodingResult || !faceEncodingResult.success) {
      return { 
        success: false, 
        message: "Could not capture face encoding. Please try again."
      };
    }
    
    updateMessage("Face captured successfully. Registering student...");
    
    // Make API call to register the student with the face encoding
    const apiUrl = 'https://sp-j16t.onrender.com/api/face_encodings';
    
    const response = await fetch(apiUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-API-KEY': apiKey
      },
      body: JSON.stringify({
        student_number: studentId,
        student_name: studentName,
        encoding: faceEncodingResult.encoding
      })
    });
    
    const data = await response.json();
    
    if (!response.ok) {
      console.error("Registration API error:", data);
      return {
        success: false,
        message: data.message || `Registration failed (${response.status})`
      };
    }
    
    // Registration succeeded
    return {
      success: true,
      message: "Student registered successfully!",
      data: data.data
    };
    
  } catch (error) {
    console.error("Error in registerStudent:", error);
    return {
      success: false,
      message: "An unexpected error occurred during registration."
    };
  }
};