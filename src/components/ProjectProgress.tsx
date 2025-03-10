
import React from "react";
import { CustomProgress } from "@/components/ui/custom-progress";

const ProjectProgress = () => {
  const features = [
    { name: "Core Plugin Structure", progress: 100, description: "Basic plugin files and structure" },
    { name: "Poll Creation Interface", progress: 85, description: "Interface for creating polls" },
    { name: "Poll Display/Voting", progress: 80, description: "Viewing and voting on polls" },
    { name: "Poll Results Visualization", progress: 90, description: "Progress bars and statistics" },
    { name: "Admin Dashboard", progress: 100, description: "Complete admin interface" },
    { name: "Settings Panel", progress: 100, description: "Configuration options" },
    { name: "Analytics", progress: 95, description: "Data visualization and exports" },
    { name: "Poll Types", progress: 60, description: "Different poll types implementation" },
    { name: "Social Media Integration", progress: 50, description: "Sharing on social platforms" },
    { name: "User Voting Tracking", progress: 90, description: "Track who voted on what" },
    { name: "Rating System", progress: 40, description: "Up/down rating for polls" },
    { name: "Comments System", progress: 35, description: "Comment functionality" },
    { name: "Shortcode Support", progress: 85, description: "Embedding polls anywhere" }
  ];

  return (
    <div className="space-y-8 p-6">
      <div className="text-center mb-8">
        <h2 className="text-2xl font-bold mb-2">WordPress Poll Plugin Progress</h2>
        <p className="text-muted-foreground">
          Current development status of features
        </p>
      </div>

      <div className="grid gap-6 md:grid-cols-2">
        {features.map((feature, index) => (
          <div key={index} className="glass-card p-4 rounded-lg">
            <CustomProgress
              value={feature.progress}
              label={feature.name}
              showValue={true}
              size="md"
              animated={feature.progress < 100}
              className="mb-1"
            />
            <p className="text-xs text-muted-foreground mt-1">{feature.description}</p>
          </div>
        ))}
      </div>

      <div className="mt-8 pt-4 border-t">
        <h3 className="font-medium mb-3">Overall Progress</h3>
        <CustomProgress
          value={78}
          size="lg"
          animated={true}
          fillClassName="bg-gradient-to-r from-blue-500 to-purple-500"
        />
        <p className="text-sm text-center mt-2 text-muted-foreground">
          Approximately 78% complete
        </p>
      </div>
    </div>
  );
};

export default ProjectProgress;
