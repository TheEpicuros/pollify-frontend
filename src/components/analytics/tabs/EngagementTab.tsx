
import React from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { ResponsiveContainer, BarChart, Bar, XAxis, YAxis, Tooltip, Legend, LineChart, Line } from "recharts";
import { userEngagement, timeOfDayData, COLORS } from "../data/mockData";
import { Clock, Users, Activity } from "lucide-react";

const EngagementTab: React.FC = () => {
  return (
    <div className="space-y-6">
      <div className="mt-6">
        <h3 className="text-lg font-medium mb-4">Weekly User Activity</h3>
        <div className="h-80">
          <ResponsiveContainer width="100%" height="100%">
            <BarChart data={userEngagement} margin={{ top: 20, right: 30, left: 20, bottom: 5 }}>
              <XAxis dataKey="day" />
              <YAxis />
              <Tooltip />
              <Legend />
              <Bar dataKey="new_users" name="New Users" fill="#8884d8" />
              <Bar dataKey="returning" name="Returning Users" fill="#82ca9d" />
              <Bar dataKey="votes" name="Total Votes" fill="#ffc658" />
            </BarChart>
          </ResponsiveContainer>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium">Time of Day Activity</CardTitle>
            <CardDescription>When users are most active</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-60">
              <ResponsiveContainer width="100%" height="100%">
                <LineChart data={timeOfDayData} margin={{ top: 5, right: 30, left: 20, bottom: 5 }}>
                  <XAxis dataKey="time" />
                  <YAxis />
                  <Tooltip />
                  <Line type="monotone" dataKey="votes" stroke="#8884d8" activeDot={{ r: 8 }} />
                </LineChart>
              </ResponsiveContainer>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium">Key Metrics</CardTitle>
            <CardDescription>User activity summary</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <div className="flex items-center">
                  <Users className="h-5 w-5 text-primary mr-2" />
                  <span>Active Users</span>
                </div>
                <span className="font-bold">1,245</span>
              </div>
              <div className="flex items-center justify-between">
                <div className="flex items-center">
                  <Activity className="h-5 w-5 text-primary mr-2" />
                  <span>Total Interactions</span>
                </div>
                <span className="font-bold">12,587</span>
              </div>
              <div className="flex items-center justify-between">
                <div className="flex items-center">
                  <Users className="h-5 w-5 text-primary mr-2" />
                  <span>Average User Age</span>
                </div>
                <span className="font-bold">32 days</span>
              </div>
              <div className="flex items-center justify-between">
                <div className="flex items-center">
                  <Clock className="h-5 w-5 text-primary mr-2" />
                  <span>Avg. Session Duration</span>
                </div>
                <span className="font-bold">3m 42s</span>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default EngagementTab;
