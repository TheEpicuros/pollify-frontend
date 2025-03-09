
import React from "react";
import { Download } from "lucide-react";

const PluginDownloadButton = () => {
  return (
    <div className="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4 rounded-md mb-6">
      <div className="flex items-start">
        <Download size={24} className="mr-3 mt-0.5 flex-shrink-0" />
        <div>
          <h3 className="font-medium">WordPress Plugin Information</h3>
          <p className="mt-2">
            This is a WordPress plugin designed to create and manage polls on WordPress websites.
            To use this plugin, you would need to install it on a WordPress site.
          </p>
          <p className="mt-2">
            Installation instructions can be found in the INSTALLATION.md file included with the plugin.
            The plugin provides shortcodes for displaying, creating, and browsing polls.
          </p>
          <ul className="list-disc list-inside mt-2 space-y-1">
            <li><code>[pollify id="123"]</code> - Display a specific poll</li>
            <li><code>[pollify_create]</code> - Show a poll creation form</li>
            <li><code>[pollify_browse]</code> - Display a list of available polls</li>
          </ul>
        </div>
      </div>
    </div>
  );
};

export default PluginDownloadButton;
