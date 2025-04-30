export function maskStudentNumber(input) {
    // Remove leading zeros
    const trimmed = input.replace(/^0+/, '');
  
    // Take first 5 digits
    const firstFive = trimmed.slice(0, 5);
  
    // Take last digit
    const lastDigit = trimmed.slice(-1);
  
    return `${firstFive}***${lastDigit}`;
  }
  