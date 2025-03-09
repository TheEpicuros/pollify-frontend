
import React, { useState } from "react";
import { Download, Loader2 } from "lucide-react";
import JSZip from "jszip";
import { toast } from "sonner";

const PluginDownloadButton = () => {
  const [isLoading, setIsLoading] = useState(false);

  const handleDownload = async () => {
    setIsLoading(true);
    
    try {
      // Create a new JSZip instance
      const zip = new JSZip();
      
      // Define the files we want to include in the ZIP
      const filePaths = [
        "/wp-poll-plugin/poll-react.php",
        "/wp-poll-plugin/README.md",
        "/wp-poll-plugin/INSTALLATION.md",
        "/wp-poll-plugin/includes/database.php",
        "/wp-poll-plugin/includes/shortcodes.php",
        "/wp-poll-plugin/includes/ajax-handlers.php",
        "/wp-poll-plugin/includes/post-types.php",
        "/wp-poll-plugin/assets/css/pollify.css",
        "/wp-poll-plugin/assets/js/pollify.js"
      ];
      
      // Fetch each file and add it to the ZIP
      const fetchPromises = filePaths.map(async (path) => {
        try {
          const response = await fetch(path);
          if (!response.ok) {
            throw new Error(`Failed to fetch ${path}`);
          }
          
          const fileContent = await response.text();
          
          // Add the file to the zip with the correct structure
          // Remove the leading "/wp-poll-plugin/" to get the correct structure in the ZIP
          const zipPath = path.replace(/^\/wp-poll-plugin\//, '');
          zip.file(zipPath, fileContent);
          
          return true;
        } catch (error) {
          console.error(`Error fetching ${path}:`, error);
          return false;
        }
      });
      
      // Wait for all fetch operations to complete
      await Promise.all(fetchPromises);
      
      // Generate the ZIP file
      const content = await zip.generateAsync({ type: "blob" });
      
      // Create a download link for the ZIP
      const url = URL.createObjectURL(content);
      const downloadLink = document.createElement("a");
      downloadLink.href = url;
      downloadLink.download = "pollify-wordpress-plugin.zip";
      
      // Trigger the download
      document.body.appendChild(downloadLink);
      downloadLink.click();
      document.body.removeChild(downloadLink);
      
      // Clean up the object URL
      URL.revokeObjectURL(url);
      
      toast.success("Plugin ZIP file created successfully", {
        description: "You can now upload this ZIP file to WordPress."
      });
    } catch (error) {
      console.error("Error creating ZIP file:", error);
      toast.error("Failed to create ZIP file", { 
        description: "Please try again or download files individually."
      });
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <button
      onClick={handleDownload}
      disabled={isLoading}
      className="bg-primary text-primary-foreground font-medium py-2.5 px-4 rounded-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors flex items-center justify-center gap-2 w-full"
    >
      {isLoading ? (
        <>
          <Loader2 size={18} className="animate-spin" />
          Creating ZIP...
        </>
      ) : (
        <>
          <Download size={18} />
          Download WordPress Plugin (ZIP)
        </>
      )}
    </button>
  );
};

export default PluginDownloadButton;
