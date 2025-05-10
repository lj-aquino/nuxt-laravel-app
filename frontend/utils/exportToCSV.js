/**
 * Exports data to a CSV file and triggers a download
 * 
 * @param {Array} headers - Array of column headers
 * @param {Array} data - Array of arrays containing row data
 * @param {String} filename - Name of the file to download (without extension)
 */
export function exportToCSV(headers, data, filename = 'export') {
    if (!process.client) return;
    
    try {
      // Combine headers and rows
      const csvContent = [
        headers.join(','),
        ...data.map(row => row.map(cell => {
          // Handle cells with commas by wrapping in quotes
          if (cell && cell.toString().includes(',')) {
            return `"${cell}"`;
          }
          return cell;
        }).join(','))
      ].join('\n');
      
      // Create a blob with the CSV content
      const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
      
      // Create a download link
      const link = document.createElement('a');
      
      // Create a URL for the blob
      const url = URL.createObjectURL(blob);
      
      // Set link attributes
      const fullFilename = `${filename}-${new Date().toISOString().slice(0, 10)}.csv`;
      link.href = url;
      link.setAttribute('download', fullFilename);
      link.style.visibility = 'hidden';
      
      // Add link to the document
      document.body.appendChild(link);
      
      // Click the link to start the download
      link.click();
      
      // Remove the link after download
      document.body.removeChild(link);
      
      // Release the URL object
      URL.revokeObjectURL(url);
      
      return true;
    } catch (error) {
      console.error('Error exporting CSV:', error);
      return false;
    }
  }