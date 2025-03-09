
import React from "react";
import { Download } from "lucide-react";

const PluginDownloadButton = () => {
  const handleDownload = () => {
    // Create an array of file paths that we want to include in the download
    const filePaths = [
      "/wp-poll-plugin/poll-react.php",
      "/wp-poll-plugin/README.md",
      "/wp-poll-plugin/INSTALLATION.md",
      "/wp-poll-plugin/includes/database.php",
      "/wp-poll-plugin/includes/shortcodes.php",
      "/wp-poll-plugin/includes/ajax-handlers.php",
      "/wp-poll-plugin/includes/post-types.php"
    ];

    // Notify the user that the download is starting
    alert("The WordPress plugin files will begin downloading soon. After download, you'll need to ZIP them before installing in WordPress.");
    
    // Download each file in sequence
    filePaths.forEach((path, index) => {
      // Add a small delay between downloads to prevent browser blocking
      setTimeout(() => {
        // Create an anchor element to trigger the download
        const downloadLink = document.createElement("a");
        downloadLink.href = path;
        
        // Extract filename from path for the download attribute
        const fileName = path.split("/").pop();
        downloadLink.download = fileName || `plugin-file-${index}.php`;
        
        // Append to body, click to trigger download, then remove
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
      }, index * 300); // 300ms delay between downloads
    });
  };

  return (
    <button
      onClick={handleDownload}
      className="bg-primary text-primary-foreground font-medium py-2.5 px-4 rounded-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors flex items-center justify-center gap-2"
    >
      <Download size={18} />
      Download Plugin Files
    </button>
  );
};

export default PluginDownloadButton;
