// studentApi.js
export async function checkStudentEnrollment(studentNumber) {
  try {
    const config = useRuntimeConfig();
    const apiBackendUrl = config.public.apiBackendUrl;
    
    if (!apiBackendUrl) {
      throw new Error('Backend API URL is not configured');
    }
    
    console.log('Making request to:', `${apiBackendUrl}/check-student`);
    
    const response = await fetch(`${apiBackendUrl}/check-student`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        student_number: studentNumber,
      }),
    });
    
    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      console.error('API Error:', errorData);
      throw new Error(`API returned ${response.status}: ${errorData.message || 'Failed to verify student enrollment'}`);
    }
    
    const data = await response.json();
    console.log('Enrollment API response:', data);
    return data;
  } catch (error) {
    console.error('Error checking student enrollment:', error);
    throw error;
  }
}