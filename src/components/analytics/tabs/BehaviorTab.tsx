
import React from "react";
import { Card, CardContent } from "@/components/ui/card";

const BehaviorTab: React.FC = () => {
  return (
    <div className="mt-6">
      <h3 className="text-lg font-medium mb-4">User Flow Analysis</h3>
      <Card>
        <CardContent className="p-6">
          <div className="space-y-4">
            <div className="p-4 border rounded-lg">
              <h4 className="font-medium mb-2">Popular Poll Categories</h4>
              <div className="grid grid-cols-2 gap-4">
                <div className="flex items-center justify-between">
                  <span>Entertainment</span>
                  <span className="font-medium">32%</span>
                </div>
                <div className="flex items-center justify-between">
                  <span>Politics</span>
                  <span className="font-medium">24%</span>
                </div>
                <div className="flex items-center justify-between">
                  <span>Technology</span>
                  <span className="font-medium">18%</span>
                </div>
                <div className="flex items-center justify-between">
                  <span>Sports</span>
                  <span className="font-medium">15%</span>
                </div>
                <div className="flex items-center justify-between">
                  <span>Food</span>
                  <span className="font-medium">7%</span>
                </div>
                <div className="flex items-center justify-between">
                  <span>Other</span>
                  <span className="font-medium">4%</span>
                </div>
              </div>
            </div>

            <div className="p-4 border rounded-lg">
              <h4 className="font-medium mb-2">User Retention</h4>
              <div className="space-y-2">
                <div className="flex items-center justify-between">
                  <span>First Day Retention</span>
                  <span className="font-medium">78%</span>
                </div>
                <div className="flex items-center justify-between">
                  <span>7-Day Retention</span>
                  <span className="font-medium">52%</span>
                </div>
                <div className="flex items-center justify-between">
                  <span>30-Day Retention</span>
                  <span className="font-medium">34%</span>
                </div>
              </div>
            </div>

            <div className="p-4 border rounded-lg">
              <h4 className="font-medium mb-2">Platform Feature Usage</h4>
              <div className="space-y-2">
                <div className="flex items-center justify-between">
                  <span>Voting</span>
                  <span className="font-medium">100%</span>
                </div>
                <div className="flex items-center justify-between">
                  <span>Commenting</span>
                  <span className="font-medium">42%</span>
                </div>
                <div className="flex items-center justify-between">
                  <span>Poll Creation</span>
                  <span className="font-medium">28%</span>
                </div>
                <div className="flex items-center justify-between">
                  <span>Social Sharing</span>
                  <span className="font-medium">18%</span>
                </div>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

export default BehaviorTab;
