// studentApi.js
export async function checkStudentEnrollment(studentNumber) {
  try {
    const apiUrl = useRuntimeConfig().public.apiBackendUrl;
    
    const response = await fetch(`${apiUrl}/check-student`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        student_number: studentNumber,
      }),
    });
    
    if (!response.ok) {
      throw new Error('Failed to verify student enrollment');
    }
    
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error checking student enrollment:', error);
    throw error;
  }
}