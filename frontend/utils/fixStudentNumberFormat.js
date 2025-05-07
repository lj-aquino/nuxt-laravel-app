/**
 * Formats student numbers to ensure they follow the correct pattern (YYYY-#####)
 * Examples:
 * - 0202109320 → 2021-09320 (removes leading zeros first)
 * - 2019-09924 → 2019-09924 (already correctly formatted)
 * - 201909924 → 2019-09924
 * @param {string} studentNumber - The student number to format
 * @returns {string} The properly formatted student number
 */
export const fixStudentNumberFormat = (studentNumber) => {
  if (!studentNumber) return '';
  
  // Convert to string if it's not already
  let strNumber = String(studentNumber);
  
  // If already formatted correctly with a dash, just return it
  if (strNumber.length === 10 && strNumber[4] === '-') {
    return strNumber;
  }
  
  // Remove leading zeros (handles cases like 0202109320 -> 202109320)
  strNumber = strNumber.replace(/^0+/, '');
  
  // For 9-digit numbers without dash, insert dash after 4th character
  if (strNumber.length === 9 && !strNumber.includes('-')) {
    return `${strNumber.substring(0, 4)}-${strNumber.substring(4)}`;
  }
  
  // Return the processed string if no specific formatting rules match
  return strNumber;
};

export default fixStudentNumberFormat;