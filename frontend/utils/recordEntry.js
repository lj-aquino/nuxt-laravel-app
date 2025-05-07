/**
 * Records a student's entry attempt
 * @param {Object} options - Entry recording options
 * @param {string} options.studentNumber - Student ID for the entry record
 * @param {boolean} options.hasId - Whether the student has an ID
 * @param {string} options.remarks - Additional remarks about the entry
 * @param {string} options.status - Status of verification (e.g., "verified")
 * @param {string} options.apiKey - API key for authentication
 * @returns {Promise<Object>} - Promise resolving to {success, message, data}
 */
export const recordEntry = async ({ studentNumber, hasId, remarks, status, apiKey }) => {
  console.log('Starting recordEntry function', { studentNumber, hasId, status });
  
  try {
    // Make API call to record the entry
    const apiUrl = 'https://sp-j16t.onrender.com/api/logs';
    console.log('Making API request to:', apiUrl);
    
    const requestBody = {
      student_number: studentNumber,
      has_id: hasId,
      remarks: remarks,
      status: status
    };
    
    console.log('API request body:', requestBody);
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
      console.error("Entry recording API error:", { status: response.status, data });
      return {
        success: false,
        message: data.message || `Failed to record entry (${response.status})`,
        data: null
      };
    }
    
    // Entry recording succeeded
    console.log('Student entry recorded successfully:', data.data);
    return {
      success: true,
      message: "Entry recorded successfully!",
      data: data.data
    };
    
  } catch (error) {
    console.error("Error in recordEntry:", error);
    console.error("Error stack:", error.stack);
    return {
      success: false,
      message: "An unexpected error occurred while recording entry.",
      data: null
    };
  }
};